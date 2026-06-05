<?php
/*
  | Source Code Aplikasi Penjualan Barang Kasir dengan PHP & MYSQL
  | 
  | @package   : pos-kasir-php
  | @file	   : config.php ( untuk mengatur koneksi php ke database mysql )
  | @author    : fauzan1892 / Fauzan Falah
  | @copyright : Copyright (c) 2017-2021 Codekop.com (https://www.codekop.com)
  | @blog      : https://www.codekop.com/read/source-code-aplikasi-penjualan-barang-kasir-dengan-php-amp-mysql-gratis.html
  |
  | 
  | keterangan : untuk login aplikasi dengan username : admin dan password : 123
  | 
  | 
  | 
 */

date_default_timezone_set("Asia/Jakarta");
// error_reporting(0); // Commented out untuk debugging - enable error display
error_reporting(E_ALL); // Show all errors untuk debugging
ini_set('display_errors', '1');

	// sesuaikan dengan server anda
	$host 	= 'localhost'; // host server
	$user 	= 'root';  // username server
	$pass 	= ''; // password server, kalau pakai xampp kosongin saja
	$dbname = 'db_toko'; // nama database anda
	
	try{
		$config = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user,$pass);
		$config->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$config->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		//echo 'sukses';
	}catch(PDOException $e){
		echo 'KONEKSI GAGAL' .$e -> getMessage();
	}
	


if (!function_exists('pos_column_exists')) {
    function pos_column_exists(PDO $pdo, string $table, string $column): bool
    {
        try {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            return (bool) $stmt->fetch();
        } catch (Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('pos_ensure_schema')) {
    function pos_ensure_schema(PDO $pdo): void
    {
        $alterations = [
            ['toko', 'diskon_member_persen', "ALTER TABLE `toko` ADD COLUMN `diskon_member_persen` DECIMAL(5,2) NOT NULL DEFAULT 2.00 AFTER `min_stok`"],
            ['nota', 'no_transaksi', "ALTER TABLE `nota` ADD COLUMN `no_transaksi` VARCHAR(40) DEFAULT NULL AFTER `id_nota`"],
            ['nota', 'diskon_member_nominal', "ALTER TABLE `nota` ADD COLUMN `diskon_member_nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `diskon_nominal`"],
            ['nota', 'diskon_poin_nominal', "ALTER TABLE `nota` ADD COLUMN `diskon_poin_nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `diskon_member_nominal`"],
            ['nota', 'poin_digunakan', "ALTER TABLE `nota` ADD COLUMN `poin_digunakan` INT(11) NOT NULL DEFAULT 0 AFTER `kembalian`"],
            ['nota', 'poin_didapat', "ALTER TABLE `nota` ADD COLUMN `poin_didapat` INT(11) NOT NULL DEFAULT 0 AFTER `poin_digunakan`"],
            ['nota', 'poin_akhir', "ALTER TABLE `nota` ADD COLUMN `poin_akhir` INT(11) DEFAULT NULL AFTER `poin_didapat`"],
            ['penjualan', 'no_transaksi', "ALTER TABLE `penjualan` ADD COLUMN `no_transaksi` VARCHAR(40) DEFAULT NULL AFTER `id_penjualan`"],
        ];

        foreach ($alterations as [$table, $column, $sql]) {
            if (!pos_column_exists($pdo, $table, $column)) {
                try {
                    $pdo->exec($sql);
                } catch (Throwable $e) {
                    // Biarkan aplikasi tetap berjalan jika user DB tidak punya izin ALTER.
                }
            }
        }
    }
}

if (isset($config) && $config instanceof PDO) {
    pos_ensure_schema($config);
}

	$view = 'fungsi/view/view.php'; // direktori fungsi select data
?>

