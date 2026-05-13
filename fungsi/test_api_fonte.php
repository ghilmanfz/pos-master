<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require '../config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['token']) || !isset($input['phone'])) {
    echo json_encode(['success' => false, 'message' => 'Token dan nomor telepon diperlukan']);
    exit;
}

$token = trim($input['token']);
$phoneTest = trim($input['phone']);
$message = isset($input['message']) ? trim($input['message']) : '';

if (empty($token) || empty($phoneTest)) {
    echo json_encode(['success' => false, 'message' => 'Token dan nomor telepon tidak boleh kosong']);
    exit;
}

// Jika tidak ada message dari input, gunakan default
if (empty($message)) {
    $message = "Test koneksi API Fonnte berhasil! Sistem reminder POS Anda sudah terhubung.";
}

// Ambil nomor customer aktif (maksimal 3 untuk test)
$sqlCustomers = "SELECT nama_customer, no_telepon FROM customer WHERE status = 'aktif' AND no_telepon != '' AND no_telepon != '0000000000' ORDER BY id_customer DESC LIMIT 3";
$stmtCustomers = $config->prepare($sqlCustomers);
$stmtCustomers->execute();
$customers = $stmtCustomers->fetchAll(PDO::FETCH_ASSOC);

// Kumpulkan semua nomor yang akan dikirim
$recipients = [];

// 1. Nomor testing
$recipients[] = ['nama' => 'Testing', 'phone' => $phoneTest];

// 2. Nomor customer
foreach ($customers as $customer) {
    $recipients[] = ['nama' => $customer['nama_customer'], 'phone' => $customer['no_telepon']];
}

// API Fonnte endpoint
$url = "https://api.fonnte.com/send";

$results = [];
$successCount = 0;
$failCount = 0;

// Kirim ke semua nomor
foreach ($recipients as $recipient) {
    $targetPhone = $recipient['phone'];
    $targetName = $recipient['nama'];
    
    // Personalisasi pesan
    $personalMessage = str_replace(
        ['{nama}', '{barang}', '{toko}', '{phone}'],
        [$targetName, 'Produk Test', 'Toko Anda', $phoneTest],
        $message
    );
    
    $data = [
        'target' => $targetPhone,
        'message' => $personalMessage
    ];
    
    // Send request using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $token
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        $results[] = $targetName . ' (' . $targetPhone . '): ❌ Error - ' . $error;
        $failCount++;
    } elseif ($httpCode == 200 || $httpCode == 201) {
        $results[] = $targetName . ' (' . $targetPhone . '): ✅ Terkirim';
        $successCount++;
    } else {
        $responseData = json_decode($response, true);
        $errorMsg = isset($responseData['message']) ? $responseData['message'] : 'HTTP ' . $httpCode;
        $results[] = $targetName . ' (' . $targetPhone . '): ❌ Gagal - ' . $errorMsg;
        $failCount++;
    }
    
    // Delay 1 detik antar pengiriman untuk menghindari rate limit
    if (count($recipients) > 1) {
        sleep(1);
    }
}

// Return summary
echo json_encode([
    'success' => $successCount > 0,
    'message' => 'Terkirim: ' . $successCount . ' | Gagal: ' . $failCount,
    'details' => implode('<br>', $results),
    'total_recipients' => count($recipients)
]);
?>
