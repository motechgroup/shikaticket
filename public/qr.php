<?php
// Simple QR proxy to avoid external image blocking on some hosts (e.g., ngrok)
// Usage: /qr.php?data=TEXT&size=200

$data = $_GET['data'] ?? '';
$size = (int)($_GET['size'] ?? 200);
if ($size < 50 || $size > 800) { $size = 200; }

if ($data === '') {
    http_response_code(400);
    header('Content-Type: text/plain');
    echo 'Missing data';
    exit;
}

$remote = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data);

// Fetch via cURL
$ch = curl_init($remote);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$img = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status >= 200 && $status < 300 && $img !== false) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    echo $img;
    exit;
}

// Fallback to Google Charts if primary fails
$fallback = 'https://chart.googleapis.com/chart?cht=qr&chs=' . $size . 'x' . $size . '&chl=' . urlencode($data);
$img = @file_get_contents($fallback);
if ($img !== false) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    echo $img;
    exit;
}

http_response_code(502);
header('Content-Type: text/plain');
echo 'Failed to generate QR';

