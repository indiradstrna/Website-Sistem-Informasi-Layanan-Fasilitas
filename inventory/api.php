<?php
session_start();
require_once __DIR__ . '/../config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Handle Login
if ($action === 'login') {
    $empId = $_POST['employee_id'] ?? '';
    $pass  = $_POST['password'] ?? '';
    
    if (!$empId || !$pass) jsonResponse(false, 'Harap isi ID Karyawan dan Password.');
    
    $stmt = $conn->prepare("
        SELECT u.id, u.full_name, u.password, u.role, u.employee_id 
        FROM users u 
        INNER JOIN employees e ON u.employee_id = e.id 
        WHERE e.nip_nik = ? AND u.role LIKE '%warehouse_admin%'
    ");
    $stmt->bind_param("s", $empId);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        if ($pass === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['employee_id'] = $row['employee_id'];
            $_SESSION['username'] = $empId;
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];
            jsonResponse(true, 'Login berhasil!');
        } else {
            jsonResponse(false, 'Password salah.');
        }
    } else {
        jsonResponse(false, 'Akun tidak ditemukan atau bukan Admin Gudang.');
    }
}

// Cek autentikasi untuk aksi selain login
if (!isset($_SESSION['user_id']) || strpos($_SESSION['role'], 'warehouse_admin') === false) {
    jsonResponse(false, 'Unauthorized. Silakan login sebagai Admin Gudang.', null, 401);
}
$userId = $_SESSION['user_id'];
$userName = $_SESSION['full_name'];

function ensureInvItemsImageColumn(mysqli $conn): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    try {
        $colRes = $conn->query("SHOW COLUMNS FROM inv_items LIKE 'image'");
        if ($colRes && $colRes->num_rows === 0) {
            $conn->query("ALTER TABLE inv_items ADD COLUMN image VARCHAR(255) DEFAULT NULL");
        }
    } catch (\Throwable $e) {
        // Abaikan jika tidak diizinkan mengubah struktur tabel
    }
}

function saveUploadedInvItemImage(array $file, string $prefix = 'item'): string {
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err !== UPLOAD_ERR_OK) {
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
            jsonResponse(false, 'Ukuran file foto terlalu besar (melebihi batas server).');
        }
        jsonResponse(false, 'Gagal mengunggah file foto (kode error: ' . intval($err) . ').');
    }

    $maxBytes = 5 * 1024 * 1024; // 5 MB
    if (($file['size'] ?? 0) > $maxBytes) {
        jsonResponse(false, 'Ukuran foto maksimal 5 MB.');
    }

    $origName = (string)($file['name'] ?? '');
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowedExts, true)) {
        jsonResponse(false, 'Format foto tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.');
    }

    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false) {
        jsonResponse(false, 'File yang diunggah bukan gambar yang valid.');
    }

    $uploadDir = __DIR__ . '/../uploads/inv_items/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        jsonResponse(false, 'Gagal membuat folder penyimpanan foto barang.');
    }

    $safePrefix = preg_replace('/[^a-zA-Z0-9_-]/', '_', $prefix);
    $newFileName = $safePrefix . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        jsonResponse(false, 'Gagal menyimpan file foto ke server.');
    }

    return 'uploads/inv_items/' . $newFileName;
}

function deleteOldInvItemImage(?string $relPath): void {
    $relPath = trim((string)$relPath);
    if ($relPath === '' || preg_match('#^https?://#i', $relPath)) return;

    $uploadDirReal = realpath(__DIR__ . '/../uploads/inv_items');
    if (!$uploadDirReal) return;

    $cleanRel = ltrim(str_replace('\\', '/', $relPath), '/');
    $candidate = realpath(__DIR__ . '/../' . $cleanRel);
    if ($candidate && strpos($candidate, $uploadDirReal) === 0 && is_file($candidate)) {
        @unlink($candidate);
    }
}

