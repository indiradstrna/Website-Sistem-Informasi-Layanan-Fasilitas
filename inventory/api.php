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
        WHERE e.nip_nik = ? AND u.role = 'warehouse_admin'
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
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warehouse_admin') {
    jsonResponse(false, 'Unauthorized. Silakan login sebagai Admin Gudang.', null, 401);
}
$userId = $_SESSION['user_id'];
$userName = $_SESSION['full_name'];

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
        $locId = $_GET['location_id'] ?? $_POST['location_id'] ?? 'all';
        $where = $locId !== 'all' ? "WHERE i.location_id = " . intval($locId) : "";
        $res = $conn->query("SELECT i.id, i.item_code, i.name, i.stock, i.unit, 0 as min_stock, c.name as category_name 
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
        $suffix = $type === 'in' ? 'M' : 'K';
        
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
            // Verify stock first if outbound
            if ($type === 'out') {
                foreach ($items as $item) {
                    $itemId = (int)$item['id'];
                    $qty = (int)$item['qty'];
                    $stmtCek = $conn->prepare("SELECT stock, name FROM inv_items WHERE id = ? FOR UPDATE");
                    $stmtCek->bind_param("i", $itemId);
                    $stmtCek->execute();
                    $row = $stmtCek->get_result()->fetch_assoc();
                    
                    if (!$row) throw new Exception("Barang {$item['name']} tidak ditemukan.");
                    if ($row['stock'] < $qty) throw new Exception("Stok tidak mencukupi untuk {$item['name']}. Sisa: {$row['stock']}.");
                }
            }

            $stmtInsert = $conn->prepare("INSERT INTO inv_transactions 
                (item_id, type, transaction_subtype, doc_number, doc_date, book_date, reference_doc, notes, quantity, unit_price, total_price, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
            $stmtUpdate = $conn->prepare("UPDATE inv_items SET stock = stock " . ($type === 'in' ? '+' : '-') . " ? WHERE id = ?");

            foreach ($items as $item) {
                $itemId = (int)$item['id'];
                $qty = (int)$item['qty'];
                $price = (float)$item['price'];
                $total = $qty * $price;
                
                // insert transaction
                $stmtInsert->bind_param("isssssssiddi", 
                    $itemId, $type, $subtype, $docNum, $docDate, $bookDate, $refDoc, $notes, $qty, $price, $total, $userId
                );
                $stmtInsert->execute();
                
                // update stock
                $stmtUpdate->bind_param("ii", $qty, $itemId);
                $stmtUpdate->execute();
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

        $insStmt = $conn->prepare("INSERT INTO inv_items (item_code, name, category_id, unit, stock, location_id) VALUES (?, ?, ?, ?, 0, ?)");
        $insStmt->bind_param("ssisi", $newItemCode, $name, $categoryId, $unit, $locId);
        if ($insStmt->execute()) {
            jsonResponse(true, 'Barang baru berhasil ditambahkan dengan kode: ' . $newItemCode);
        } else {
            jsonResponse(false, 'Gagal menambahkan barang: ' . $conn->error);
        }
        $insStmt->close();
        break;

    case 'get_uakpb':
        $res = $conn->query("SELECT id, code, name FROM inv_locations ORDER BY id ASC");
        echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
        break;

    default:
        jsonResponse(false, 'Action tidak dikenali.');
}
