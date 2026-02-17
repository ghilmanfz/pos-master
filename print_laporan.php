<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin'])) {
    die('<h1>Error: Silakan login terlebih dahulu</h1><p><a href="index.php">Login</a></p>');
}

require 'config.php';
include $view;

$lihat = new view($config);
$toko  = $lihat->toko();

function rupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// Get transaksi by id_nota range (lebih akurat)
$idNotaMin = filter_input(INPUT_GET, 'id_nota_min', FILTER_VALIDATE_INT);
$idNotaMax = filter_input(INPUT_GET, 'id_nota_max', FILTER_VALIDATE_INT);

// Fallback untuk backward compatibility dengan link lama yang masih pake id_nota
if (!$idNotaMin) {
    $idNotaMin = filter_input(INPUT_GET, 'id_nota', FILTER_VALIDATE_INT);
    $idNotaMax = $idNotaMin;
}

if (!$idNotaMin || $idNotaMin <= 0) {
    die('<h3>Error: Parameter ID Transaksi tidak valid</h3><p>ID Nota Min: ' . htmlspecialchars($_GET['id_nota_min'] ?? $_GET['id_nota'] ?? 'kosong') . '</p>');
}

// Pastikan idNotaMax ada
if (!$idNotaMax || $idNotaMax < $idNotaMin) {
    $idNotaMax = $idNotaMin;
}

// Query langsung dengan BETWEEN untuk akurasi maksimal
$sql = "SELECT nota.id_nota, nota.id_barang, nota.id_member, nota.id_customer, 
        nota.jumlah, nota.total, nota.tanggal_input, nota.periode,
        COALESCE(nota.diskon_persen, 0) as diskon_persen,
        COALESCE(nota.diskon_nominal, 0) as diskon_nominal,
        COALESCE(nota.total_akhir, nota.total) as total_akhir,
        COALESCE(nota.bayar, 0) as bayar,
        COALESCE(nota.kembalian, 0) as kembalian,
        barang.nama_barang, barang.satuan_barang, barang.harga_jual,
        member.nm_member, customer.nama_customer
        FROM nota 
        LEFT JOIN barang ON barang.id_barang = nota.id_barang 
        LEFT JOIN member ON member.id_member = nota.id_member 
        LEFT JOIN customer ON customer.id_customer = nota.id_customer
        WHERE nota.id_nota BETWEEN ? AND ?
        ORDER BY nota.id_nota ASC";

// Debug: tampilkan parameter query
echo "<!-- Debug: ID Nota Min = " . $idNotaMin . ", ID Nota Max = " . $idNotaMax . " -->\n";

$row = $config->prepare($sql);
$row->execute([$idNotaMin, $idNotaMax]);
$items = $row->fetchAll();

// Debug: tampilkan jumlah data
echo "<!-- Debug: Jumlah data ditemukan = " . count($items) . " -->\n";

if (empty($items)) {
    die('<h3>Error: Transaksi tidak ditemukan</h3><p>ID Nota Range: ' . $idNotaMin . ' - ' . $idNotaMax . '</p><p>Kemungkinan data sudah dihapus atau ID tidak valid</p>');
}

$firstItem = $items[0];
$kasir = htmlspecialchars($firstItem['nm_member'] ?? 'Kasir', ENT_QUOTES, 'UTF-8');
$customer = htmlspecialchars($firstItem['nama_customer'] ?? '-', ENT_QUOTES, 'UTF-8');
$tanggal = htmlspecialchars($firstItem['tanggal_input'] ?? '', ENT_QUOTES, 'UTF-8');
$idTransaksi = 'TRX-' . str_pad((string)$firstItem['id_nota'], 6, '0', STR_PAD_LEFT);

// Hitung total
$totalBelanja = 0.0;
$diskonPersen = isset($firstItem['diskon_persen']) ? (float)$firstItem['diskon_persen'] : 0.0;
$diskonNominal = isset($firstItem['diskon_nominal']) ? (float)$firstItem['diskon_nominal'] : 0.0;
$bayarNominal = isset($firstItem['bayar']) ? (float)$firstItem['bayar'] : 0.0;
$kembalianNominal = isset($firstItem['kembalian']) ? (float)$firstItem['kembalian'] : 0.0;

foreach ($items as $item) {
    $totalBelanja += isset($item['total']) ? (float)$item['total'] : 0.0;
}

// Hitung diskon dari persen jika ada
$diskonDariPersen = 0.0;
if ($diskonPersen > 0) {
    $diskonDariPersen = ($totalBelanja * $diskonPersen) / 100;
}