switch ($action) {
    case 'get_dashboard_stats':
        $locId = $_GET['location_id'] ?? $_POST['location_id'] ?? 'all';
        $whereItems = $locId !== 'all' ? "WHERE location_id = " . intval($locId) : "";
        $whereItemsAnd = $locId !== 'all' ? "WHERE location_id = " . intval($locId) . " AND" : "WHERE";
        $whereTx = $locId !== 'all' ? " AND i.location_id = " . intval($locId) : "";
        
        $totalItems = $conn->query("SELECT COUNT(*) as c FROM inv_items $whereItems")->fetch_assoc()['c'];
        $lowStock = $conn->query("SELECT COUNT(*) as c FROM inv_items $whereItemsAnd stock <= 0")->fetch_assoc()['c'];
        $todayIn = $conn->query("SELECT COUNT(t.id) as c FROM inv_transactions t JOIN inv_items i ON t.item_id = i.id WHERE t.type='in' AND DATE(t.created_at) = CURDATE() $whereTx")->fetch_assoc()['c'];
        $todayOut = $conn->query("SELECT COUNT(t.id) as c FROM inv_transactions t JOIN inv_items i ON t.item_id = i.id WHERE t.type='out' AND DATE(t.created_at) = CURDATE() $whereTx")->fetch_assoc()['c'];
        
        jsonResponse(true, '', [
            'totalItems' => $totalItems,
            'lowStock' => $lowStock,
            'todayIn' => $todayIn,
            'todayOut' => $todayOut
        ]);
        break;

    case 'get_items':
        ensureInvItemsImageColumn($conn);
        $locId = $_GET['location_id'] ?? $_POST['location_id'] ?? 'all';
        $where = $locId !== 'all' ? "WHERE i.location_id = " . intval($locId) : "";
        $res = $conn->query("SELECT i.id, i.item_code, i.name, i.stock, i.unit, i.image, 0 as min_stock, c.name as category_name 
                             FROM inv_items i 
                             LEFT JOIN inv_categories c ON i.category_id = c.id 
                             $where
                             ORDER BY i.name ASC");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'search_items':
        $q = $_GET['q'] ?? '';
        $locId = $_GET['location_id'] ?? $_POST['location_id'] ?? 'all';
        $like = "%$q%";
        
        $whereSql = "(i.item_code LIKE ? OR i.name LIKE ?)";
        if ($locId !== 'all') {
            $whereSql .= " AND i.location_id = " . intval($locId);
        }
        
        $stmt = $conn->prepare("
            SELECT i.id, i.item_code, i.name, i.stock, i.unit, i.location_id,
                   COALESCE((SELECT unit_price FROM inv_transactions WHERE item_id = i.id AND type='in' ORDER BY id DESC LIMIT 1), 0) as last_price
            FROM inv_items i 
            WHERE $whereSql
            LIMIT 10
        ");
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_transactions':
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $locId = $_GET['location_id'] ?? $_POST['location_id'] ?? 'all';
        
        $sql = "SELECT t.id, t.type, t.transaction_subtype, t.quantity, t.reference_id, t.note, t.created_at, i.name as item_name, i.item_code, u.full_name as user_name 
                FROM inv_transactions t 
                JOIN inv_items i ON t.item_id = i.id 
                LEFT JOIN users u ON t.user_id = u.id 
                WHERE 1=1";
                
        $params = [];
        $types = "";
        
        if ($locId !== 'all') {
            $stmtLoc = $conn->prepare("SELECT code FROM inv_locations WHERE id = ?");
            $stmtLoc->bind_param("i", $locId);
            $stmtLoc->execute();
            $resLoc = $stmtLoc->get_result()->fetch_assoc();
            if ($resLoc && $resLoc['code']) {
                $sql .= " AND t.doc_number LIKE ?";
                $params[] = $resLoc['code'] . '%';
                $types .= "s";
            }
            $sql .= " AND i.location_id = ?";
            $params[] = intval($locId);
            $types .= "i";
        }
        
        $sql .= " ORDER BY t.created_at DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'generate_doc_number':
        $type = $_POST['type'] ?? 'in';
        $locId = $_POST['location_id'] ?? null;
        
        $prefix = 'DEFAULT';
        if ($locId) {
            $stmtLoc = $conn->prepare("SELECT code FROM inv_locations WHERE id = ?");
            $stmtLoc->bind_param("i", $locId);
            $stmtLoc->execute();
            $resLoc = $stmtLoc->get_result()->fetch_assoc();
            if ($resLoc) {
                $prefix = $resLoc['code'];
            }
        }

        $year = date('Y');
        $suffix = $type === 'in' ? 'M' : ($type === 'opname' ? 'O' : 'K');
        
        $likePattern = $prefix . $year . '%' . $suffix;
        $stmt = $conn->prepare("SELECT doc_number FROM inv_transactions WHERE doc_number LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("s", $likePattern);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        
        $nextNum = 1;
        if ($res && preg_match('/' . $year . '(\d{5})' . $suffix . '$/', $res['doc_number'], $matches)) {
            $nextNum = (int)$matches[1] + 1;
        }
        
        $newDocNum = $prefix . $year . str_pad($nextNum, 5, '0', STR_PAD_LEFT) . $suffix;
        jsonResponse(true, 'Success', $newDocNum);
        break;

    case 'get_documents':
        $type = $_POST['type'] ?? 'in';
        $subtype = $_POST['subtype'] ?? '';
        $locId = $_POST['location_id'] ?? $_GET['location_id'] ?? 'all';
        
        $sql = "SELECT t.doc_number, MAX(t.doc_date) as doc_date, MAX(t.reference_doc) as reference_doc, SUM(t.total_price) as total_price, COUNT(t.id) as item_count
                FROM inv_transactions t
                JOIN inv_items i ON t.item_id = i.id
                WHERE 1=1";
                
        $params = [];
        $types = "";
        
        if ($locId !== 'all') {
            // First, get the location code to use as prefix
            $locCode = '';
            $stmtLoc = $conn->prepare("SELECT code FROM inv_locations WHERE id = ?");
            $stmtLoc->bind_param("i", $locId);
            $stmtLoc->execute();
            $resLoc = $stmtLoc->get_result()->fetch_assoc();
            if ($resLoc && $resLoc['code']) {
                $locCode = $resLoc['code'] . '%';
                $sql .= " AND t.doc_number LIKE ?";
                $params[] = $locCode;
                $types .= "s";
            }
            // Still filter by location_id for the items to ensure consistency
            $sql .= " AND i.location_id = ?";
            $params[] = intval($locId);
            $types .= "i";
        }
        
        if ($type === 'koreksi') {
            $sql .= " AND t.transaction_subtype = ?";
            $params[] = 'Koreksi';
            $types .= "s";
        } elseif ($type === 'opname') {
            // Opname disimpan sebagai IN/OUT dengan subtype 'Hasil Opname Fisik'
            $sql .= " AND t.transaction_subtype = ?";
            $params[] = 'Hasil Opname Fisik';
            $types .= "s";
        } else {
            $sql .= " AND t.type = ?";
            $params[] = $type;
            $types .= "s";
            
            if ($subtype) {
                $sql .= " AND t.transaction_subtype = ?";
                $params[] = $subtype;
                $types .= "s";
            }
        }
        
        $sql .= " GROUP BY t.doc_number ORDER BY MAX(t.created_at) DESC";
        
        $stmt = $conn->prepare($sql);
        if (count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'save_document':
        $type = $_POST['type'] ?? '';
        $subtype = $_POST['transaction_subtype'] ?? null;
        $docNum = $_POST['doc_number'] ?? '';
        $docDate = $_POST['doc_date'] ?? date('Y-m-d');
        $bookDate = $_POST['book_date'] ?? date('Y-m-d');
        $refDoc = $_POST['reference_doc'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $itemsJson = $_POST['items'] ?? '[]';
        
        $items = json_decode($itemsJson, true);
        if (!$items || count($items) === 0) {
            jsonResponse(false, 'Tidak ada barang dalam dokumen.');
        }

        $conn->begin_transaction();
        try {
            if ($type === 'opname' || $type === 'koreksi') {
                // -------------------------------------------------------
                // OPNAME FISIK & KOREKSI: Simpan sebagai transaksi IN/OUT berdasarkan
                // selisih (delta) antara stok real/fisik nyata vs stok buku.
                // Ini memastikan Laporan Rincian Persediaan otomatis sinkron.
                // -------------------------------------------------------
                $defaultSubtype = $type === 'opname' ? 'Hasil Opname Fisik' : 'Koreksi';
                $effectiveSubtype = $subtype ?: $defaultSubtype;

                $stmtInsert = $conn->prepare("INSERT INTO inv_transactions 
                    (item_id, type, transaction_subtype, doc_number, doc_date, book_date, transaction_date, reference_doc, notes, quantity, unit_price, total_price, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)");
                $stmtUpdateStock = $conn->prepare("UPDATE inv_items SET stock = ? WHERE id = ?");

                foreach ($items as $item) {
                    $itemId     = (int)$item['id'];
                    $stokFisik  = (int)$item['qty'];   // input user = stok real/fisik nyata
                    if ($stokFisik < 0) {
                        throw new Exception("Stok real tidak boleh kurang dari 0.");
                    }
                    $price      = (float)$item['price'];

                    // Ambil stok buku saat ini (dengan lock agar aman concurrent)
                    $stmtCek = $conn->prepare("SELECT stock, name FROM inv_items WHERE id = ? FOR UPDATE");
                    $stmtCek->bind_param("i", $itemId);
                    $stmtCek->execute();
                    $rowItem = $stmtCek->get_result()->fetch_assoc();
                    $stmtCek->close();
                    if (!$rowItem) throw new Exception("Barang tidak ditemukan (id={$itemId}).");

                    $stokBuku = (int)$rowItem['stock'];
                    $delta    = $stokFisik - $stokBuku;  // negatif = selisih kurang, positif = selisih lebih

                    // Tentukan tipe dan qty transaksi berdasarkan delta
                    if ($delta < 0) {
                        $txTypeSave = 'out';
                        $qtySave    = abs($delta);
                    } elseif ($delta > 0) {
                        $txTypeSave = 'in';
                        $qtySave    = $delta;
                    } else {
                        // Tidak ada selisih – catat dengan qty=0 agar dokumen tetap muncul di list
                        $txTypeSave = 'out';
                        $qtySave    = 0;
                    }
                    $totalSave = $qtySave * $price;

                    // Insert transaksi selisih
                    $stmtInsert->bind_param("isssssssiddi",
                        $itemId, $txTypeSave, $effectiveSubtype, $docNum, $docDate, $bookDate, $refDoc, $notes,
                        $qtySave, $price, $totalSave, $userId
                    );
                    $stmtInsert->execute();

                    // Set stok master ke nilai real/fisik nyata
                    $stmtUpdateStock->bind_param("ii", $stokFisik, $itemId);
                    $stmtUpdateStock->execute();
                }

            } else {
                // -------------------------------------------------------
                // TRANSAKSI NORMAL (in / out)
                // -------------------------------------------------------
                if ($type === 'out') {
                    foreach ($items as $item) {
                        $itemId = (int)$item['id'];
                        $qty    = (int)$item['qty'];
                        $stmtCek = $conn->prepare("SELECT stock, name FROM inv_items WHERE id = ? FOR UPDATE");
                        $stmtCek->bind_param("i", $itemId);
                        $stmtCek->execute();
                        $row = $stmtCek->get_result()->fetch_assoc();
                        if (!$row) throw new Exception("Barang {$item['name']} tidak ditemukan.");
                        if ($row['stock'] < $qty) throw new Exception("Stok tidak mencukupi untuk {$item['name']}. Sisa: {$row['stock']}.");
                    }
                }

                $stmtInsert = $conn->prepare("INSERT INTO inv_transactions 
                    (item_id, type, transaction_subtype, doc_number, doc_date, book_date, transaction_date, reference_doc, notes, quantity, unit_price, total_price, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)");
                $stmtUpdate = $conn->prepare(
                    "UPDATE inv_items SET stock = stock " . ($type === 'in' ? '+' : '-') . " ? WHERE id = ?"
                );

                foreach ($items as $item) {
                    $itemId = (int)$item['id'];
                    $qty    = (int)$item['qty'];
                    $price  = (float)$item['price'];
                    $total  = $qty * $price;

                    $stmtInsert->bind_param("isssssssiddi",
                        $itemId, $type, $subtype, $docNum, $docDate, $bookDate, $refDoc, $notes, $qty, $price, $total, $userId
                    );
                    $stmtInsert->execute();

                    $stmtUpdate->bind_param("ii", $qty, $itemId);
                    $stmtUpdate->execute();
                }
            }

            $conn->commit();
            jsonResponse(true, 'Dokumen berhasil disimpan dengan ' . count($items) . ' barang.');
        } catch (Exception $e) {
            $conn->rollback();
            jsonResponse(false, $e->getMessage());
        }
        break;

    case 'get_bmn_bid':
        $res = $conn->query("SELECT kd_bidbrg as code, kd_bid as short_code, ur_bid as name FROM inv_bmn_bid ORDER BY kd_bidbrg ASC");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    case 'get_bmn_kel':
        $bidCode = $_GET['parent'] ?? '';
        $stmt = $conn->prepare("SELECT kd_kelbrg as code, kd_kel as short_code, ur_kel as name FROM inv_bmn_kel WHERE kd_kelbrg LIKE ? ORDER BY kd_kelbrg ASC");
        $like = $bidCode . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        $stmt->close();
        break;

    case 'get_bmn_skel':
        $kelCode = $_GET['parent'] ?? '';
        $stmt = $conn->prepare("SELECT kd_skelbrg as code, kd_skel as short_code, ur_skel as name FROM inv_bmn_skel WHERE kd_skelbrg LIKE ? ORDER BY kd_skelbrg ASC");
        $like = $kelCode . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        $stmt->close();
        break;

    case 'get_bmn_sskel':
        $skelCode = $_GET['parent'] ?? '';
        $stmt = $conn->prepare("SELECT kd_brg as code, kd_sskel as short_code, ur_sskel as name FROM inv_bmn_sskel WHERE kd_brg LIKE ? ORDER BY kd_brg ASC");
        $like = $skelCode . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        $stmt->close();
        break;

    case 'add_master_item':
        $sskelCode = $_POST['sskel_code'] ?? '';
        $name = $_POST['name'] ?? '';
        $unit = $_POST['unit'] ?? '';
        $locId = $_POST['location_id'] ?? '';

        if (!$sskelCode || !$name) jsonResponse(false, 'Kode SSKEL dan Nama Barang harus diisi.');
        if (!$locId || $locId === 'all') jsonResponse(false, 'UAKPB harus dipilih spesifik untuk menambah barang.');
        if (strlen($sskelCode) !== 10) jsonResponse(false, 'Format kode SSKEL tidak valid.');

        // Generate next sequence
        $stmt = $conn->prepare("SELECT MAX(item_code) as max_code FROM inv_items WHERE item_code LIKE ?");
        $like = $sskelCode . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $nextSeq = 1;
        if ($row && $row['max_code']) {
            $lastSeqStr = substr($row['max_code'], -6);
            $nextSeq = intval($lastSeqStr) + 1;
        }
        $newItemCode = $sskelCode . str_pad($nextSeq, 6, '0', STR_PAD_LEFT);

        // Get category_id based on kd_kelbrg (first 5 chars of sskelCode)
        $kelCode = substr($sskelCode, 0, 5);
        $catStmt = $conn->prepare("SELECT id FROM inv_categories WHERE code = ?");
        $catStmt->bind_param("s", $kelCode);
        $catStmt->execute();
        $catRow = $catStmt->get_result()->fetch_assoc();
        $categoryId = $catRow ? $catRow['id'] : null;
        $catStmt->close();

        ensureInvItemsImageColumn($conn);
        $imagePath = null;
        if (!empty($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $imagePath = saveUploadedInvItemImage($_FILES['image'], 'item_' . $newItemCode);
        }

        $insStmt = $conn->prepare("INSERT INTO inv_items (item_code, name, category_id, unit, stock, location_id, image) VALUES (?, ?, ?, ?, 0, ?, ?)");
        $insStmt->bind_param("ssisis", $newItemCode, $name, $categoryId, $unit, $locId, $imagePath);
        if ($insStmt->execute()) {
            jsonResponse(true, 'Barang baru berhasil ditambahkan dengan kode: ' . $newItemCode, ['image' => $imagePath]);
        } else {
            if ($imagePath) deleteOldInvItemImage($imagePath);
            jsonResponse(false, 'Gagal menambahkan barang: ' . $conn->error);
        }
        $insStmt->close();
        break;

    case 'update_item_image':
        ensureInvItemsImageColumn($conn);
        $itemId = intval($_POST['item_id'] ?? 0);
        $removeImage = ($_POST['remove_image'] ?? '0') === '1';

        if ($itemId <= 0) {
            jsonResponse(false, 'ID barang tidak valid.');
        }

        $stmtCurr = $conn->prepare("SELECT id, item_code, image FROM inv_items WHERE id = ? LIMIT 1");
        $stmtCurr->bind_param("i", $itemId);
        $stmtCurr->execute();
        $currItem = $stmtCurr->get_result()->fetch_assoc();
        $stmtCurr->close();

        if (!$currItem) {
            jsonResponse(false, 'Data barang tidak ditemukan.');
        }

        if ($removeImage) {
            $stmtUpd = $conn->prepare("UPDATE inv_items SET image = NULL WHERE id = ?");
            $stmtUpd->bind_param("i", $itemId);
            if ($stmtUpd->execute()) {
                $stmtUpd->close();
                deleteOldInvItemImage($currItem['image'] ?? null);
                jsonResponse(true, 'Foto barang berhasil dihapus.', ['image' => null]);
            }
            $err = $conn->error;
            $stmtUpd->close();
            jsonResponse(false, 'Gagal menghapus foto barang: ' . $err);
        }

        if (empty($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            jsonResponse(false, 'Silakan pilih file foto terlebih dahulu.');
        }

        $newImagePath = saveUploadedInvItemImage($_FILES['image'], 'item_' . ($currItem['item_code'] ?: $itemId));
        $stmtUpd = $conn->prepare("UPDATE inv_items SET image = ? WHERE id = ?");
        $stmtUpd->bind_param("si", $newImagePath, $itemId);
        if ($stmtUpd->execute()) {
            $stmtUpd->close();
            deleteOldInvItemImage($currItem['image'] ?? null);
            jsonResponse(true, 'Foto barang berhasil disimpan.', ['image' => $newImagePath]);
        } else {
            $err = $conn->error;
            $stmtUpd->close();
            deleteOldInvItemImage($newImagePath);
            jsonResponse(false, 'Gagal memperbarui foto barang: ' . $err);
        }
        break;

    case 'auto_fetch_item_image':
        ensureInvItemsImageColumn($conn);
        $itemId = intval($_POST['item_id'] ?? 0);
        $customQuery = trim((string)($_POST['query'] ?? ''));

        if ($itemId <= 0) {
            jsonResponse(false, 'ID barang tidak valid.');
        }

        $stmtCurr = $conn->prepare("SELECT id, item_code, name, image FROM inv_items WHERE id = ? LIMIT 1");
        $stmtCurr->bind_param("i", $itemId);
        $stmtCurr->execute();
        $currItem = $stmtCurr->get_result()->fetch_assoc();
        $stmtCurr->close();

        if (!$currItem) {
            jsonResponse(false, 'Data barang tidak ditemukan.');
        }

        $searchQuery = $customQuery !== ''
            ? $customQuery
            : (trim(preg_replace('/\s+/', ' ', (string)$currItem['name'])) . ' foto produk');

        $searchUrl = 'https://www.bing.com/images/async?q=' . rawurlencode($searchQuery) . '&first=0&count=8&mmasync=1';
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

        $ch = curl_init($searchUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_HTTPHEADER => ['Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7'],
        ]);
        $html = (string)curl_exec($ch);
        curl_close($ch);

        if ($html === '') {
            jsonResponse(false, 'Gagal menghubungi pencarian gambar internet.');
        }

        $candidates = [];
        if (preg_match_all('#https://tse\d+\.mm\.bing\.net/th/id/(?:OIP|OIF)\.[^"\'\s<>]+#i', $html, $mOip)) {
            foreach ($mOip[0] as $rawU) {
                $u = html_entity_decode($rawU, ENT_QUOTES);
                $u = preg_replace('/w=\d+&h=\d+/', 'w=360&h=360', $u);
                if (!in_array($u, $candidates, true)) $candidates[] = $u;
                if (count($candidates) >= 4) break;
            }
        }
        if (preg_match_all('/murl&quot;:&quot;(https?:\/\/[^&"]+)&quot;/i', $html, $mMurl)) {
            foreach ($mMurl[1] as $rawU) {
                $u = html_entity_decode($rawU, ENT_QUOTES);
                if (!in_array($u, $candidates, true)) $candidates[] = $u;
                if (count($candidates) >= 7) break;
            }
        }

        if (empty($candidates)) {
            jsonResponse(false, 'Gambar tidak ditemukan untuk kata kunci tersebut.');
        }

        $savedBin = null;
        $savedExt = null;
        foreach ($candidates as $candUrl) {
            $chImg = curl_init($candUrl);
            curl_setopt_array($chImg, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => $ua,
            ]);
            $bin = curl_exec($chImg);
            $code = (int)curl_getinfo($chImg, CURLINFO_HTTP_CODE);
            curl_close($chImg);

            if ($code !== 200 || !is_string($bin) || strlen($bin) < 1800 || strlen($bin) > 2500000) {
                continue;
            }
            $info = @getimagesizefromstring($bin);
            if ($info === false) continue;

            $mimeMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
            ];
            $ext = $mimeMap[$info['mime'] ?? ''] ?? null;
            if ($ext) {
                $savedBin = $bin;
                $savedExt = $ext;
                break;
            }
        }

        if (!$savedBin || !$savedExt) {
            jsonResponse(false, 'Tidak berhasil mengunduh gambar yang valid.');
        }

        $uploadDir = __DIR__ . '/../uploads/inv_items/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            jsonResponse(false, 'Gagal membuat folder penyimpanan foto barang.');
        }

        $newFileName = 'item_' . $itemId . '_' . time() . '.' . $savedExt;
        if (file_put_contents($uploadDir . $newFileName, $savedBin) === false) {
            jsonResponse(false, 'Gagal menyimpan file gambar.');
        }

        $newImagePath = 'uploads/inv_items/' . $newFileName;
        $stmtUpd = $conn->prepare("UPDATE inv_items SET image = ? WHERE id = ?");
        $stmtUpd->bind_param("si", $newImagePath, $itemId);
        if ($stmtUpd->execute()) {
            $stmtUpd->close();
            deleteOldInvItemImage($currItem['image'] ?? null);
            jsonResponse(true, 'Foto otomatis berhasil diambil & disimpan.', ['image' => $newImagePath]);
        } else {
            $err = $conn->error;
            $stmtUpd->close();
            deleteOldInvItemImage($newImagePath);
            jsonResponse(false, 'Gagal memperbarui database foto: ' . $err);
        }
        break;

    case 'get_uakpb':
        $res = $conn->query("SELECT id, code, name FROM inv_locations ORDER BY id ASC");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    default:
        jsonResponse(false, 'Action tidak dikenali.');
}
