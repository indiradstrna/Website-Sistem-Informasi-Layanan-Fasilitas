<?php
$connOld = new mysqli('localhost', 'root', '', 'dbsedia10_old');
$connNew = new mysqli('localhost', 'root', '', 'biotrop');

if ($connOld->connect_error || $connNew->connect_error) {
    die("Connection failed");
}

$locOld = '023010199111213000KP'; // ATK LAMA in old DB
$locNew = 2; // ATK LAMA in new DB

// 1. Import Items (t_brg)
$stmt = $connOld->prepare("SELECT * FROM t_brg WHERE kd_lokasi = ?");
$stmt->bind_param("s", $locOld);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$addedItems = 0;
foreach ($items as $item) {
    // Insert into inv_items
    $code = $item['kd_brg'];
    $name = $item['ur_brg'] ?? '-';
    $unit = $item['satuan'] ?? '-';
    
    $stmtIns = $connNew->prepare("INSERT INTO inv_items (item_code, name, location_id, unit, stock) VALUES (?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    $stmtIns->bind_param("ssis", $code, $name, $locNew, $unit);
    $stmtIns->execute();
    $addedItems++;
}

// 2. Import Transactions (t_sediam and t_sediak)
// We need to map item_code + location_id to inv_items.id
$itemMap = [];
$res = $connNew->query("SELECT id, item_code FROM inv_items WHERE location_id = $locNew");
while ($row = $res->fetch_assoc()) {
    $itemMap[$row['item_code']] = $row['id'];
}

$addedMasuk = 0;
// Persediaan Masuk (t_sediam)
$stmt = $connOld->prepare("SELECT * FROM t_sediam WHERE kd_lokasi = ?");
$stmt->bind_param("s", $locOld);
$stmt->execute();
$masuk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($masuk as $t) {
    $item_code = $t['kd_brg'];
    if (!isset($itemMap[$item_code])) continue;
    $item_id = $itemMap[$item_code];
    $type = 'in';
    $subtype = 'Pembelian';
    $qty = $t['kuantitas'] ?? 0;
    $unit_price = $t['rph_sat'] ?? 0;
    $total_price = $qty * $unit_price;
    $doc_number = $t['nodok'];
    $doc_date = $t['tgldok'];
    $book_date = $t['tglbuku'];
    $notes = $t['keterangan'] ?? '';
    $transaction_date = $t['tgldok'];
    $reference_doc = $t['nobukti'] ?? '';
    
    // avoid duplicates? We can check by doc_number and item_id
    $check = $connNew->prepare("SELECT id FROM inv_transactions WHERE doc_number=? AND item_id=? AND type=?");
    $check->bind_param("sis", $doc_number, $item_id, $type);
    $check->execute();
    if ($check->get_result()->num_rows > 0) continue;
    
    $ins = $connNew->prepare("INSERT INTO inv_transactions (type, transaction_subtype, item_id, quantity, unit_price, total_price, doc_number, doc_date, book_date, notes, transaction_date, reference_doc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->bind_param("ssiidsssssss", $type, $subtype, $item_id, $qty, $unit_price, $total_price, $doc_number, $doc_date, $book_date, $notes, $transaction_date, $reference_doc);
    if($ins->execute()) $addedMasuk++;
}

$addedKeluar = 0;
// Persediaan Keluar (t_sediak)
$stmt = $connOld->prepare("SELECT * FROM t_sediak WHERE kd_lokasi = ?");
$stmt->bind_param("s", $locOld);
$stmt->execute();
$keluar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($keluar as $t) {
    $item_code = $t['kd_brg'];
    if (!isset($itemMap[$item_code])) continue;
    $item_id = $itemMap[$item_code];
    $type = 'out';
    $subtype = 'Pemakaian';
    $qty = $t['kuantitas'] ?? 0;
    $unit_price = $t['rph_sat'] ?? 0;
    $total_price = $qty * $unit_price; 
    if (isset($t['rphaset']) && $t['rphaset'] > 0) {
        $total_price = $t['rphaset'];
    }
    $doc_number = $t['nodok'];
    $doc_date = $t['tgldok'];
    $book_date = $t['tglbuku'];
    $notes = $t['keterangan'] ?? '';
    $transaction_date = $t['tgldok'];
    $reference_doc = $t['nobukti'] ?? '';
    
    $check = $connNew->prepare("SELECT id FROM inv_transactions WHERE doc_number=? AND item_id=? AND type=?");
    $check->bind_param("sis", $doc_number, $item_id, $type);
    $check->execute();
    if ($check->get_result()->num_rows > 0) continue;
    
    $ins = $connNew->prepare("INSERT INTO inv_transactions (type, transaction_subtype, item_id, quantity, unit_price, total_price, doc_number, doc_date, book_date, notes, transaction_date, reference_doc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->bind_param("ssiidsssssss", $type, $subtype, $item_id, $qty, $unit_price, $total_price, $doc_number, $doc_date, $book_date, $notes, $transaction_date, $reference_doc);
    if($ins->execute()) $addedKeluar++;
}
echo "Import finished. Items: $addedItems, Masuk: $addedMasuk, Keluar: $addedKeluar\n";
