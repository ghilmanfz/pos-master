-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 04:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `id_barang` varchar(255) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` text NOT NULL,
  `merk` varchar(255) NOT NULL,
  `harga_beli` varchar(255) NOT NULL,
  `harga_jual` varchar(255) NOT NULL,
  `satuan_barang` varchar(255) NOT NULL,
  `stok` text NOT NULL,
  `tgl_input` varchar(255) NOT NULL,
  `tgl_update` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `id_barang`, `id_kategori`, `nama_barang`, `merk`, `harga_beli`, `harga_jual`, `satuan_barang`, `stok`, `tgl_input`, `tgl_update`) VALUES
(4, 'BR001', 10, 'Tabung Gas 3 Kg', '-', '18000', '20000', 'PCS', '9', '27 September 2025, 17:50', '27 September 2025, 17:54'),
(5, 'BR002', 10, 'Tabung LPG 5,5 kg', 'Bright gas', '45000', '50000', 'PCS', '4', '27 September 2025, 17:54', NULL),
(6, 'BR003', 10, 'Tabung LPG 12 kg', 'Biru', '75000', '80000', 'PCS', '10', '27 September 2025, 17:55', NULL),
(7, 'BR004', 10, 'Tabung LPG 12 Kg', 'pink bright gas', '75000', '80000', 'PCS', '5', '27 September 2025, 17:55', NULL),
(8, 'BR005', 15, 'Kompor', 'Miyako', '120000', '130000', 'PCS', '7', '27 September 2025, 17:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_customer` int(11) NOT NULL,
  `nama_customer` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `poin_diskon` int(11) DEFAULT 0,
  `total_belanja` decimal(15,2) DEFAULT 0.00,
  `tgl_daftar` varchar(255) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `reminder_aktif` enum('ya','tidak') DEFAULT NULL,
  `reminder_interval` int(11) DEFAULT NULL,
  `pesan_custom` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_customer`, `nama_customer`, `no_telepon`, `alamat`, `email`, `poin_diskon`, `total_belanja`, `tgl_daftar`, `status`, `reminder_aktif`, `reminder_interval`, `pesan_custom`) VALUES
(1, 'Customer Umum', '08978223198', '-', 'elincayangfikri@gmail.com', 0, 416800.00, '16 February 2026', 'aktif', 'tidak', NULL, NULL),
(3, 'Ghilman', '081289102568', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'tets@gmail.com', 0, 0.00, '16 February 2026, 20:13', 'aktif', NULL, NULL, NULL),
(4, 'Raihan', '08589214765', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'admin@gmail.com', 0, 0.00, '16 February 2026, 20:14', 'aktif', NULL, NULL, NULL),
(5, 'nama', '0897822319', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'supandi.subur@gmail.com', 0, 0.00, '16 February 2026, 23:53', 'aktif', NULL, NULL, NULL),
(7, 'kepo', '0897777', 'kepo', 'a@a.com', 0, 98000.00, '17 February 2026, 13:22', 'aktif', NULL, NULL, NULL),
(10, 'Pak Budi Santoso', '628123456789', 'Jl. Merdeka No. 45, Jakarta Selatan', 'budi.santoso@gmail.com', 5, 90000.00, '15 February 2026, 10:30', 'aktif', 'ya', 2, 'Yth. Pak {nama}, produk {barang} sudah ready stock! Langsung order ke {toko} di {phone}'),
(11, 'Ibu Siti Nurhaliza', '628234567890', 'Jl. Sudirman No. 12, Jakarta Pusat', 'siti.nurhaliza@yahoo.com', 3, 100000.00, '16 February 2026, 11:45', 'aktif', 'ya', NULL, NULL),
(12, 'Pak Ahmad Wijaya', '628345678901', 'Jl. Gatot Subroto No. 88, Tangerang', 'ahmad.wijaya@hotmail.com', 8, 230000.00, '17 February 2026, 09:20', 'aktif', 'ya', 3, NULL),
(13, 'Ibu Dewi Lestari', '628456789012', 'Jl. Asia Afrika No. 23, Bandung', 'dewi.lestari@gmail.com', 2, 100000.00, '18 February 2026, 14:15', 'aktif', 'ya', NULL, 'Halo Ibu {nama}, kabar baik! {barang} kembali tersedia dengan harga {harga}. Info: {phone}'),
(14, 'Pak Hendra Gunawan', '628567890123', 'Jl. Diponegoro No. 67, Surabaya', 'hendra.g@gmail.com', 10, 420000.00, '19 February 2026, 08:00', 'aktif', 'ya', 5, NULL),
(15, 'Ibu Rina Kusuma', '628678901234', 'Jl. Pahlawan No. 34, Semarang', 'rina.kusuma@outlook.com', 4, 90000.00, '20 February 2026, 13:30', 'aktif', 'ya', NULL, NULL),
(16, 'Pak Dedi Firmansyah', '628789012345', 'Jl. Ahmad Yani No. 56, Bekasi', 'dedi.firman@gmail.com', 6, 160000.00, '21 February 2026, 10:45', 'aktif', 'ya', 7, NULL),
(17, 'Ibu Maya Sari', '628890123456', 'Jl. Veteran No. 78, Depok', 'maya.sari@yahoo.com', 1, 20000.00, '22 February 2026, 15:20', 'aktif', 'tidak', NULL, NULL),
(18, 'Pak Eko Prasetyo', '628901234567', 'Jl. Kartini No. 90, Bogor', 'eko.prasetyo@gmail.com', 7, 310000.00, '23 February 2026, 09:15', 'aktif', 'ya', 2, NULL),
(19, 'Ibu Linda Wijayanti', '628012345678', 'Jl. Pemuda No. 12, Yogyakarta', 'linda.w@hotmail.com', 5, 120000.00, '24 February 2026, 11:00', 'aktif', 'ya', NULL, NULL),
(20, 'Pak Agus Salim', '628111111111', 'Jl. Gajah Mada No. 45, Malang', 'agus.salim@gmail.com', 9, 260000.00, '25 February 2026, 08:30', 'aktif', 'ya', 4, NULL),
(21, 'Ibu Fitri Handayani', '628222222222', 'Jl. Hayam Wuruk No. 23, Solo', 'fitri.h@yahoo.com', 3, 150000.00, '26 February 2026, 14:00', 'aktif', 'ya', NULL, NULL),
(22, 'Pak Bambang Susilo', '628333333333', 'Jl. Thamrin No. 67, Medan', 'bambang.s@gmail.com', 11, 310000.00, '27 February 2026, 10:15', 'aktif', 'ya', 3, 'Pak {nama}, {barang} ready! Hubungi {toko} segera di {phone}'),
(23, 'Ibu Yuli Astuti', '628444444444', 'Jl. Sisingamangaraja No. 89, Palembang', 'yuli.astuti@outlook.com', 2, 100000.00, '28 February 2026, 12:45', 'aktif', 'ya', NULL, NULL),
(24, 'Pak Rudi Hartono', '628555555555', 'Jl. Imam Bonjol No. 34, Makassar', 'rudi.hartono@gmail.com', 8, 300000.00, '01 March 2026, 09:00', 'aktif', 'ya', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_barang`
--

