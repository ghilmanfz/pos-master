<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../debug_tambah.log');

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
        echo "<h1 style='color:red'>Fatal Error</h1>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($error['file']) . "</p>";
        echo "<p><strong>Line:</strong> " . $error['line'] . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><a href='../../index.php?page=customer'>← Kembali ke Customer</a></p>";
        echo "</body></html>";
    }
});

// Start output buffering
ob_start();

// Debug output
file_put_contents(__DIR__ . '/../../debug_tambah.log', "\n" . str_repeat('=', 80) . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../../debug_tambah.log', date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../../debug_tambah.log', "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../../debug_tambah.log', "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../../debug_tambah.log', "GET: " . json_encode($_GET) . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../../debug_tambah.log', "POST keys: " . json_encode(array_keys($_POST ?? [])) . "\n", FILE_APPEND);

session_start();
file_put_contents(__DIR__ . '/../../debug_tambah.log', "Session role: " . ($_SESSION['admin'] ?? $_SESSION['kasir'] ?? $_SESSION['owner'] ?? 'none') . "\n", FILE_APPEND);

if (!empty($_SESSION['admin'])) {
    try {
        require '../../config.php';
        require_once __DIR__.'/../csrf.php';
        file_put_contents(__DIR__ . '/../../debug_tambah.log', "Config loaded, DB connection: " . (isset($config) ? 'OK' : 'FAIL') . "\n", FILE_APPEND);
        csrf_guard();
        file_put_contents(__DIR__ . '/../../debug_tambah.log', "CSRF guard passed\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/../../debug_tambah.log', "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
        echo "<h1 style='color:red'>Error Loading Config</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><a href='../../index.php'>← Kembali</a></p>";
        echo "</body></html>";
        exit;
    }

    if (!function_exists('redirect_with_fallback')) {
        function redirect_with_fallback(string $url): void
        {
            if (!headers_sent()) {
                header('Location: '.$url);
            }
            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            echo '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0;url='.$safeUrl.'"></head><body>';
            echo '<script>window.location="'.$safeUrl.'";</script>';
            echo 'Mengalihkan... Jika tidak berpindah, klik <a href="'.$safeUrl.'">di sini</a>.';
            echo '</body></html>';
            exit;
        }
    }

    if (!function_exists('sanitize_scalar_input')) {
        function sanitize_scalar_input($value, bool $allowNewlines = false): string
        {
            $stringValue = trim((string) $value);
            $pattern = $allowNewlines ? '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u';
            $cleaned = preg_replace($pattern, '', $stringValue);

            return $cleaned === null ? '' : trim($cleaned);
        }

        function get_post_string(string $key, bool $allowNewlines = false): string
        {
            $value = filter_input(
                INPUT_POST,
                $key,
                FILTER_UNSAFE_RAW,
                ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]
            );

            if ($value === null) {
                return '';
            }

            return sanitize_scalar_input($value, $allowNewlines);
        }

        function get_post_float(string $key): float
        {
            $value = filter_input(
                INPUT_POST,
                $key,
                FILTER_SANITIZE_NUMBER_FLOAT,
                ['flags' => FILTER_FLAG_ALLOW_FRACTION]
            );

            if ($value === null || $value === false || $value === '') {
                return 0.0;
            }

            return (float) $value;
        }

        function get_post_int(string $key): int
        {
            $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
            if ($value === null || $value === false) {
                return 0;
            }

            return (int) $value;
        }

        function get_get_param(string $key): string
        {
            $value = filter_input(
                INPUT_GET,
                $key,
                FILTER_UNSAFE_RAW,
                ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]
            );
            if ($value === null) {
                return '';
            }

            return sanitize_scalar_input($value);
        }
    }

    $kategoriAction = get_get_param('kategori');
    if ($kategoriAction !== '') {
        $nama = get_post_string('kategori');
        if ($nama === '') {
            echo '<script>alert("Kategori tidak valid");history.go(-1);</script>';
            exit;
        }

        $tgl = date('j F Y, G:i');
        $data = [$nama, $tgl];
        $sql = 'INSERT INTO kategori (nama_kategori,tgl_input) VALUES(?,?)';
        $row = $config->prepare($sql);
        $row->execute($data);
        redirect_with_fallback('../../index.php?page=kategori&&success=tambah-data');
    }

    $barangAction = get_get_param('barang');
    if ($barangAction !== '') {
        $id = get_post_string('id');
        if ($id === '' || !preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            echo '<script>alert("ID barang tidak valid");history.go(-1);</script>';
            exit;
        }

        $kategori = get_post_string('kategori');
        $nama = get_post_string('nama');
        $merk = get_post_string('merk');
        $beli = get_post_float('beli');
        $jual = get_post_float('jual');
        $satuan = get_post_string('satuan');
        $stok = get_post_int('stok');
        $tgl = get_post_string('tgl');

        $data = [
            $id,
            $kategori,
            $nama,
            $merk,
            $beli,
            $jual,
            $satuan,
            $stok,
            $tgl,
        ];

        $sql = 'INSERT INTO barang (id_barang,id_kategori,nama_barang,merk,harga_beli,harga_jual,satuan_barang,stok,tgl_input)
                            VALUES (?,?,?,?,?,?,?,?,?) ';
        $row = $config->prepare($sql);
        $row->execute($data);
        redirect_with_fallback('../../index.php?page=barang&success=tambah-data');
    }

    $jualAction = get_get_param('jual');
    if ($jualAction !== '') {
        csrf_require_token(get_get_param('csrf_token'));
        
        // Check if AJAX request
        $isAjax = (get_get_param('ajax') === '1') || 
                  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

        $id = get_get_param('id');
        if ($id === '' || !preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Barang tidak valid']);
                exit;
            }
            echo '<script>alert("Barang tidak valid");window.location="../../index.php?page=jual"</script>';
            exit;
        }

        $kasir = get_get_param('id_kasir');
        if ($kasir === '' || !ctype_digit($kasir)) {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Kasir tidak valid']);
                exit;
            }
            echo '<script>alert("Kasir tidak valid");window.location="../../index.php?page=jual"</script>';
            exit;
        }

        // get tabel barang id_barang
        $sql = 'SELECT * FROM barang WHERE id_barang = ?';
        $row = $config->prepare($sql);
        $row->execute([$id]);
        $hsl = $row->fetch();

        if ($hsl && (int) $hsl['stok'] > 0) {
            $jumlah = 1;
            $total = (float) $hsl['harga_jual'];
            $tgl = date('j F Y, G:i');

            $data1 = [$id, $kasir, $jumlah, $total, $tgl];

            $sql1 = 'INSERT INTO penjualan (id_barang,id_member,jumlah,total,tanggal_input) VALUES (?,?,?,?,?)';
            $row1 = $config->prepare($sql1);
            $row1->execute($data1);

            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Barang berhasil ditambahkan']);
                exit;
            }
            
            redirect_with_fallback('../../index.php?page=jual&success=tambah-data');
        } else {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Stok barang telah habis']);
                exit;
            }
            echo '<script>alert("Stok Barang Anda Telah Habis !");
                                        window.location="../../index.php?page=jual#keranjang"</script>';
        }
    }


    $userAction = get_get_param('user');
    if ($userAction !== '') {
        csrf_require_token(get_post_string('csrf_token')); // CSRF protection

        // Ambil data dari form
        $nama = get_post_string('nama');
        $role = get_post_string('role');
        $email = get_post_string('email');
        $tlp = get_post_string('tlp');
        $nik = get_post_string('nik');
        $alamat = get_post_string('alamat', true);
        $user = get_post_string('user');
        $pass = get_post_string('pass');

        if ($nama === '' || $role === '' || $user === '' || $pass === '') {
            echo '<script>alert("Form tidak lengkap");history.go(-1);</script>';
            exit;
        }

        // Mulai transaksi agar insert ke member & login konsisten
        $config->beginTransaction();

        try {
            // 1️⃣ Insert ke tabel member
            $sqlMember = 'INSERT INTO member (nm_member, role, email, telepon, NIK, alamat_member) 
                          VALUES (?,?,?,?,?,?)';
            $stmtMember = $config->prepare($sqlMember);
            $stmtMember->execute([$nama, $role, $email, $tlp, $nik, $alamat]);

            $id_member = $config->lastInsertId(); // ambil id member yang baru

            // 2️⃣ Insert ke tabel login
            // $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
            // $sqlLogin = 'INSERT INTO login (id_member, user, pass) VALUES (?,?,?)';
            // $stmtLogin = $config->prepare($sqlLogin);
            // $stmtLogin->execute([$id_member, $user, $passwordHash]);

            $passMd5 = md5($pass);
            $sqlLogin = 'INSERT INTO login (id_member, user, pass) VALUES (?,?,?)';
            $stmtLogin = $config->prepare($sqlLogin);
            $stmtLogin->execute([$id_member, $user, $passMd5]);

            $config->commit();

            redirect_with_fallback('../../index.php?page=kelola_user/list_user&success=tambah-data');
        } catch (Exception $e) {
            $config->rollBack();
            echo '<script>alert("Gagal menambahkan user: '.$e->getMessage().'");history.go(-1);</script>';
        }
    }

    // Tambah Customer
    $customerAction = get_get_param('customer');
    file_put_contents(__DIR__ . '/../../debug_tambah.log', "Customer action: " . json_encode($customerAction) . "\n", FILE_APPEND);
    
    if ($customerAction === 'tambah') {
        // Check if AJAX request
        $isAjax = (get_get_param('ajax') === '1') || 
                  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        
        file_put_contents(__DIR__ . '/../../debug_tambah.log', "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') . "\n", FILE_APPEND);
        
        // Validasi method harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "ERROR: Not POST method\n", FILE_APPEND);
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
                exit;
            }
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2>Error: Method Not Allowed</h2>';
            echo '<p>Halaman ini hanya bisa diakses melalui form submit.</p>';
            echo '<p><a href="../../index.php?page=customer">Kembali ke Halaman Customer</a></p>';
            echo '</body></html>';
            exit;
        }

        file_put_contents(__DIR__ . '/../../debug_tambah.log', "POST data: " . json_encode($_POST) . "\n", FILE_APPEND);
        csrf_require_token(get_post_string('csrf_token'));
        file_put_contents(__DIR__ . '/../../debug_tambah.log', "CSRF passed\n", FILE_APPEND);

        $nama_customer = get_post_string('nama_customer');
        $no_telepon = get_post_string('no_telepon');
        $alamat = get_post_string('alamat', true);
        $email = get_post_string('email');

        if ($nama_customer === '' || $no_telepon === '') {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Nama dan nomor telepon wajib diisi!']);
                exit;
            }
            echo '<script>alert("Nama dan nomor telepon wajib diisi!");history.go(-1);</script>';
            exit;
        }

        // Cek apakah nomor telepon sudah terdaftar
        $sqlCheck = "SELECT * FROM customer WHERE no_telepon = ?";
        $rowCheck = $config->prepare($sqlCheck);
        if (!$rowCheck) {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tabel customer belum tersedia. Jalankan update_database.sql terlebih dahulu.']);
                exit;
            }
            echo '<script>alert("Tabel customer belum tersedia. Jalankan update_database.sql terlebih dahulu.");window.location="../../index.php?page=customer"</script>';
            exit;
        }
        $rowCheck->execute([$no_telepon]);
        if ($rowCheck->fetch()) {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Nomor telepon sudah terdaftar!']);
                exit;
            }
            echo '<script>alert("Nomor telepon sudah terdaftar!");history.go(-1);</script>';
            exit;
        }

        $tgl_daftar = date('j F Y, G:i');
        $data = [$nama_customer, $no_telepon, $alamat, $email, $tgl_daftar];
        
        $sql = "INSERT INTO customer (nama_customer, no_telepon, alamat, email, tgl_daftar) VALUES (?,?,?,?,?)";
        $row = $config->prepare($sql);
        if (!$row) {
            $dbError = $config->errorInfo();
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tabel customer belum ada atau struktur tidak sesuai: ' . $dbError[2]]);
                exit;
            }
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2 style="color:red">Error Database</h2>';
            echo '<p><strong>Pesan:</strong> Tabel customer belum ada atau struktur tidak sesuai.</p>';
            echo '<p><strong>Detail Error:</strong> ' . htmlspecialchars($dbError[2]) . '</p>';
            echo '<p><strong>Solusi:</strong> Jalankan installer database dengan mengakses: ';
            echo '<a href="../../install_database.php">install_database.php</a></p>';
            echo '<p><a href="../../index.php?page=customer">← Kembali</a></p>';
            echo '</body></html>';
            exit;
        }
        
        try {
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "Executing SQL with data: " . json_encode($data) . "\n", FILE_APPEND);
            $row->execute($data);
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "SQL executed successfully\n", FILE_APPEND);
            
            // Get last insert ID
            $lastId = $config->lastInsertId();
            
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => 'Customer berhasil ditambahkan',
                    'id_customer' => $lastId,
                    'nama_customer' => $nama_customer
                ]);
                exit;
            }
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "SQL ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error saat menyimpan: ' . $e->getMessage()]);
                exit;
            }
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2 style="color:red">Error Saat Menyimpan</h2>';
            echo '<p><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Data yang dikirim:</strong></p><ul>';
            echo '<li>Nama: ' . htmlspecialchars($nama_customer) . '</li>';
            echo '<li>Telepon: ' . htmlspecialchars($no_telepon) . '</li>';
            echo '<li>Alamat: ' . htmlspecialchars($alamat) . '</li>';
            echo '<li>Email: ' . htmlspecialchars($email) . '</li></ul>';
            echo '<p><a href="../../index.php?page=customer">← Kembali</a></p>';
            echo '</body></html>';
            exit;
        }

        file_put_contents(__DIR__ . '/../../debug_tambah.log', "Before redirect\n", FILE_APPEND);
        // Check if it's from kasir page or customer page
        if (isset($_SESSION['return_to_kasir'])) {
            unset($_SESSION['return_to_kasir']);
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "Redirecting to kasir\n", FILE_APPEND);
            redirect_with_fallback('../../index.php?page=jual&success-add=customer');
        } else {
            file_put_contents(__DIR__ . '/../../debug_tambah.log', "Redirecting to customer\n", FILE_APPEND);
            redirect_with_fallback('../../index.php?page=customer&success-add=customer');
        }
    }
    
    file_put_contents(__DIR__ . '/../../debug_tambah.log', "End of actions - no matching action found\n", FILE_APPEND);
} elseif (!empty($_SESSION['kasir'])) {
    file_put_contents(__DIR__ . '/../../debug_tambah.log', "Kasir session redirect\n", FILE_APPEND);
    redirect_with_fallback('../../index.php?page=jual');
} elseif (!empty($_SESSION['owner'])) {
    file_put_contents(__DIR__ . '/../../debug_tambah.log', "Owner session redirect\n", FILE_APPEND);
    redirect_with_fallback('../../index.php?page=laporan');
} else {
    file_put_contents(__DIR__ . '/../../debug_tambah.log', "No session redirect\n", FILE_APPEND);
    redirect_with_fallback('../../index.php');
}

file_put_contents(__DIR__ . '/../../debug_tambah.log', "End of file reached\n", FILE_APPEND);