// Total diskon gabungan
$totalDiskon = $diskonDariPersen + $diskonNominal;

// Total akhir
$totalAkhir = $totalBelanja - $totalDiskon;
if ($totalAkhir < 0) $totalAkhir = 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Struk Transaksi <?= $idTransaksi; ?></title>
    <style>
    @page {
        margin: 2mm;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        background: #fff;
        font-family: "Courier New", Courier, monospace;
        font-size: 12px;
        color: #000;
    }

    .receipt {
        width: 100%;
        margin: 0 auto;
    }

    .center {
        text-align: center;
    }

    .sep {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        text-align: left;
        padding: 2px 0;
        vertical-align: top;
    }

    thead th {
        border-bottom: 1px dashed #000;
        font-weight: bold;
    }

    .ta-r {
        text-align: right;
    }

    .item-sep td {
        border-bottom: 1px dashed #000;
        padding-top: 4px;
    }

    .totals .row {
        display: grid;
        grid-template-columns: 1fr auto;
        margin: 2px 0;
    }

    .mb-4 {
        margin-bottom: 4px;
    }

    .mb-6 {
        margin-bottom: 6px;
    }

    .mb-8 {
        margin-bottom: 8px;
    }
    </style>

</head>

<body onload="window.print()" onafterprint="window.close()">
    <div class="receipt">
        <!-- Header toko -->
        <div class="header center mb-8">
            <p><strong><?= htmlspecialchars($toko['nama_toko'] ?? 'Toko', ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <?php if (!empty($toko['alamat_toko'])): ?>
            <p><?= nl2br(htmlspecialchars($toko['alamat_toko'], ENT_QUOTES, 'UTF-8')); ?></p>
            <?php endif; ?>
        </div>

        <div class="sep"></div>

        <!-- Meta -->
        <div class="meta mb-8">
            <div><strong>ID Transaksi: <?= $idTransaksi; ?></strong></div>
            <div>Tanggal: <?= $tanggal; ?></div>
            <div>Kasir: <?= $kasir; ?></div>
            <div>Customer: <?= $customer; ?></div>
        </div>

        <div class="sep"></div>

        <!-- Daftar item -->
        <table class="mb-6">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th class="ta-r">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $first = true;
                foreach ($items as $item):
                    $nama = htmlspecialchars($item['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8');
                    $jumlah = (int)($item['jumlah'] ?? 0);
                    $satuan = htmlspecialchars($item['satuan_barang'] ?? 'PCS', ENT_QUOTES, 'UTF-8');
                    $total = (float)($item['total'] ?? 0.0);
                    $hargaSatuan = $jumlah > 0 ? $total / $jumlah : $total;
                ?>
                <?php if (!$first): ?>
                <tr class="item-sep">
                    <td colspan="2"></td>
                </tr>
                <?php endif; $first = false; ?>
                <tr>
                    <td class="item-name"><?= $nama; ?></td>
                    <td class="ta-r"><?= rupiah($total); ?></td>
                </tr>
                <tr>
                    <td><?= $jumlah; ?> <?= $satuan; ?> × <?= rupiah($hargaSatuan); ?></td>
                    <td class="ta-r"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="sep"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="row">
                <div>Total Belanja</div>
                <div><?= rupiah($totalBelanja); ?></div>
            </div>
            <?php if ($diskonDariPersen > 0): ?>
            <div class="row">
                <div>Diskon (<?= number_format($diskonPersen, 1); ?>%)</div>
                <div>- <?= rupiah($diskonDariPersen); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($diskonNominal > 0): ?>
            <div class="row">
                <div>Diskon Poin</div>
                <div>- <?= rupiah($diskonNominal); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($totalDiskon > 0): ?>
            <div class="row">
                <div><strong>Total Diskon</strong></div>
                <div><strong>- <?= rupiah($totalDiskon); ?></strong></div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div><strong>Total Bayar</strong></div>
                <div><strong><?= rupiah($totalAkhir); ?></strong></div>
            </div>
            <?php if ($bayarNominal >= $totalAkhir): ?>
            <div class="row">
                <div>Bayar</div>
                <div><?= rupiah($bayarNominal); ?></div>
            </div>
            <?php if ($kembalianNominal > 0): ?>
            <div class="row">
                <div>Kembali</div>
                <div><?= rupiah($kembalianNominal); ?></div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="sep"></div>

        <!-- Footer -->
        <div class="footer center mb-4">
            <p>Terima kasih telah berbelanja!</p>
        </div>
    </div>
</body>

</html>
