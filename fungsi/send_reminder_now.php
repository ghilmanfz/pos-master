<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require '../config.php';

try {
    // Ambil pengaturan toko
    $sqlToko = "SELECT * FROM toko WHERE id_toko = 1";
    $rowToko = $config->prepare($sqlToko);
    $rowToko->execute();
    $toko = $rowToko->fetch();
    
    // Cek apakah reminder aktif
    if (!$toko) {
        echo json_encode(['success' => false, 'message' => 'Data toko tidak ditemukan']);
        exit;
    }
    
    $apiToken = $toko['api_fonte_token'] ?? '';
    $namaToko = $toko['nama_toko'] ?? '';
    $kontakToko = $toko['tlp'] ?? '';
    $pesanTemplate = $toko['pesan_test'] ?? 'Halo {nama}, stok {barang} sudah tersedia!';
    $intervalDays = isset($toko['interval_reminder']) ? (int)$toko['interval_reminder'] : 3;
    
    if (empty($apiToken)) {
        echo json_encode(['success' => false, 'message' => 'API Token belum dikonfigurasi']);
        exit;
    }
    
    // Ambil daftar customer yang perlu di-reminder
    // Customer yang sudah X hari sejak terakhir beli dan barang nya stok habis/hampir habis
    $sql = "SELECT DISTINCT cb.*, c.nama_customer, c.no_telepon, b.nama_barang, b.stok, b.harga_jual
            FROM customer_barang cb
            LEFT JOIN customer c ON cb.id_customer = c.id_customer
            LEFT JOIN barang b ON cb.id_barang = b.id_barang
            WHERE c.status = 'aktif' 
            AND c.no_telepon != ''
            AND c.no_telepon != '0000000000'
            AND b.stok <= 3
            AND cb.terakhir_beli <= DATE_SUB(NOW(), INTERVAL ? DAY)
            ORDER BY cb.terakhir_beli ASC";
    
    $row = $config->prepare($sql);
    $row->execute([$intervalDays]);
    $customers = $row->fetchAll();
    
    if (count($customers) == 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Tidak ada customer yang memenuhi kriteria untuk reminder (stok habis & sudah ' . $intervalDays . ' hari)'
        ]);
        exit;
    }
    
    $url = "https://api.fonnte.com/send";
    $results = [];
    $successCount = 0;
    $failCount = 0;
    
    foreach ($customers as $customer) {
        $namaCustomer = $customer['nama_customer'];
        $noTelepon = $customer['no_telepon'];
        $namaBarang = $customer['nama_barang'];
        $stok = $customer['stok'];
        $hargaJual = number_format($customer['harga_jual'], 0, ',', '.');
        
        // Personalisasi pesan
        $personalMessage = str_replace(
            ['{nama}', '{barang}', '{toko}', '{phone}', '{stok}', '{harga}'],
            [$namaCustomer, $namaBarang, $namaToko, $kontakToko, $stok, $hargaJual],
            $pesanTemplate
        );
        
        $data = [
            'target' => $noTelepon,
            'message' => $personalMessage
        ];
        
        // Send request using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiToken
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $status = 'gagal';
        $statusMsg = '';
        
        if ($error) {
            $statusMsg = 'Error: ' . $error;
            $failCount++;
        } elseif ($httpCode == 200 || $httpCode == 201) {
            $status = 'berhasil';
            $statusMsg = 'Terkirim';
            $successCount++;
        } else {
            $responseData = json_decode($response, true);
            $statusMsg = isset($responseData['message']) ? $responseData['message'] : 'HTTP ' . $httpCode;
            $failCount++;
        }
        
        $results[] = $namaCustomer . ' (' . $noTelepon . ') - ' . $namaBarang . ': ' . ($status == 'berhasil' ? '✅' : '❌') . ' ' . $statusMsg;
        
        // Log ke database
        $sqlLog = "INSERT INTO reminder_log (id_customer, id_barang, no_telepon, pesan, status, tanggal_kirim, response) 
                   VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmtLog = $config->prepare($sqlLog);
        $stmtLog->execute([
            $customer['id_customer'],
            $customer['id_barang'],
            $noTelepon,
            $personalMessage,
            $status,
            $response
        ]);
        
        // Delay 1 detik antar pengiriman
        if (count($customers) > 1) {
            sleep(1);
        }
    }
    
    // Update reminder_terakhir di toko
    $sqlUpdateToko = "UPDATE toko SET reminder_terakhir = NOW() WHERE id_toko = 1";
    $config->query($sqlUpdateToko);
    
    echo json_encode([
        'success' => $successCount > 0,
        'message' => 'Terkirim: ' . $successCount . ' | Gagal: ' . $failCount . ' (dari ' . count($customers) . ' customer)',
        'details' => implode('<br>', $results),
        'total_recipients' => count($customers)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
