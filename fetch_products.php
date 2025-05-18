<?php
header('Content-Type: application/json');

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$perpage = 100;
$mainCategory = 5;
$orderby = 1;

// Buat payload boundary untuk multipart/form-data
$boundary = '----WebKitFormBoundary' . bin2hex(random_bytes(8));

// Buat body request
$data = '';
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"Page\"\r\n\r\n";
$data .= "$page\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"Perpage\"\r\n\r\n";
$data .= "$perpage\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"Orderby\"\r\n\r\n";
$data .= "$orderby\r\n";
$data .= "--$boundary\r\n";
$data .= "Content-Disposition: form-data; name=\"MainCategory\"\r\n\r\n";
$data .= "$mainCategory\r\n";
$data .= "--$boundary--\r\n";

// Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://core.dandanku.com/api/product');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: multipart/form-data; boundary=' . $boundary,
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
]);

// Nonaktifkan verifikasi SSL untuk mengatasi error sertifikat
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// Eksekusi request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Handle error
if ($error) {
    echo json_encode([
        'Status' => 'Error',
        'Message' => 'cURL Error: ' . $error,
        'Data' => null
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'Status' => 'Error',
        'Message' => 'HTTP Error: ' . $httpCode,
        'Data' => null
    ]);
    exit;
}

// Kirim response dari API
echo $response;