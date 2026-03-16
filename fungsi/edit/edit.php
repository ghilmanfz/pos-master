<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../debug_edit.log');

// Register shutdown function to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
        echo "<h1 style='color:red'>Fatal Error</h1>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($error['file']) . "</p>";
        echo "<p><strong>Line:</strong> " . $error['line'] . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><a href='../../index.php?page=customer'>← Kembali</a></p>";
        echo "</body></html>";
    }
});

// Start output buffering
ob_start();

session_start();
if (!empty($_SESSION['admin'])) {
    require '../../config.php';
    require_once __DIR__.'/../csrf.php';
    csrf_guard();
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
    }

    if (!function_exists('get_post_string')) {
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
    }

    if (!function_exists('get_post_float')) {
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
    }

    if (!function_exists('get_post_int')) {
        function get_post_int(string $key): int
        {
            $value = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
            if ($value === null || $value === false) {
                return 0;
            }

            return (int) $value;
        }
    }

    if (!function_exists('get_get_param')) {
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

    if (!function_exists('get_post_array')) {
        function get_post_array(string $key): array
        {
            $value = filter_input(
                INPUT_POST,
                $key,
                FILTER_DEFAULT,
                ['flags' => FILTER_REQUIRE_ARRAY]
            );
            if (!is_array($value)) {
                return [];
            }

            return array_map(static function ($item): string {
                return sanitize_scalar_input($item, true);
            }, $value);
        }
    }
    
    if (get_get_param('pengaturan') !== '') {
        // Validasi method harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2>Error: Method Not Allowed</h2>';
            echo '<p>Halaman ini hanya bisa diakses melalui form submit.</p>';
            echo '<p><a href="../../index.php?page=pengaturan">Kembali ke Halaman Pengaturan</a></p>';
            echo '</body></html>';
            exit;
        }

        $nama = get_post_string('namatoko');
        $alamat = get_post_string('alamat');
        $kontak = get_post_string('kontak');
        $pemilik = get_post_string('pemilik');
        $api_fonte_token = get_post_string('api_fonte_token');
        $api_fonte_phone = get_post_string('api_fonte_phone');
        $pesan_test = get_post_string('pesan_test', true);
        $reminder_aktif = get_post_string('reminder_aktif');
        if ($reminder_aktif !== 'ya' && $reminder_aktif !== 'tidak') {
            $reminder_aktif = 'tidak';
        }
        $interval_reminder = get_post_string('interval_reminder');
        if (!in_array($interval_reminder, ['1', '3', '7'])) {
            $interval_reminder = '3';
        }
        $id = '1';

        $availableColumns = [];
        $descStatement = $config->query('DESCRIBE toko');
        if ($descStatement) {
            $descRows = $descStatement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($descRows as $descRow) {
                if (!empty($descRow['Field'])) {
                    $availableColumns[$descRow['Field']] = true;
                }
            }
        }

        $updateParts = [
            'nama_toko=?',
            'alamat_toko=?',
            'tlp=?',
            'nama_pemilik=?'
        ];
        $data = [$nama, $alamat, $kontak, $pemilik];

        if (isset($availableColumns['api_fonte_token'])) {
            $updateParts[] = 'api_fonte_token=?';
            $data[] = $api_fonte_token;
        }
        if (isset($availableColumns['api_fonte_phone'])) {
            $updateParts[] = 'api_fonte_phone=?';
            $data[] = $api_fonte_phone;
        }
        if (isset($availableColumns['pesan_test'])) {
            $updateParts[] = 'pesan_test=?';
            $data[] = $pesan_test;
        }
        if (isset($availableColumns['reminder_aktif'])) {
            $updateParts[] = 'reminder_aktif=?';
            $data[] = $reminder_aktif;
        }
        if (isset($availableColumns['interval_reminder'])) {
            $updateParts[] = 'interval_reminder=?';
            $data[] = $interval_reminder;
        }

        $data[] = $id;
        $sql = 'UPDATE toko SET '.implode(', ', $updateParts).' WHERE id_toko = ?';
        $row = $config->prepare($sql);
        if (!$row) {
            $dbError = $config->errorInfo();
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2 style="color:red">Error Database</h2>';
            echo '<p><strong>Pesan:</strong> Gagal menyiapkan query update.</p>';
            echo '<p><strong>Detail Error:</strong> ' . htmlspecialchars($dbError[2]) . '</p>';
            echo '<p><a href="../../index.php?page=pengaturan">← Kembali</a></p>';
            echo '</body></html>';
            exit;
        }
        
        try {
            $row->execute($data);
        } catch (Exception $e) {
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2 style="color:red">Error Saat Menyimpan Pengaturan</h2>';
            echo '<p><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Query:</strong> ' . htmlspecialchars($sql) . '</p>';
            echo '<p><a href="../../index.php?page=pengaturan">← Kembali</a></p>';
            echo '</body></html>';
            exit;
        }
        redirect_with_fallback('../../index.php?page=pengaturan&success=edit-data');
    }

    // Edit Customer
    if (get_get_param('customer') === 'update') {
        // Validasi method harus POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo '<!doctype html><html><head><meta charset="utf-8"></head><body>';
            echo '<h2>Error: Method Not Allowed</h2>';
            echo '<p>Halaman ini hanya bisa diakses melalui form submit.</p>';
            echo '<p><a href="../../index.php?page=customer">Kembali ke Halaman Customer</a></p>';
            echo '</body></html>';
            exit;
        }

        csrf_require_token(get_post_string('csrf_token'));

        $id_customer = get_post_int('id_customer');
        $nama_customer = get_post_string('nama_customer');
        $no_telepon = get_post_string('no_telepon');
        $alamat = get_post_string('alamat', true);
        $email = get_post_string('email');
        $poin_diskon = get_post_int('poin_diskon');
        $status = get_post_string('status');
        $reminder_aktif = get_post_string('reminder_aktif');
        $reminder_interval = get_post_int('reminder_interval');
        $pesan_custom = get_post_string('pesan_custom', true);
        
        // Jika reminder_interval 0 atau kosong, set ke NULL
        if ($reminder_interval <= 0) {
            $reminder_interval = null;
        }
        
        // Jika pesan_custom kosong, set ke NULL
        if ($pesan_custom === '') {
            $pesan_custom = null;
        }
        
        // Set default reminder_aktif jika kosong
        if ($reminder_aktif === '') {
            $reminder_aktif = 'ya';
        }

        if ($id_customer <= 0 || $nama_customer === '' || $no_telepon === '') {
            echo '<script>alert("Data tidak lengkap!");history.go(-1);</script>';
            exit;
        }

        // Cek apakah nomor telepon sudah digunakan customer lain
        $sqlCheck = "SELECT * FROM customer WHERE no_telepon = ? AND id_customer != ?";
        $rowCheck = $config->prepare($sqlCheck);
        if (!$rowCheck) {
            echo '<script>alert("Tabel customer belum tersedia. Jalankan update_database.sql terlebih dahulu.");window.location="../../index.php?page=customer"</script>';
            exit;
        }
        $rowCheck->execute([$no_telepon, $id_customer]);
        if ($rowCheck->fetch()) {
            echo '<script>alert("Nomor telepon sudah digunakan customer lain!");history.go(-1);</script>';
            exit;
        }

        $data = [$nama_customer, $no_telepon, $alamat, $email, $poin_diskon, $status, $reminder_aktif, $reminder_interval, $pesan_custom, $id_customer];
        $sql = "UPDATE customer SET nama_customer=?, no_telepon=?, alamat=?, email=?, poin_diskon=?, status=?, reminder_aktif=?, reminder_interval=?, pesan_custom=? WHERE id_customer=?";
        $row = $config->prepare($sql);
        if (!$row) {
            echo '<script>alert("Gagal mengubah customer. Pastikan struktur database sudah diperbarui.");window.location="../../index.php?page=customer"</script>';
            exit;
        }
        $row->execute($data);
        redirect_with_fallback('../../index.php?page=customer&success=edit-data');
    }

    if (get_get_param('kategori') !== '') {
        $nama = get_post_string('kategori');
        $id = get_post_string('id');
        if ($id === '' || !ctype_digit($id)) {
            echo '<script>alert("Data kategori tidak valid");history.go(-1);</script>';
            exit;
        }

        $data = [$nama, $id];
        $sql = 'UPDATE kategori SET  nama_kategori=? WHERE id_kategori=?';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../index.php?page=kategori&uid='.$id.'&success-edit=edit-data"</script>';
    }

    if (get_get_param('stok') !== '') {
        $restok = get_post_int('restok');
        $id = get_post_string('id');
        if ($id === '' || !preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            echo '<script>alert("Barang tidak valid");history.go(-1);</script>';
            exit;
        }

        $sqlS = 'select*from barang WHERE id_barang=?';
        $rowS = $config->prepare($sqlS);
        $rowS->execute([$id]);
        $hasil = $rowS->fetch();

        $stok = $restok + (int) ($hasil['stok'] ?? 0);

        $sql = 'UPDATE barang SET stok=? WHERE id_barang=?';
        $row = $config->prepare($sql);
        $row->execute([$stok, $id]);
        echo '<script>window.location="../../index.php?page=barang&success-stok=stok-data"</script>';
    }

    if (get_get_param('barang') !== '') {
        $id = get_post_string('id');
        if ($id === '' || !preg_match('/^[A-Za-z0-9-]+$/', $id)) {
            echo '<script>alert("Barang tidak valid");history.go(-1);</script>';
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

        $data = [$kategori, $nama, $merk, $beli, $jual, $satuan, $stok, $tgl, $id];
        $sql = 'UPDATE barang SET id_kategori=?, nama_barang=?, merk=?,
                                harga_beli=?, harga_jual=?, satuan_barang=?, stok=?, tgl_update=?  WHERE id_barang=?';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../index.php?page=barang/edit&barang='.$id.'&success=edit-data"</script>';
    }

    if (get_get_param('gambar') !== '') {
        $id = get_post_int('id');
        if ($id <= 0) {
            echo '<script>alert("Data pengguna tidak valid");history.go(-1);</script>';
            exit;
        }
        set_time_limit(0);
        if (!isset($_FILES['foto']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
            echo '<script>alert("Masukan Gambar !");window.location="../../index.php?page=user"</script>';
            exit;
        }

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            echo '<script>alert("You can only upload JPG, PNG and GIF file");window.location="../../index.php?page=user"</script>';
            exit;
        }

        $allowedTypes = [
            'image/png'   => 'png',
            'image/jpeg'  => 'jpg',
            'image/gif'   => 'gif',
            'image/jpg'   => 'jpeg',
            'image/webp'  => 'webp'
        ];

        $tmpName = $_FILES['foto']['tmp_name'];
        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$fileinfo) {
            echo '<script>alert("You can only upload JPG, PNG and GIF file");window.location="../../index.php?page=user"</script>';
            exit;
        }

        $filetype = finfo_file($fileinfo, $tmpName);
        finfo_close($fileinfo);

        if (!isset($allowedTypes[$filetype])) {
            echo '<script>alert("You can only upload JPG, PNG and GIF file");window.location="../../index.php?page=user"</script>';
            exit;
        }

        if (round($_FILES['foto']["size"] / 1024) > 4096) {
            echo '<script>alert("WARNING !!! Besar Gambar Tidak Boleh Lebih Dari 4 MB");window.location="../../index.php?page=user"</script>';
            exit;
        }

        $uploadDir = realpath(__DIR__.'/../../assets/img/user');
        if ($uploadDir === false) {
            echo '<script>alert("Masukan Gambar !");window.location="../../index.php?page=user"</script>';
            exit;
        }

        $uploadDir .= DIRECTORY_SEPARATOR;
        $name = time().'_'.bin2hex(random_bytes(8)).'.'.$allowedTypes[$filetype];

        if (move_uploaded_file($tmpName, $uploadDir.$name)) {
            $foto2Raw = get_post_string('foto2');
            $foto2 = $foto2Raw !== '' ? basename($foto2Raw) : '';
            if ($foto2 !== '') {
                $oldFile = $uploadDir.$foto2;
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $data = [$name, $id];
            $sql = 'UPDATE member SET gambar=?  WHERE member.id_member=?';
            $row = $config->prepare($sql);
            $row->execute($data);
            echo '<script>window.location="../../index.php?page=user&success=edit-data"</script>';
        } else {
            echo '<script>alert("Masukan Gambar !");window.location="../../index.php?page=user"</script>';
            exit;
        }
    }

    if (get_get_param('profil') !== '') {
        $id = get_post_int('id');
        if ($id <= 0) {
            echo '<script>alert("Data pengguna tidak valid");history.go(-1);</script>';
            exit;
        }

        $nama = get_post_string('nama');
        $alamat = get_post_string('alamat', true);
        $tlp = get_post_string('tlp');
        $email = get_post_string('email');
        $nik = get_post_string('nik');
        $role = get_post_string('role');

        $data = [$nama, $alamat, $tlp, $email, $nik, $role, $id];
        $sql = 'UPDATE member SET nm_member=?,alamat_member=?,telepon=?,email=?,NIK=?,role=? WHERE id_member=?';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../index.php?page=user&success=edit-data"</script>';
    }
    
    if (get_get_param('pass') !== '') {
        $id = get_post_int('id');
        if ($id <= 0) {
            echo '<script>alert("Data pengguna tidak valid");history.go(-1);</script>';
            exit;
        }

        $user = get_post_string('user');
        $pass = get_post_string('pass');

        $data = [$user, $pass, $id];
        $sql = 'UPDATE login SET user=?,pass=md5(?) WHERE id_member=?';
        $row = $config->prepare($sql);
        $row->execute($data);
        echo '<script>window.location="../../index.php?page=user&success=edit-data"</script>';
    }

    if (get_get_param('jual') !== '') {
        // Check if AJAX request
        $isAjax = (get_get_param('ajax') === '1') || 
                  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        
        $id = get_post_int('id');
        $id_barang = get_post_string('id_barang');
        $jumlah = get_post_int('jumlah');

        if ($id <= 0 || $id_barang === '' || !preg_match('/^[A-Za-z0-9-]+$/', $id_barang) || $jumlah <= 0) {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Data penjualan tidak valid']);
                exit;
            }
            echo '<script>alert("Data penjualan tidak valid");history.go(-1);</script>';
            exit;
        }

        $sql_tampil = 'select *from barang where barang.id_barang=?';
        $row_tampil = $config->prepare($sql_tampil);
        $row_tampil->execute([$id_barang]);
        $hasil = $row_tampil->fetch();

        if ($hasil && (int) $hasil['stok'] > $jumlah) {
            $jual = (float) $hasil['harga_jual'];
            $total = $jual * $jumlah;
            $data1 = [$jumlah, $total, $id];
            $sql1 = 'UPDATE penjualan SET jumlah=?,total=? WHERE id_penjualan=?';
            $row1 = $config->prepare($sql1);
            $row1->execute($data1);
            
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Jumlah berhasil diupdate']);
                exit;
            }
            
            echo '<script>window.location="../../index.php?page=jual#keranjang"</script>';
        } else {
            if ($isAjax) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Keranjang melebihi stok barang']);
                exit;
            }
            echo '<script>alert("Keranjang Melebihi Stok Barang Anda !");
                                        window.location="../../index.php?page=jual#keranjang"</script>';
        }
    }

    if (!empty($_GET['cari_barang'])) {
        $cari = trim((string) filter_input(INPUT_POST, 'keyword', FILTER_UNSAFE_RAW, FILTER_FLAG_NO_ENCODE_QUOTES));
        if ($cari !== '') {
            $param = "%{$cari}%";
            $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                                        from barang inner join kategori on barang.id_kategori = kategori.id_kategori
                                        where barang.id_barang like ? or barang.nama_barang like ? or barang.merk like ?";
            $row = $config -> prepare($sql);
            $row -> execute(array($param, $param, $param));
            $hasil1= $row -> fetchAll();
            ?>
                <table class="table table-stripped" width="100%" id="example2">
                        <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                        </tr>
                <?php foreach ($hasil1 as $hasil) {
                    $tambahUrl = "fungsi/tambah/tambah.php?jual=jual&id=" . urlencode($hasil['id_barang']) . 
                                 "&id_kasir=" . urlencode($_SESSION['admin']['id_member']) . 
                                 "&csrf_token=" . urlencode(csrf_get_token());
                ?>
                        <tr>
                                <td><?php echo htmlspecialchars($hasil['id_barang'], ENT_QUOTES, 'UTF-8');?></td>
                                <td><?php echo htmlspecialchars($hasil['nama_barang'], ENT_QUOTES, 'UTF-8');?></td>
                                <td><?php echo htmlspecialchars($hasil['merk'], ENT_QUOTES, 'UTF-8');?></td>
                                <td>Rp <?php echo number_format($hasil['harga_jual'], 0, ',', '.');?>,-</td>
                                <td>
                                <button type="button" 
                                    class="btn btn-success btn-tambah-barang" 
                                    data-url="<?php echo htmlspecialchars($tambahUrl, ENT_QUOTES, 'UTF-8');?>"
                                    data-nama="<?php echo htmlspecialchars($hasil['nama_barang'], ENT_QUOTES, 'UTF-8');?>">
                                    <i class="fa fa-shopping-cart"></i> Tambah
                                </button>
                                </td>
                        </tr>
                <?php }?>
                </table>
<?php
        }
    }
    
    // AJAX Endpoint untuk proses pembayaran
    if (!empty($_POST['proses_bayar_ajax'])) {
        ob_clean();
        header('Content-Type: application/json');
        
        try {
            $totalInput = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
            $bayarInput = filter_input(INPUT_POST, 'bayar', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
            $customerIdInput = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
            $diskonPersenInput = filter_input(INPUT_POST, 'diskon_persen', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
            $diskonNominalInput = filter_input(INPUT_POST, 'diskon_nominal', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
            $totalAkhirInput = filter_input(INPUT_POST, 'total_akhir', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
            $poinDigunakanInput = filter_input(INPUT_POST, 'poin_digunakan', FILTER_VALIDATE_INT);
            
            $total = is_numeric($totalInput) ? (float) $totalInput : 0.0;
            $bayar = is_numeric($bayarInput) ? (float) $bayarInput : 0.0;
            $customerId = ($customerIdInput !== null && $customerIdInput !== false) ? (int) $customerIdInput : 0;
            $diskon_persen = is_numeric($diskonPersenInput) ? (float) $diskonPersenInput : 0.0;
            $diskon_nominal = is_numeric($diskonNominalInput) ? (float) $diskonNominalInput : 0.0;
            $total_akhir = is_numeric($totalAkhirInput) ? (float) $totalAkhirInput : ($total - $diskon_nominal);
            $poin_digunakan = ($poinDigunakanInput !== null && $poinDigunakanInput !== false) ? (int) $poinDigunakanInput : 0;
            
            if($bayar <= 0.0) {
                echo json_encode(['success' => false, 'message' => 'Masukkan jumlah bayar terlebih dahulu!']);
                exit;
            }
            
            if($bayar < $total_akhir) {
                $kurang = $total_akhir - $bayar;
                echo json_encode(['success' => false, 'message' => 'Uang Kurang! Rp ' . number_format($kurang, 0, ',', '.')]);
                exit;
            }
            
            $hitung = $bayar - $total_akhir;  // Kembalian
            $idBarangList = filter_input(INPUT_POST, 'id_barang', FILTER_DEFAULT, ['flags' => FILTER_REQUIRE_ARRAY]);
            $idMemberList = filter_input(INPUT_POST, 'id_member', FILTER_DEFAULT, ['flags' => FILTER_REQUIRE_ARRAY]);
            $jumlahList = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT, ['flags' => FILTER_REQUIRE_ARRAY]);
            $totalList = filter_input(INPUT_POST, 'total1', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_ALLOW_FRACTION]);
            $tglInputList = filter_input(INPUT_POST, 'tgl_input', FILTER_UNSAFE_RAW, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES]);
            $periodeList = filter_input(INPUT_POST, 'periode', FILTER_UNSAFE_RAW, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES]);
            
            $jumlahDipilih = is_array($idBarangList) ? count($idBarangList) : 0;
            
            if($jumlahDipilih == 0) {
                echo json_encode(['success' => false, 'message' => 'Keranjang kosong!']);
                exit;
            }
            
            for($x = 0; $x < $jumlahDipilih; $x++) {
                $barangId = '';
                if (is_array($idBarangList) && isset($idBarangList[$x]) && is_string($idBarangList[$x]) && preg_match('/^[A-Za-z0-9-]+$/', $idBarangList[$x])) {
                    $barangId = $idBarangList[$x];
                }
                
                $memberId = (is_array($idMemberList) && isset($idMemberList[$x])) ? (int) $idMemberList[$x] : 0;
                $jumlahItem = (is_array($jumlahList) && isset($jumlahList[$x]) && $jumlahList[$x] !== false) ? (int) $jumlahList[$x] : 0;
                $totalItem = (is_array($totalList) && isset($totalList[$x]) && $totalList[$x] !== false && $totalList[$x] !== '') ? (float) $totalList[$x] : 0.0;
                $tglInputItem = (is_array($tglInputList) && isset($tglInputList[$x])) ? trim((string) $tglInputList[$x]) : '';
                $periodeItem = (is_array($periodeList) && isset($periodeList[$x])) ? trim((string) $periodeList[$x]) : '';
                
                if ($barangId === '' || $memberId <= 0 || $jumlahItem <= 0 || $tglInputItem === '' || $periodeItem === '') {
                    continue;
                }
                
                // Hitung total_akhir per item (proporsi dari total akhir transaksi)
                $totalAkhirItem = $total > 0 ? ($totalItem / $total) * $total_akhir : $totalItem;
                
                $d = array($barangId, $memberId, $customerId, $jumlahItem, $totalItem, $diskon_persen, $diskon_nominal, $totalAkhirItem, $bayar, $hitung, $tglInputItem, $periodeItem);
                $sql = "INSERT INTO nota (id_barang,id_member,id_customer,jumlah,total,diskon_persen,diskon_nominal,total_akhir,bayar,kembalian,tanggal_input,periode) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
                $row = $config->prepare($sql);
                $row->execute($d);
                
                $sql_barang = "SELECT * FROM barang WHERE id_barang = ?";
                $row_barang = $config->prepare($sql_barang);
                $row_barang->execute(array($barangId));
                $hsl = $row_barang->fetch();
                
                if ($hsl) {
                    $stok = (int) $hsl['stok'];
                    $idb  = $hsl['id_barang'];
                    
                    $total_stok = $stok - $jumlahItem;
                    $sql_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
                    $row_stok = $config->prepare($sql_stok);
                    $row_stok->execute(array($total_stok, $idb));
                    
                    // Update customer_barang untuk tracking
                    if($customerId > 0) {
                        $sqlCheck = "SELECT * FROM customer_barang WHERE id_customer = ? AND id_barang = ?";
                        $rowCheck = $config->prepare($sqlCheck);
                        $rowCheck->execute([$customerId, $barangId]);
                        $existing = $rowCheck->fetch();
                        
                        if($existing) {
                            $freq = (int)$existing['frekuensi_beli'] + 1;
                            $sqlUpdate = "UPDATE customer_barang SET terakhir_beli = NOW(), frekuensi_beli = ?, jumlah_terakhir = ? WHERE id_customer = ? AND id_barang = ?";
                            $rowUpdate = $config->prepare($sqlUpdate);
                            $rowUpdate->execute([$freq, $jumlahItem, $customerId, $barangId]);
                        } else {
                            $sqlInsert = "INSERT INTO customer_barang (id_customer, id_barang, terakhir_beli, frekuensi_beli, jumlah_terakhir) VALUES (?, ?, NOW(), 1, ?)";
                            $rowInsert = $config->prepare($sqlInsert);
                            $rowInsert->execute([$customerId, $barangId, $jumlahItem]);
                        }
                    }
                }
            }
            
            // Update total belanja customer dan kurangi poin
            if($customerId > 0) {
                $sqlUpdateCustomer = "UPDATE customer SET total_belanja = total_belanja + ?, poin_diskon = poin_diskon - ? WHERE id_customer = ?";
                $rowUpdateCustomer = $config->prepare($sqlUpdateCustomer);
                $rowUpdateCustomer->execute([$total_akhir, $poin_digunakan, $customerId]);
            }
            
            $nmMember = isset($_SESSION['admin']['nm_member']) ? $_SESSION['admin']['nm_member'] : '';
            
            echo json_encode([
                'success' => true, 
                'message' => 'Pembayaran berhasil!',
                'bayar' => $bayar,
                'kembalian' => $hitung,
                'nm_member' => $nmMember
            ]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            exit;
        }
    }
}
