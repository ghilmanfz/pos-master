-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 08:10 AM
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
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_customer`, `nama_customer`, `no_telepon`, `alamat`, `email`, `poin_diskon`, `total_belanja`, `tgl_daftar`, `status`) VALUES
(1, 'Customer Umum', '08978223198', '-', 'elincayangfikri@gmail.com', 0, 416800.00, '16 February 2026', 'aktif'),
(3, 'Ghilman', '081289102568', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'tets@gmail.com', 0, 0.00, '16 February 2026, 20:13', 'aktif'),
(4, 'Raihan', '08589214765', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'admin@gmail.com', 0, 0.00, '16 February 2026, 20:14', 'aktif'),
(5, 'nama', '0897822319', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', 'supandi.subur@gmail.com', 0, 0.00, '16 February 2026, 23:53', 'aktif'),
(7, 'kepo', '0897777', 'kepo', 'a@a.com', 0, 98000.00, '17 February 2026, 13:22', 'aktif');

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
(6, 7, 'BR004', '2026-02-17 13:23:20', 1, 1);

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
  `role` enum('admin','view') NOT NULL DEFAULT 'view'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id_member`, `nm_member`, `alamat_member`, `telepon`, `email`, `gambar`, `NIK`, `role`) VALUES
(1, 'Admin', 'Jl H Sarmah', '08888888', 'example@gmail.com', '1758987953_45ebedc70e6c01a9.png', '13333333', 'admin'),
(11, 'viewer09', '09', '', 'viewer09@gmail.com', '1759071302_e30514768e385608.jpg', '09', 'view'),
(13, 'kas', 'jl. kh wahid hasyim , gang awab Rt03/05 no 36 cipadu jaya, larangan, tangerang, banten', '', 'kasi@gmail.com', '', 'qd12', '');

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
(26, 'BR001', 1, '1', '20000', '17 February 2026, 13:53', '02-2026', 0, 0.00, 0.00, 20000.00, 80000.00, 40000.00);

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
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer_barang`
--
ALTER TABLE `customer_barang`
  MODIFY `id_cb` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `reminder_log`
--
ALTER TABLE `reminder_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

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
