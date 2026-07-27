<?php
$connOld = new mysqli('localhost', 'root', '', 'dbsedia10_old');
$connNew = new mysqli('localhost', 'root', '', 'biotrop');

if ($connOld->connect_error || $connNew->connect_error) {
    die("Connection failed");
}

$locOld = '138010199693186000KP'; // KEMDIKDASMEN
$locNew = 8; // KEMDIKDASMEN in new DB
$docNum = '138010199693186000KP202600001M';

// First, delete the previous incorrect import from `biotrop`
$connNew->query("DELETE FROM inv_transactions WHERE doc_number = '$docNum'");

// 1. Import Items from t_brg if they don't exist
$stmt = $connOld->prepare("SELECT * FROM t_brg WHERE kd_lokasi = ?");
$stmt->bind_param("s", $locOld);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($items as $item) {
    // Insert into inv_items
    $code = $item['kd_brg'];
    $name = $item['ur_brg'] ?? '-';
    $unit = $item['satuan'] ?? '-';
    
    $stmtIns = $connNew->prepare("INSERT INTO inv_items (item_code, name, location_id, unit, stock) VALUES (?, ?, ?, ?, 0) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    $stmtIns->bind_param("ssis", $code, $name, $locNew, $unit);
    $stmtIns->execute();
}

// Map item_code to id
$itemMap = [];
$res = $connNew->query("SELECT id, item_code FROM inv_items WHERE location_id = $locNew");
while ($row = $res->fetch_assoc()) {
    $itemMap[$row['item_code']] = $row['id'];
}

$addedMasuk = 0;
// Import the actual 2026 document
$stmt = $connOld->prepare("SELECT * FROM t_sediam WHERE nodok = ?");
$stmt->bind_param("s", $docNum);
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
    $doc_date = $t['tgldok'];
    $book_date = $t['tglbuku'];
    $notes = $t['keterangan'] ?? '';
    $transaction_date = $doc_date;
    $reference_doc = $t['nobukti'] ?? '';
    
    $ins = $connNew->prepare("INSERT INTO inv_transactions (type, transaction_subtype, item_id, quantity, unit_price, total_price, doc_number, doc_date, book_date, notes, transaction_date, reference_doc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ins->bind_param("ssiidsssssss", $type, $subtype, $item_id, $qty, $unit_price, $total_price, $docNum, $doc_date, $book_date, $notes, $transaction_date, $reference_doc);
    if($ins->execute()) $addedMasuk++;
}

// Recalculate stock
$connNew->query("UPDATE inv_items i JOIN (SELECT item_id, SUM(CASE WHEN type='in' THEN quantity WHEN type='out' THEN -quantity ELSE 0 END) as calculated_stock FROM inv_transactions GROUP BY item_id) t ON i.id = t.item_id SET i.stock = t.calculated_stock WHERE i.location_id = $locNew");

echo "Import finished. Inserted $addedMasuk items for document $docNum.\n";
