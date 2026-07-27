<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$periode_type = $_GET['periode_type'] ?? 'tahun';
$startDate = '';
$endDate = '';
$periodLabel = '';
$yearLabel = '';
$prevDateLabel = '';
$currDateLabel = '';

if ($periode_type === 'tahun') {
    $y = $_GET['tahun_val'] ?? date('Y');
    $startDate = "$y-01-01";
    $endDate = "$y-12-31";
    $periodLabel = "UNTUK PERIODE YANG BERAKHIR TANGGAL 31 DESEMBER $y";
    $yearLabel = "TAHUN ANGGARAN : $y";
    $prevDateLabel = "S/D 31 DESEMBER " . ($y - 1);
    $currDateLabel = "S/D 31 DESEMBER $y";
} elseif ($periode_type === 'semester') {
    $sem = $_GET['semester_val'] ?? '1';
    $y = $_GET['semester_tahun_val'] ?? date('Y');
    if ($sem == '1') {
        $startDate = "$y-01-01";
        $endDate = "$y-06-30";
        $periodLabel = "UNTUK PERIODE YANG BERAKHIR TANGGAL 30 JUNI $y";
        $prevDateLabel = "S/D 31 DESEMBER " . ($y - 1);
        $currDateLabel = "S/D 30 JUNI $y";
    } else {
        $startDate = "$y-07-01";
        $endDate = "$y-12-31";
        $periodLabel = "UNTUK PERIODE YANG BERAKHIR TANGGAL 31 DESEMBER $y";
        $prevDateLabel = "S/D 30 JUNI $y";
        $currDateLabel = "S/D 31 DESEMBER $y";
    }
    $yearLabel = "TAHUN ANGGARAN : $y";
} else {
    $sd_tanggal = $_GET['sd_tanggal_val'] ?? date('Y-m-d');
    $startDate = date('Y-01-01', strtotime($sd_tanggal)); // start of year
    $endDate = $sd_tanggal;
    $y = date('Y', strtotime($sd_tanggal));
    $dFmt = date('d-m-Y', strtotime($sd_tanggal));
    $periodLabel = "UNTUK PERIODE YANG BERAKHIR TANGGAL $dFmt";
    $yearLabel = "TAHUN ANGGARAN : $y";
    $prevDateLabel = "S/D 31 DESEMBER " . ($y - 1);
    $currDateLabel = "S/D $dFmt";
}

$locId = $_GET['location_id'] ?? 'all';
$whereLocation = '';
$locationTitle = 'SEMUA UAKPB';
$locationCode = '-';
if ($locId !== 'all') {
    $whereLocation = "WHERE i.location_id = " . intval($locId);
    $stmtLoc = $conn->prepare("SELECT code, name FROM inv_locations WHERE id = ?");
    $stmtLoc->bind_param("i", $locId);
    $stmtLoc->execute();
    $resLoc = $stmtLoc->get_result()->fetch_assoc();
    if ($resLoc) {
        $locationTitle = strtoupper($resLoc['name']);
        $locationCode = $resLoc['code'];
    }
}

$sql = "
SELECT 
    LEFT(i.item_code, 10) AS kd_brg,
    COALESCE(s.ur_sskel, 'LAIN-LAIN') AS category_name,
    i.item_code,
    i.name AS item_name,
    COALESCE(SUM(CASE WHEN t.doc_date < ? AND t.type = 'in' THEN t.quantity 
                      WHEN t.doc_date < ? AND t.type = 'out' THEN -t.quantity ELSE 0 END), 0) AS saldo_awal_qty,
    COALESCE(SUM(CASE WHEN t.doc_date < ? AND t.type = 'in' THEN t.total_price 
                      WHEN t.doc_date < ? AND t.type = 'out' THEN -t.total_price ELSE 0 END), 0) AS saldo_awal_rp,
    
    COALESCE(SUM(CASE WHEN t.doc_date >= ? AND t.doc_date <= ? AND t.type = 'in' THEN t.quantity ELSE 0 END), 0) AS mutasi_tambah_qty,
    COALESCE(SUM(CASE WHEN t.doc_date >= ? AND t.doc_date <= ? AND t.type = 'out' THEN t.quantity ELSE 0 END), 0) AS mutasi_kurang_qty,
    
    COALESCE(SUM(CASE WHEN t.doc_date >= ? AND t.doc_date <= ? AND t.type = 'in' THEN t.total_price ELSE 0 END), 0) AS mutasi_tambah_rp,
    COALESCE(SUM(CASE WHEN t.doc_date >= ? AND t.doc_date <= ? AND t.type = 'out' THEN t.total_price ELSE 0 END), 0) AS mutasi_kurang_rp
FROM inv_items i
LEFT JOIN inv_transactions t ON i.id = t.item_id
LEFT JOIN inv_sskel s ON LEFT(i.item_code, 10) = s.kd_brg
$whereLocation
GROUP BY i.id
HAVING saldo_awal_qty != 0 OR mutasi_tambah_qty != 0 OR mutasi_kurang_qty != 0
ORDER BY i.item_code ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssss", $startDate, $startDate, $startDate, $startDate, 
                                $startDate, $endDate, $startDate, $endDate,
                                $startDate, $endDate, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$totalAwalRp = 0;
$totalAkhirRp = 0;

