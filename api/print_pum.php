<?php
// =============================================================
// api/print_pum.php - Cetak Formulir PUM (Permintaan Uang Muka)
// Mengisi docs/PUM_Template.xlsx dengan data RAB lalu convert ke PDF via LibreOffice
// =============================================================

require_once '../config.php'; // Menggunakan koneksi mysqli standar ($conn)

// ==========================================
// KONFIGURASI LIBREOFFICE
// ==========================================
// Ubah path ini menyesuaikan lokasi instalasi LibreOffice di server/komputer Anda.
// Jika sudah masuk di Environment Variables (PATH), cukup gunakan 'soffice'.
$LIBREOFFICE_PATH = '"C:\Program Files\LibreOffice\program\soffice.exe"';

// ---------- 1. Ambil & validasi request_id ----------
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($request_id <= 0) {
    die('Parameter id tidak valid.');
}

// ---------- 2. Ambil data pengajuan perbaikan ----------
$stmt = $conn->prepare("
    SELECT r.*, u.full_name AS applicant_name 
    FROM repair_requests r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$repair = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$repair) {
    die('Data pengajuan tidak ditemukan.');
}

// ---------- 3. Ambil item RAB ----------
$stmt2 = $conn->prepare("
    SELECT item_name, quantity, unit_price, total_price
    FROM repair_budgets
    WHERE repair_request_id = ?
    ORDER BY id ASC
");
$stmt2->bind_param("i", $request_id);
$stmt2->execute();
$rab_items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

if (empty($rab_items)) {
    die('Belum ada data RAB untuk pengajuan ini.');
}

// ---------- 4. Ambil nama approver (Manager FMD) ----------
$approver_name     = '';
$approver_position = 'Manager FMD';

// Parse log di kolom note untuk mencari approver
if (!empty($repair['note'])) {
    $lines = explode("\n", $repair['note']);
    foreach (array_reverse($lines) as $line) {
        if (preg_match('/\[.*?\]\s*\[(.*?)\]\s*-\s*(waiting_manager_fmd|approved_waiting_fund|approved)/i', $line, $m)) {
            $approver_name = trim($m[1]);
            break;
        }
    }
}

// Fallback: ambil dari tabel users berdasarkan role
if (empty($approver_name)) {
    $stmtA = $conn->query("SELECT full_name FROM users WHERE role = 'managerFMD' LIMIT 1");
    if ($stmtA && $stmtA->num_rows > 0) {
        $approver_name = $stmtA->fetch_assoc()['full_name'];
    } else {
        $approver_name = 'Manager FMD';
    }
}

// ---------- 5. Hitung total RAB ----------
$grand_total = 0;
foreach ($rab_items as $item) {
    $grand_total += (float)$item['total_price'];
}

// ---------- 6. Konversi angka ke terbilang (Bahasa Indonesia) ----------
function terbilang(float $angka): string {
    $angka   = (int) round($angka);
    $satuan  = ['', 'satu', 'dua', 'tiga', 'empat', 'lima',
                'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
                'sebelas'];

    if ($angka < 12) return $satuan[$angka];
    if ($angka < 20) return terbilang($angka - 10) . ' belas';
    if ($angka < 100) {
        $r = (int)($angka / 10);
        return terbilang($r) . ' puluh' . ($angka % 10 !== 0 ? ' ' . terbilang($angka % 10) : '');
    }
    if ($angka < 200) return 'seratus' . ($angka % 100 !== 0 ? ' ' . terbilang($angka % 100) : '');
    if ($angka < 1000) {
        $r = (int)($angka / 100);
        return terbilang($r) . ' ratus' . ($angka % 100 !== 0 ? ' ' . terbilang($angka % 100) : '');
    }
    if ($angka < 2000) return 'seribu' . ($angka % 1000 !== 0 ? ' ' . terbilang($angka % 1000) : '');
    if ($angka < 1000000) {
        $r = (int)($angka / 1000);
        return terbilang($r) . ' ribu' . ($angka % 1000 !== 0 ? ' ' . terbilang($angka % 1000) : '');
    }
    if ($angka < 1000000000) {
        $r = (int)($angka / 1000000);
        return terbilang($r) . ' juta' . ($angka % 1000000 !== 0 ? ' ' . terbilang($angka % 1000000) : '');
    }
    $r = (int)($angka / 1000000000);
    return terbilang($r) . ' miliar' . ($angka % 1000000000 !== 0 ? ' ' . terbilang($angka % 1000000000) : '');
}

function formatTerbilang(float $angka): string {
    $str = ucfirst(terbilang($angka)) . ' rupiah';
    return preg_replace('/\s+/', ' ', $str);
}

// ---------- 7. Siapkan data untuk template ----------
$bulan_indo = ['Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'];
$tanggal_sekarang = date('d') . ' ' . $bulan_indo[(int)date('m') - 1] . ' ' . date('Y');

$activity_desc = "Perbaikan " . htmlspecialchars($repair['repair_type'] ?? '') . " di " . htmlspecialchars($repair['location_details'] ?? '');

// ---------- 8. Load composer autoload (PhpSpreadsheet) ----------
$autoload_path = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload_path)) {
    die("Composer autoload belum diinstall. Silakan jalankan 'composer install' di folder utama.");
}
require_once $autoload_path;

