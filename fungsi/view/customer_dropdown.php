<?php
// Prevent caching of AJAX responses
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: application/json');

session_start();
if (!empty($_SESSION['admin'])) {
    require '../../config.php';
    include $view;
    
    $lihat = new view($config);
    
    try {
        // Get active customers
        $customers = $lihat->customer_aktif();
        
        echo json_encode([
            'success' => true,
            'customers' => $customers
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal memuat data customer: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
}
?>