while ($row = $result->fetch_assoc()) {
    $cat = $row['kd_brg'] . " - " . strtoupper($row['category_name']);
    if (!isset($data[$cat])) {
        $data[$cat] = [
            'items' => [],
            'sum_awal_rp' => 0,
            'sum_akhir_rp' => 0
        ];
    }
    
    $row['saldo_akhir_qty'] = $row['saldo_awal_qty'] + $row['mutasi_tambah_qty'] - $row['mutasi_kurang_qty'];
    $row['saldo_akhir_rp'] = $row['saldo_awal_rp'] + $row['mutasi_tambah_rp'] - $row['mutasi_kurang_rp'];
    
    $data[$cat]['items'][] = $row;
    $data[$cat]['sum_awal_rp'] += $row['saldo_awal_rp'];
    $data[$cat]['sum_akhir_rp'] += $row['saldo_akhir_rp'];
    
    $totalAwalRp += $row['saldo_awal_rp'];
    $totalAkhirRp += $row['saldo_akhir_rp'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rincian Barang Persediaan</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; margin: 20px; }
        .header-top { display: flex; justify-content: space-between; font-size: 10pt; margin-bottom: 20px; }
        .header-center { text-align: center; font-weight: bold; margin-bottom: 20px; line-height: 1.5; }
        .info-table { border: none; font-size: 11pt; font-weight: bold; color: #0000FF; margin-bottom: 10px; }
        .info-table td { padding: 2px 10px 2px 0; }
        
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid black; padding: 4px; word-break: break-word; }
        .table-data th { background-color: #f3f4f6; text-align: center; font-weight: bold; }
        
        .cat-row td { color: #0000FF; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { margin: 0; font-size: 10pt; }
            .no-print { display: none !important; }
            .table-data th, .table-data td { padding: 3px; font-size: 9pt; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #0ea5e9; color: white; border: none; border-radius: 4px;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px; border: 1px solid #ccc; background: white; border-radius: 4px;">Tutup</button>
    </div>

    <div class="header-top">
        <div>
            UAPB<br>
            UAPPB-E1<br>
            UAPPB-W
        </div>
        <div>
            : KEMENTERIAN DASAR DAN MENENGAH<br>
            : SEKRETARIAT JENDERAL<br>
            : Kantor Pusat
        </div>
        <div style="flex-grow: 1; text-align: right;">Hal 1</div>
    </div>

    <div class="header-center">
        LAPORAN RINCIAN BARANG PERSEDIAAN<br>
        <?php echo $periodLabel; ?><br>
        <?php echo $yearLabel; ?>
    </div>

    <table class="info-table" cellspacing="0">
        <tr>
            <td>UAKPB</td>
            <td>: <?php echo htmlspecialchars($locationTitle); ?></td>
        </tr>
        <tr>
            <td>KODE UAKPB</td>
            <td>: <?php echo htmlspecialchars($locationCode); ?></td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 10%;">KODE</th>
                <th rowspan="2" style="width: 25%;">URAIAN</th>
                <th colspan="2">NILAI<br><?php echo $prevDateLabel; ?></th>
                <th colspan="3">MUTASI</th>
                <th colspan="2">NILAI<br><?php echo $currDateLabel; ?></th>
            </tr>
            <tr>
                <th>JUMLAH</th>
                <th>RUPIAH</th>
                <th>TAMBAH</th>
                <th>KURANG</th>
                <th>JUMLAH</th>
                <th>JUMLAH</th>
                <th>RUPIAH</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data persediaan pada periode ini.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($data as $cat => $group): ?>
                    <?php 
                        // split category code and name for display
                        list($catCode, $catName) = explode(" - ", $cat, 2);
                    ?>
                    <tr class="cat-row">
                        <td></td>
                        <td><?php echo htmlspecialchars($catName); ?></td>
                        <td></td>
                        <td class="text-right"><?php echo number_format($group['sum_awal_rp'], 0, ',', '.'); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><?php echo number_format($group['sum_akhir_rp'], 0, ',', '.'); ?></td>
                    </tr>
                    
                    <?php foreach ($group['items'] as $item): ?>
                        <?php 
                            $shortCode = substr($item['item_code'], 10); 
                            $mutasiJml = $item['mutasi_tambah_qty'] - $item['mutasi_kurang_qty'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo htmlspecialchars($shortCode); ?></td>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td class="text-right"><?php echo $item['saldo_awal_qty']; ?></td>
                            <td class="text-right"><?php echo number_format($item['saldo_awal_rp'], 0, ',', '.'); ?></td>
                            <td class="text-right"><?php echo $item['mutasi_tambah_qty']; ?></td>
                            <td class="text-right"><?php echo $item['mutasi_kurang_qty']; ?></td>
                            <td class="text-right"><?php echo $mutasiJml; ?></td>
                            <td class="text-right"><?php echo $item['saldo_akhir_qty']; ?></td>
                            <td class="text-right"><?php echo number_format($item['saldo_akhir_rp'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="display: flex; justify-content: space-between; margin-top: 50px;">
        <div style="text-align: center; width: 30%;">
        </div>
        <div style="text-align: center; width: 30%;">
            Bogor, <?php echo $_GET['tanggal_isi'] ? date('d-m-Y', strtotime($_GET['tanggal_isi'])) : date('d-m-Y'); ?><br>
            Penanggung Jawab UAKPB<br><br><br><br>
            ( ......................................... )<br>
            NIP: 
        </div>
    </div>
</body>
</html>