// Lokasi template PUM asli
$template_path = __DIR__ . '/../templates/PUM_Template.xlsx';
if (!file_exists($template_path)) {
    die("Template file tidak ditemukan di: " . $template_path);
}

// ---------- 9. Isi template Excel menggunakan PhpSpreadsheet ----------
try {
    $reader      = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($template_path);
    $sheet       = $spreadsheet->getActiveSheet();

    // ─── Isi field header ───────────────────────────────────────────
    $sheet->setCellValue('B9', 'Activity     : ' . $activity_desc);
    $sheet->setCellValue('J18', $tanggal_sekarang);

    // ─── Isi baris item RAB (B12:I15, maks 4 item) ──────────────────
    // Template hanya punya 4 baris ruang
    $rows    = ['12', '13', '14', '15'];
    $max_row = min(count($rab_items), 4);

    for ($i = 0; $i < $max_row; $i++) {
        $row  = $rows[$i];
        $item = $rab_items[$i];

        $sheet->setCellValue('D' . $row, $item['item_name']);
        $sheet->setCellValue('G' . $row, (int)$item['quantity']);
        $sheet->setCellValue('H' . $row, (float)$item['unit_price']);
        $sheet->setCellValue('I' . $row, '=G' . $row . '*H' . $row);
    }

    if (count($rab_items) > 4) {
        $sheet->setCellValue('J12', 'RAB/rincian terlampir (' . count($rab_items) . ' item)');
    } else {
        $sheet->setCellValue('J12', 'Sesuai rincian RAB');
    }

    // ─── Terbilang (B17) ─────────────────────────────────────────────
    $sheet->setCellValue('B17', 'Amount in words/ Terbilang :   ' . formatTerbilang($grand_total));

    // ─── Nama approver (C27) ─────────────────────────────────────────
    $sheet->setCellValue('C27', $approver_name);
    $sheet->setCellValue('C30', $approver_position);

    // ─── Nama pemohon (J27) ──────────────────────────────────────────
    $sheet->setCellValue('J27', $repair['applicant_name'] ?? '');
    $sheet->setCellValue('J30', 'Staff / Pegawai');

    // ─── Set Page Setup agar muat di 1 halaman (Fit to Page) ─────────
    $sheet->getPageSetup()->setFitToPage(true);
    $sheet->getPageSetup()->setFitToWidth(1);
    $sheet->getPageSetup()->setFitToHeight(1);
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
    $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

    // ─── Simpan ke file temp lalu convert dengan LibreOffice ─────────
    $tmp_dir  = sys_get_temp_dir();
    $tmp_xlsx = tempnam($tmp_dir, 'pum_') . '.xlsx';
    
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($tmp_xlsx);

    // ─── Convert ke PDF via LibreOffice (CLI) ────────────────────────
    // soffice --headless --convert-to pdf --outdir <dir> <file>
    $cmd = sprintf(
        '%s --headless --convert-to pdf --outdir %s %s 2>&1',
        $LIBREOFFICE_PATH,
        escapeshellarg($tmp_dir),
        escapeshellarg($tmp_xlsx)
    );
    
    exec($cmd, $output, $ret);

    $tmp_pdf = $tmp_dir . DIRECTORY_SEPARATOR . pathinfo($tmp_xlsx, PATHINFO_FILENAME) . '.pdf';

    if ($ret === 0 && file_exists($tmp_pdf)) {
        // Kirim PDF ke browser
        $filename = 'PUM_Repair_' . $request_id . '_' . date('Ymd') . '.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmp_pdf));
        readfile($tmp_pdf);

        // Cleanup
        @unlink($tmp_xlsx);
        @unlink($tmp_pdf);
        exit;
    } else {
        echo "<h3>Gagal mengkonversi ke PDF dengan LibreOffice!</h3>";
        echo "<p>Return code: " . $ret . "</p>";
        echo "<pre>Command: " . htmlspecialchars($cmd) . "</pre>";
        echo "<pre>Output: " . htmlspecialchars(implode("\n", $output)) . "</pre>";
        echo "<p>Silakan pastikan LibreOffice terinstall dan path di konfigurasi benar.</p>";
        // Cleanup xlsx
        @unlink($tmp_xlsx);
    }

} catch (\Exception $e) {
    die('Error PhpSpreadsheet: ' . $e->getMessage());
}