CREATE TABLE `customer_barang` (
  `id_cb` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_barang` varchar(255) NOT NULL,
  `terakhir_beli` datetime DEFAULT NULL,
  `frekuensi_beli` int(11) DEFAULT 1,
  `jumlah_terakhir` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer_barang`
--

INSERT INTO `customer_barang` (`id_cb`, `id_customer`, `id_barang`, `terakhir_beli`, `frekuensi_beli`, `jumlah_terakhir`) VALUES
(1, 1, 'BR001', '2026-02-17 00:32:58', 2, 1),
(2, 1, 'BR002', '2026-02-17 00:20:17', 2, 1),
(3, 1, 'BR004', '2026-02-17 00:20:17', 2, 1),
(4, 1, 'BR005', '2026-02-17 00:32:58', 1, 1),
(5, 7, 'BR001', '2026-02-17 13:23:20', 1, 1),
(6, 7, 'BR004', '2026-02-17 13:23:20', 1, 1),
(10, 10, 'BR001', '2026-02-15 10:45:00', 2, 2),
(11, 10, 'BR002', '2026-02-15 10:45:00', 1, 1),
(12, 11, 'BR001', '2026-02-16 11:50:00', 1, 1),
(13, 11, 'BR004', '2026-02-16 11:50:00', 1, 1),
(14, 12, 'BR002', '2026-02-17 09:30:00', 2, 2),
(15, 12, 'BR005', '2026-02-17 09:30:00', 1, 1),
(16, 13, 'BR001', '2026-02-18 14:20:00', 1, 1),
(17, 13, 'BR003', '2026-02-18 14:20:00', 1, 1),
(18, 14, 'BR004', '2026-02-19 08:15:00', 2, 2),
(19, 14, 'BR005', '2026-02-19 08:15:00', 2, 2),
(20, 15, 'BR001', '2026-02-20 13:40:00', 2, 2),
(21, 15, 'BR002', '2026-02-20 13:40:00', 1, 1),
(22, 16, 'BR003', '2026-02-21 10:50:00', 1, 1),
(23, 16, 'BR004', '2026-02-21 10:50:00', 1, 1),
(24, 17, 'BR001', '2026-02-22 15:25:00', 1, 1),
(25, 18, 'BR005', '2026-02-23 09:20:00', 2, 2),
(26, 18, 'BR002', '2026-02-23 09:20:00', 1, 1),
(27, 19, 'BR001', '2026-02-24 11:10:00', 2, 2),
(28, 19, 'BR003', '2026-02-24 11:10:00', 1, 1),
(29, 20, 'BR004', '2026-02-25 08:35:00', 2, 2),
(30, 20, 'BR002', '2026-02-25 08:35:00', 2, 2),
(31, 21, 'BR001', '2026-02-26 14:05:00', 1, 1),
(32, 21, 'BR005', '2026-02-26 14:05:00', 1, 1),
(33, 22, 'BR002', '2026-02-27 10:20:00', 3, 3),
(34, 22, 'BR004', '2026-02-27 10:20:00', 2, 2),
(35, 23, 'BR001', '2026-02-28 12:50:00', 1, 1),
(36, 23, 'BR003', '2026-02-28 12:50:00', 1, 1),
(37, 24, 'BR005', '2026-03-01 09:05:00', 2, 2),
(38, 24, 'BR001', '2026-03-01 09:05:00', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `tgl_input` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `tgl_input`) VALUES
(10, 'Tabung Gas', '27 September 2025, 17:46'),
(15, 'Perlengkapan Gas', '27 September 2025, 17:47'),
(19, 'Galon', '27 September 2025, 17:48');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` char(32) NOT NULL,
  `id_member` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id_login`, `user`, `pass`, `id_member`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(11, 'viewer09', '0df7410f4450355fbf46898f7e75c674', 11),
(13, 'kasir', 'c7911af3adbd12a035b289556d96470a', 13);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id_member` int(11) NOT NULL,
  `nm_member` varchar(255) NOT NULL,
  `alamat_member` text NOT NULL,
  `telepon` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gambar` text NOT NULL,
  `NIK` text NOT NULL,
  `role` enum('admin','Kasir','view') NOT NULL DEFAULT 'view'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id_member`, `nm_member`, `alamat_member`, `telepon`, `email`, `gambar`, `NIK`, `role`) VALUES
(1, 'Admin', 'Jl H Sarmah', '08888888', 'example@gmail.com', '1758987953_45ebedc70e6c01a9.png', '13333333', 'admin'),
(11, 'viewer09', '09', '', 'viewer09@gmail.com', '1759071302_e30514768e385608.jpg', '09', 'view'),
(13, 'kas', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', '', 'kasi@gmail.com', '', 'qd12', 'view');

-- --------------------------------------------------------

--
-- Table structure for table `nota`
--

CREATE TABLE `nota` (
  `id_nota` int(11) NOT NULL,
  `id_barang` varchar(255) NOT NULL,
  `id_member` int(11) NOT NULL,
  `jumlah` varchar(255) NOT NULL,
  `total` varchar(255) NOT NULL,
  `tanggal_input` varchar(255) NOT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `diskon_nominal` decimal(15,2) DEFAULT 0.00,
  `total_akhir` decimal(15,2) DEFAULT 0.00,
  `bayar` decimal(15,2) DEFAULT 0.00,
  `kembalian` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `nota`
--

INSERT INTO `nota` (`id_nota`, `id_barang`, `id_member`, `jumlah`, `total`, `tanggal_input`, `periode`, `id_customer`, `diskon_persen`, `diskon_nominal`, `total_akhir`, `bayar`, `kembalian`) VALUES
(1, 'BR002', 1, '1', '50000', '27 September 2025, 19:33', '09-2025', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, 'BR002', 1, '1', '50000', '27 September 2025, 19:33', '09-2025', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(3, 'BR002', 1, '1', '50000', '27 September 2025, 19:33', '09-2025', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, 'BR001', 11, '1', '20000', '28 September 2025, 22:05', '09-2025', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 'BR001', 1, '1', '20000', '16 February 2026, 23:52', '02-2026', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(6, 'BR001', 1, '1', '20000', '16 February 2026, 23:52', '02-2026', NULL, 0.00, 0.00, 0.00, 0.00, 0.00),
(7, 'BR001', 1, '1', '20000', '16 February 2026, 23:54', '02-2026', 1, 0.00, 0.00, 20000.00, 0.00, 0.00),
(8, 'BR002', 1, '1', '50000', '17 February 2026, 0:04', '02-2026', 1, 2.00, 7600.00, 50000.00, 0.00, 0.00),
(9, 'BR004', 1, '1', '80000', '17 February 2026, 0:04', '02-2026', 1, 2.00, 7600.00, 80000.00, 0.00, 0.00),
(10, 'BR002', 1, '1', '50000', '17 February 2026, 0:18', '02-2026', 1, 2.00, 2600.00, 50000.00, 0.00, 0.00),
(11, 'BR004', 1, '1', '80000', '17 February 2026, 0:19', '02-2026', 1, 2.00, 2600.00, 80000.00, 0.00, 0.00),
(12, 'BR005', 1, '1', '130000', '17 February 2026, 0:32', '02-2026', 1, 2.00, 3000.00, 130000.00, 0.00, 0.00),
(13, 'BR001', 1, '1', '20000', '17 February 2026, 0:32', '02-2026', 1, 2.00, 3000.00, 20000.00, 0.00, 0.00),
(14, 'BR005', 1, '1', '130000', '17 February 2026, 0:54', '02-2026', 0, 0.00, 0.00, 130000.00, 0.00, 0.00),
(15, 'BR002', 1, '1', '50000', '17 February 2026, 0:54', '02-2026', 0, 0.00, 0.00, 50000.00, 0.00, 0.00),
(16, 'BR002', 1, '1', '50000', '17 February 2026, 1:02', '02-2026', 0, 100.00, 50000.00, 50000.00, 0.00, 0.00),
(17, 'BR002', 1, '10', '500000', '17 February 2026, 1:37', '02-2026', 0, 0.00, 0.00, 500000.00, 600000.00, 100000.00),
(18, 'BR001', 1, '1', '20000', '17 February 2026, 1:51', '02-2026', 0, 0.00, 0.00, 20000.00, 20000.00, 0.00),
(19, 'BR005', 1, '10', '1300000', '17 February 2026, 1:56', '02-2026', 0, 0.00, 0.00, 1300000.00, 100000000.00, 98680000.00),
(20, 'BR001', 1, '1', '20000', '17 February 2026, 2:01', '02-2026', 0, 0.00, 0.00, 20000.00, 100000000.00, 98680000.00),
(21, 'BR001', 1, '1', '20000', '17 February 2026, 13:11', '02-2026', 7, 2.00, 2000.00, 19600.00, 99999.00, 1999.00),
(22, 'BR004', 1, '1', '80000', '17 February 2026, 13:15', '02-2026', 7, 2.00, 2000.00, 78400.00, 99999.00, 1999.00),
(23, 'BR005', 1, '1', '130000', '17 February 2026, 13:23', '02-2026', 0, 0.00, 0.00, 130000.00, 800000.00, 670000.00),
(24, 'BR001', 1, '1', '20000', '17 February 2026, 13:30', '02-2026', 0, 0.00, 0.00, 20000.00, 800000.00, 780000.00),
(25, 'BR001', 1, '1', '20000', '17 February 2026, 14:08', '02-2026', 0, 0.00, 0.00, 20000.00, 80000.00, 40000.00),
(26, 'BR001', 1, '1', '20000', '17 February 2026, 13:53', '02-2026', 0, 0.00, 0.00, 20000.00, 80000.00, 40000.00),
(30, 'BR001', 1, '2', '40000', '15 February 2026, 10:45', '02-2026', 10, 5.00, 2000.00, 40000.00, 50000.00, 10000.00),
(31, 'BR002', 1, '1', '50000', '15 February 2026, 10:45', '02-2026', 10, 5.00, 2000.00, 50000.00, 50000.00, 0.00),
(32, 'BR001', 1, '1', '20000', '16 February 2026, 11:50', '02-2026', 11, 0.00, 0.00, 20000.00, 20000.00, 0.00),
(33, 'BR004', 1, '1', '80000', '16 February 2026, 11:50', '02-2026', 11, 0.00, 0.00, 80000.00, 100000.00, 20000.00),
(34, 'BR002', 1, '2', '100000', '17 February 2026, 09:30', '02-2026', 12, 5.00, 5000.00, 100000.00, 100000.00, 0.00),
(35, 'BR005', 1, '1', '130000', '17 February 2026, 09:30', '02-2026', 12, 5.00, 5000.00, 130000.00, 130000.00, 0.00),
(36, 'BR001', 1, '1', '20000', '18 February 2026, 14:20', '02-2026', 13, 0.00, 0.00, 20000.00, 50000.00, 30000.00),
(37, 'BR003', 1, '1', '80000', '18 February 2026, 14:20', '02-2026', 13, 0.00, 0.00, 80000.00, 80000.00, 0.00),
(38, 'BR004', 1, '2', '160000', '19 February 2026, 08:15', '02-2026', 14, 5.00, 8000.00, 160000.00, 200000.00, 40000.00),
(39, 'BR005', 1, '2', '260000', '19 February 2026, 08:15', '02-2026', 14, 5.00, 8000.00, 260000.00, 260000.00, 0.00),
(40, 'BR001', 1, '2', '40000', '20 February 2026, 13:40', '02-2026', 15, 0.00, 0.00, 40000.00, 50000.00, 10000.00),
(41, 'BR002', 1, '1', '50000', '20 February 2026, 13:40', '02-2026', 15, 0.00, 0.00, 50000.00, 50000.00, 0.00),
(42, 'BR003', 1, '1', '80000', '21 February 2026, 10:50', '02-2026', 16, 5.00, 4000.00, 80000.00, 100000.00, 20000.00),
(43, 'BR004', 1, '1', '80000', '21 February 2026, 10:50', '02-2026', 16, 5.00, 4000.00, 80000.00, 80000.00, 0.00),
(44, 'BR001', 1, '1', '20000', '22 February 2026, 15:25', '02-2026', 17, 0.00, 0.00, 20000.00, 20000.00, 0.00),
(45, 'BR005', 1, '2', '260000', '23 February 2026, 09:20', '02-2026', 18, 5.00, 13000.00, 260000.00, 300000.00, 40000.00),
(46, 'BR002', 1, '1', '50000', '23 February 2026, 09:20', '02-2026', 18, 5.00, 13000.00, 50000.00, 50000.00, 0.00),
(47, 'BR001', 1, '2', '40000', '24 February 2026, 11:10', '02-2026', 19, 0.00, 0.00, 40000.00, 50000.00, 10000.00),
(48, 'BR003', 1, '1', '80000', '24 February 2026, 11:10', '02-2026', 19, 0.00, 0.00, 80000.00, 80000.00, 0.00),
(49, 'BR004', 1, '2', '160000', '25 February 2026, 08:35', '02-2026', 20, 5.00, 8000.00, 160000.00, 200000.00, 40000.00),
(50, 'BR002', 1, '2', '100000', '25 February 2026, 08:35', '02-2026', 20, 5.00, 8000.00, 100000.00, 100000.00, 0.00),
(51, 'BR001', 1, '1', '20000', '26 February 2026, 14:05', '02-2026', 21, 0.00, 0.00, 20000.00, 50000.00, 30000.00),
(52, 'BR005', 1, '1', '130000', '26 February 2026, 14:05', '02-2026', 21, 0.00, 0.00, 130000.00, 130000.00, 0.00),
(53, 'BR002', 1, '3', '150000', '27 February 2026, 10:20', '02-2026', 22, 5.00, 7500.00, 150000.00, 200000.00, 50000.00),
(54, 'BR004', 1, '2', '160000', '27 February 2026, 10:20', '02-2026', 22, 5.00, 7500.00, 160000.00, 160000.00, 0.00),
(55, 'BR001', 1, '1', '20000', '28 February 2026, 12:50', '02-2026', 23, 0.00, 0.00, 20000.00, 20000.00, 0.00),
(56, 'BR003', 1, '1', '80000', '28 February 2026, 12:50', '02-2026', 23, 0.00, 0.00, 80000.00, 100000.00, 20000.00),
(57, 'BR005', 1, '2', '260000', '01 March 2026, 09:05', '03-2026', 24, 5.00, 13000.00, 260000.00, 300000.00, 40000.00),
(58, 'BR001', 1, '2', '40000', '01 March 2026, 09:05', '03-2026', 24, 5.00, 13000.00, 40000.00, 40000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` int(11) NOT NULL,
  `id_barang` varchar(255) NOT NULL,
  `id_member` int(11) NOT NULL,
  `jumlah` varchar(255) NOT NULL,
  `total` varchar(255) NOT NULL,
  `tanggal_input` varchar(255) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `diskon_nominal` decimal(15,2) DEFAULT 0.00,
  `total_akhir` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_log`
--

CREATE TABLE `reminder_log` (
  `id_log` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `id_barang` varchar(255) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `pesan` text DEFAULT NULL,
  `status` enum('berhasil','gagal','pending') DEFAULT 'pending',
  `tanggal_kirim` datetime DEFAULT NULL,
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reminder_log`
--

INSERT INTO `reminder_log` (`id_log`, `id_customer`, `id_barang`, `no_telepon`, `pesan`, `status`, `tanggal_kirim`, `response`) VALUES
(1, 10, 'BR001', '628123456789', 'Yth. Pak Pak Budi Santoso, produk Tabung Gas 3 Kg sudah ready stock! Langsung order ke Agen Gas Bang Usup di 088888888', 'berhasil', '2026-03-10 09:00:00', 'Message sent successfully'),
(2, 11, 'BR004', '628234567890', 'Halo Ibu Siti Nurhaliza, stok Tabung LPG 12 Kg sudah tersedia lagi! Hubungi Agen Gas Bang Usup di 088888888', 'berhasil', '2026-03-10 09:00:30', 'Message sent successfully'),
(3, 12, 'BR002', '628345678901', 'Halo Pak Ahmad Wijaya, stok Tabung LPG 5,5 kg sudah tersedia lagi! Hubungi Agen Gas Bang Usup di 088888888', 'gagal', '2026-03-10 09:01:00', 'Invalid phone number'),
(4, 13, 'BR003', '628456789012', 'Halo Ibu Ibu Dewi Lestari, kabar baik! Tabung LPG 12 kg kembali tersedia dengan harga Rp 80.000. Info: 088888888', 'berhasil', '2026-03-10 09:01:30', 'Message sent successfully'),
(5, 14, 'BR005', '628567890123', 'Halo Pak Hendra Gunawan, stok Kompor sudah tersedia lagi! Hubungi Agen Gas Bang Usup di 088888888', 'berhasil', '2026-03-10 09:02:00', 'Message sent successfully');

-- --------------------------------------------------------

--
-- Table structure for table `setting_diskon`
--

CREATE TABLE `setting_diskon` (
  `id_setting` int(11) NOT NULL,
  `min_belanja` decimal(15,2) DEFAULT 0.00,
  `diskon_persen` decimal(5,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `setting_diskon`
--

INSERT INTO `setting_diskon` (`id_setting`, `min_belanja`, `diskon_persen`, `keterangan`, `status`) VALUES
(1, 100000.00, 5.00, 'Diskon 5% untuk belanja minimal Rp 100.000', 'aktif'),
(2, 500000.00, 10.00, 'Diskon 10% untuk belanja minimal Rp 500.000', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `id_toko` int(11) NOT NULL,
  `nama_toko` varchar(255) NOT NULL,
  `alamat_toko` text NOT NULL,
  `tlp` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `api_fonte_token` varchar(255) DEFAULT NULL,
  `api_fonte_phone` varchar(20) DEFAULT NULL,
  `pesan_test` text DEFAULT NULL,
  `reminder_aktif` enum('ya','tidak') DEFAULT 'tidak',
  `reminder_terakhir` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`id_toko`, `nama_toko`, `alamat_toko`, `tlp`, `nama_pemilik`, `api_fonte_token`, `api_fonte_phone`, `pesan_test`, `reminder_aktif`, `reminder_terakhir`) VALUES
(1, 'Agen Gas Bang Usup', 'Jl H Sarmah No 55, Pondok Kacang Timur, Pondok Aren', '088888888', 'Usup', 'rKEAo8bU5yCTKMqYKPed', '', 'Sedang Testing', 'ya', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`),
  ADD UNIQUE KEY `no_telepon` (`no_telepon`);

--
-- Indexes for table `customer_barang`
--
ALTER TABLE `customer_barang`
  ADD PRIMARY KEY (`id_cb`),
  ADD UNIQUE KEY `customer_barang` (`id_customer`,`id_barang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id_member`);

--
-- Indexes for table `nota`
--
ALTER TABLE `nota`
  ADD PRIMARY KEY (`id_nota`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`);

--
-- Indexes for table `reminder_log`
--
ALTER TABLE `reminder_log`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `setting_diskon`
--
ALTER TABLE `setting_diskon`
  ADD PRIMARY KEY (`id_setting`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`id_toko`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `customer_barang`
--
ALTER TABLE `customer_barang`
  MODIFY `id_cb` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `nota`
--
ALTER TABLE `nota`
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminder_log`
--
ALTER TABLE `reminder_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `setting_diskon`
--
ALTER TABLE `setting_diskon`
  MODIFY `id_setting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `id_toko` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
