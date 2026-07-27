<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$type = $_GET['type'] ?? 'in';
$subtype = $_GET['subtype'] ?? '';
$periodType = $_GET['period_type'] ?? 'month';
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$typeStr = $type === 'in' ? 'MASUK' : ($type === 'out' ? 'KELUAR' : '(KOREKSI)');
$subtypeStr = $subtype && $type !== 'koreksi' ? "(".strtoupper($subtype).")" : "";

$periodStr = "";
if ($periodType === 'month') {
    $months = [
        '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET',
        '04' => 'APRIL', '05' => 'MEI', '06' => 'JUNI',
        '07' => 'JULI', '08' => 'AGUSTUS', '09' => 'SEPTEMBER',
        '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER'
    ];
    // If year selected from the year dropdown, but period_type is month, we should use current year unless year dropdown isn't disabled.
    // In our UI, if month is selected, the year dropdown is hidden but we can just use the current year.
    $periodStr = "BULAN " . ($months[$month] ?? '') . " " . date('Y');
} else {
    $periodStr = "TAHUN " . $year;
}

$locId = $_GET['location_id'] ?? 'all';
$locationTitle = 'SEMUA UAKPB';
$locationCode = '-';
if ($locId !== 'all') {
    $stmtLoc = $conn->prepare("SELECT code, name FROM inv_locations WHERE id = ?");
    $stmtLoc->bind_param("i", $locId);
    $stmtLoc->execute();
    $resLoc = $stmtLoc->get_result()->fetch_assoc();
    if ($resLoc) {
        $locationTitle = strtoupper($resLoc['name']);
        $locationCode = $resLoc['code'];
    }
}

// Build query
$sql = "
SELECT 
    t.doc_number, t.doc_date, t.book_date, 
    i.item_code, i.name, 
    t.quantity, t.unit_price, t.total_price, t.notes
FROM inv_transactions t
JOIN inv_items i ON t.item_id = i.id
WHERE 1=1
";
$params = [];
$types = "";

if ($locId !== 'all') {
    if ($locationCode !== '-') {
        $sql .= " AND t.doc_number LIKE ?";
        $params[] = $locationCode . '%';
        $types .= "s";
    }
    
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

if ($periodType === 'month') {
    $sql .= " AND MONTH(t.doc_date) = ? AND YEAR(t.doc_date) = ?";
    $params[] = $month;
    $params[] = date('Y');
    $types .= "ss";
} else {
    $sql .= " AND YEAR(t.doc_date) = ?";
    $params[] = $year;
    $types .= "s";
}

$sql .= " ORDER BY t.doc_date ASC, t.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak RTH - <?php echo $typeStr; ?></title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; font-weight: bold; font-size: 12pt; }
        .info { margin-bottom: 10px; font-weight: bold; font-size: 11pt; }
        .info table { border: none; font-size: 11pt; }
        .info td { padding: 2px 5px; }
        .table-data { width: 100%; max-width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: auto; word-wrap: break-word; }
        .table-data th, .table-data td { border: 1px solid black; padding: 4px; word-break: break-word; hyphens: auto; }
        .table-data th { background-color: #b3c6e7; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Column Width hints to prevent wide columns from breaking */
        .table-data th:nth-child(1) { width: 18%; } /* NOMOR DOKUMEN */
        .table-data th:nth-child(2), .table-data th:nth-child(3) { width: 8%; } /* TGL */
        .table-data th:nth-child(4) { width: 12%; } /* KODE BARANG */
        .table-data th:nth-child(5) { width: 25%; } /* NAMA BARANG */
        .table-data th:nth-child(6) { width: 7%; } /* JUMLAH */
        
        <?php if ($type === 'in'): ?>
        .table-data th:nth-child(7), .table-data th:nth-child(8) { width: 11%; } /* HARGA & TOTAL */
        <?php else: ?>
        .table-data th:nth-child(7) { width: 22%; } /* KETERANGAN */
        <?php endif; ?>

        @media print {
            @page { size: auto; margin: 10mm; }
            body { margin: 0; font-size: 10pt; }
            .no-print { display: none !important; }
            .table-data th, .table-data td { padding: 3px; font-size: 9pt; }
            .header { font-size: 11pt; }
            .info, .info table { font-size: 10pt; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #0ea5e9; color: white; border: none; border-radius: 4px;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px; border: 1px solid #ccc; background: white; border-radius: 4px;">Tutup</button>
    </div>

    <div class="header">
        REGISTER TRANSAKSI HARIAN<br>
        PERSEDIAAN <?php echo $type === 'koreksi' ? $typeStr : $typeStr . " " . $subtypeStr; ?><br>
        UNTUK PERIODE <?php echo $periodStr; ?>
    </div>

    <div class="info">
        <table cellspacing="0">
            <tr>
                <td>KODE UAKPB</td>
                <td>: <?php echo htmlspecialchars($locationCode); ?></td>
            </tr>
            <tr>
                <td>UAKPB</td>
                <td>: <?php echo htmlspecialchars($locationTitle); ?></td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th>NOMOR DOKUMEN</th>
                <th>TGL. DOK</th>
                <th>TGL. BUKU</th>
                <th>KODE BARANG</th>
                <th>NAMA BARANG</th>
                <th>JUMLAH</th>
                <?php if ($type === 'in'): ?>
                <th>HARGA SATUAN</th>
                <th>TOTAL</th>
                <?php else: ?>
                <th>KETERANGAN</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="<?php echo $type === 'in' ? '8' : '7'; ?>" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['doc_number']); ?></td>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($row['doc_date'])); ?></td>
                    <td class="text-center"><?php echo date('d-m-Y', strtotime($row['book_date'])); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['item_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td class="text-center"><?php echo $row['quantity']; ?></td>
                    
                    <?php if ($type === 'in'): ?>
                    <td class="text-right"><?php echo number_format($row['unit_price'], 0, ',', '.'); ?></td>
                    <td class="text-right"><?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                    <?php else: ?>
                    <td><?php echo htmlspecialchars($row['notes'] ?? ''); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
