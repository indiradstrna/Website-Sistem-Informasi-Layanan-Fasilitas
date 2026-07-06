<?php
require_once __DIR__ . '/config.php';

// Test CURL ke Telegram
$token = TELE_TOKEN;
$groupId = TELE_GROUP_ID;

echo "<h2>🔍 Debug Telegram</h2>";
echo "<b>Token:</b> " . substr($token, 0, 10) . "...<br>";
echo "<b>Group ID:</b> $groupId<br><br>";

// 1. Cek CURL tersedia
echo "<b>CURL Tersedia:</b> " . (function_exists('curl_init') ? '✅ Ya' : '❌ Tidak') . "<br><br>";

// 2. Test kirim pesan
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$payload = [
    'chat_id'    => $groupId,
    'text'       => "🧪 <b>TEST NOTIFIKASI</b>\n\nIni pesan uji coba dari sistem SILATAS.\nWaktu: " . date('d M Y H:i:s'),
    'parse_mode' => 'HTML'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<b>HTTP Status Code:</b> $httpCode<br>";

if ($curlError) {
    echo "<b style='color:red'>❌ CURL Error:</b> $curlError<br><br>";
} else {
    echo "<b>CURL Error:</b> ✅ Tidak ada<br><br>";
}

$result = json_decode($response, true);
echo "<b>Response Telegram:</b><br>";
echo "<pre style='background:#f1f5f9;padding:1rem;border-radius:0.5rem;overflow-x:auto;'>" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";

if (!empty($result['ok']) && $result['ok'] === true) {
    echo "<div style='color:green;font-weight:bold'>✅ Pesan berhasil dikirim ke Telegram!</div>";
} else {
    echo "<div style='color:red;font-weight:bold'>❌ Gagal kirim. Lihat error description di atas.</div>";
    
    if (!empty($result['description'])) {
        echo "<br><b>Detail Error:</b> " . $result['description'];
    }
}
