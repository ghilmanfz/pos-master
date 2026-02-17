<?php
/**
 * Script Reminder Otomatis untuk Member/Customer
 * Jalankan script ini via Cron Job setiap hari atau sesuai kebutuhan
 * Contoh Cron: 0 9 * * * php /path/to/reminder_otomatis.php (setiap jam 9 pagi)
 */

require __DIR__ . '/../config.php';

// Fungsi untuk mengirim WhatsApp via API Fonnte
function sendWhatsAppReminder($token, $phone, $message) {
    $url = "https://api.fonnte.com/send";
    
    $data = [
        'target' => $phone,
        'message' => $message
    ];
    
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
        return ['success' => false, 'message' => $error];
    }
    
    if ($httpCode == 200 || $httpCode == 201) {
        return ['success' => true, 'message' => 'Berhasil', 'response' => $response];
    } else {
        return ['success' => false, 'message' => 'HTTP ' . $httpCode, 'response' => $response];
    }
}

try {
    // Ambil pengaturan toko
    $sqlToko = "SELECT * FROM toko WHERE id_toko = 1";
    $rowToko = $config->prepare($sqlToko);
    $rowToko->execute();
    $toko = $rowToko->fetch();
    
    // Cek apakah reminder aktif
    if (!$toko || $toko['reminder_aktif'] != 'ya') {
        echo "Reminder tidak aktif\n";
        exit;
    }
    
    $apiToken = $toko['api_fonte_token'];
    $apiPhone = $toko['api_fonte_phone'];
    $namaToko = $toko['nama_toko'];
    $kontakToko = $toko['tlp'];
    $pesanTemplate = $toko['pesan_test'] ?? 'Halo {nama}, stok {barang} sudah tersedia!';
    $intervalDays = isset($toko['interval_reminder']) ? (int)$toko['interval_reminder'] : 3;
    
    if (empty($apiToken) || empty($apiPhone)) {
        echo "API Fonte belum dikonfigurasi\n";
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
        echo "Tidak ada customer yang perlu di-reminder (interval: " . $intervalDays . " hari)\n";
        exit;
    }
    
    echo "Ditemukan " . count($customers) . " customer yang perlu di-reminder (interval: " . $intervalDays . " hari)\n";
    
    $successCount = 0;
    $failCount = 0;
    
    foreach ($customers as $customer) {
        $namaCustomer = $customer['nama_customer'];
        $noTelepon = $customer['no_telepon'];
        $namaBarang = $customer['nama_barang'];
        $stok = $customer['stok'];
        $hargaJual = number_format($customer['harga_jual'], 0, ',', '.');
        
        // Gunakan template pesan dari database, atau fallback ke default
        $pesan = str_replace(
            ['{nama}', '{barang}', '{toko}', '{phone}', '{stok}', '{harga}'],
            [$namaCustomer, $namaBarang, $namaToko, $kontakToko, $stok, 'Rp ' . $hargaJual],
            $pesanTemplate
        );
        
        // Kirim pesan
        $result = sendWhatsAppReminder($apiToken, $noTelepon, $pesan);
        
        // Log ke database
        $status = $result['success'] ? 'berhasil' : 'gagal';
        $response = isset($result['response']) ? $result['response'] : $result['message'];
        
        $sqlLog = "INSERT INTO reminder_log (id_customer, id_barang, no_telepon, pesan, status, tanggal_kirim, response) 
                   VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $rowLog = $config->prepare($sqlLog);
        $rowLog->execute([
            $customer['id_customer'],
            $customer['id_barang'],
            $noTelepon,
            $pesan,
            $status,
            $response
        ]);
        
        if ($result['success']) {
            $successCount++;
            echo "✓ Berhasil mengirim ke {$namaCustomer} ({$noTelepon})\n";
        } else {
            $failCount++;
            echo "✗ Gagal mengirim ke {$namaCustomer} ({$noTelepon}): {$result['message']}\n";
        }
        
        // Delay untuk menghindari rate limiting
        sleep(2);
    }
    
    // Update reminder terakhir di toko
    $sqlUpdate = "UPDATE toko SET reminder_terakhir = NOW() WHERE id_toko = 1";
    $rowUpdate = $config->prepare($sqlUpdate);
    $rowUpdate->execute();
    
    echo "\n=== Selesai ===\n";
    echo "Berhasil: {$successCount}\n";
    echo "Gagal: {$failCount}\n";
    echo "Total: " . count($customers) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
