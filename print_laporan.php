<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin'])) {
    die('<h1>Error: Silakan login terlebih dahulu</h1><p><a href="index.php">Login</a></p>');
}

require 'config.php';
include $view;
$lihat = new view($config);
$toko = $lihat->toko();

function rupiah(float $n): string { return 'Rp ' . number_format($n, 0, ',', '.'); }

$noTransaksiRaw = filter_input(INPUT_GET, 'no_transaksi', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$noTransaksi = is_string($noTransaksiRaw) ? trim($noTransaksiRaw) : '';
$idNotaMin = filter_input(INPUT_GET, 'id_nota_min', FILTER_VALIDATE_INT);
$idNotaMax = filter_input(INPUT_GET, 'id_nota_max', FILTER_VALIDATE_INT);
if (!$idNotaMin) {
    $idNotaMin = filter_input(INPUT_GET, 'id_nota', FILTER_VALIDATE_INT);
    $idNotaMax = $idNotaMin;
}
if (!$idNotaMax || ($idNotaMin && $idNotaMax < $idNotaMin)) {
    $idNotaMax = $idNotaMin;
}

if ($noTransaksi !== '') {
    $sql = "SELECT nota.*, barang.nama_barang, barang.satuan_barang, barang.harga_jual, member.nm_member, customer.nama_customer
            FROM nota
            LEFT JOIN barang ON barang.id_barang = nota.id_barang
            LEFT JOIN member ON member.id_member = nota.id_member
            LEFT JOIN customer ON customer.id_customer = nota.id_customer
            WHERE nota.no_transaksi = ?
            ORDER BY nota.id_nota ASC";
    $stmt = $config->prepare($sql);
    $stmt->execute([$noTransaksi]);
} else {
    if (!$idNotaMin || $idNotaMin <= 0) {
        die('<h3>Error: Parameter transaksi tidak valid</h3>');
    }
    $sql = "SELECT nota.*, barang.nama_barang, barang.satuan_barang, barang.harga_jual, member.nm_member, customer.nama_customer
            FROM nota
            LEFT JOIN barang ON barang.id_barang = nota.id_barang
            LEFT JOIN member ON member.id_member = nota.id_member
            LEFT JOIN customer ON customer.id_customer = nota.id_customer
            WHERE nota.id_nota BETWEEN ? AND ?
            ORDER BY nota.id_nota ASC";
    $stmt = $config->prepare($sql);
    $stmt->execute([$idNotaMin, $idNotaMax]);
}
$items = $stmt->fetchAll();
if (empty($items)) {
    die('<h3>Error: Transaksi tidak ditemukan</h3>');
}

$firstItem = $items[0];
$kasir = htmlspecialchars((string)($firstItem['nm_member'] ?? 'Kasir'), ENT_QUOTES, 'UTF-8');
$customer = htmlspecialchars((string)($firstItem['nama_customer'] ?? '-'), ENT_QUOTES, 'UTF-8');
$tanggal = htmlspecialchars((string)($firstItem['tanggal_input'] ?? ''), ENT_QUOTES, 'UTF-8');
$idTransaksi = !empty($firstItem['no_transaksi']) ? $firstItem['no_transaksi'] : ('TRX-' . str_pad((string)$firstItem['id_nota'], 6, '0', STR_PAD_LEFT));
$idTransaksiSafe = htmlspecialchars($idTransaksi, ENT_QUOTES, 'UTF-8');

$totalBelanja = 0.0;
foreach ($items as $item) { $totalBelanja += (float)($item['total'] ?? 0); }
$diskonPersen = (float)($firstItem['diskon_persen'] ?? 0);
$diskonNominal = (float)($firstItem['diskon_nominal'] ?? 0);
$diskonMemberNominal = (float)($firstItem['diskon_member_nominal'] ?? 0);
$diskonPoinNominal = (float)($firstItem['diskon_poin_nominal'] ?? 0);
$poinDigunakan = (int)($firstItem['poin_digunakan'] ?? 0);
$poinDidapat = (int)($firstItem['poin_didapat'] ?? 0);
$poinAkhir = (int)($firstItem['poin_akhir'] ?? 0);
$bayarNominal = (float)($firstItem['bayar'] ?? 0);
$kembalianNominal = (float)($firstItem['kembalian'] ?? 0);
$totalAkhir = max(0, $totalBelanja - $diskonNominal);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Struk Transaksi <?= $idTransaksiSafe; ?></title>
    <style>
    @page { margin: 2mm; }
    html, body { margin: 0; padding: 0; background: #fff; font-family: "Courier New", Courier, monospace; font-size: 12px; color: #000; }
    .receipt { width: 100%; margin: 0 auto; }
    .center { text-align: center; }
    .sep { border-top: 1px dashed #000; margin: 6px 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { text-align: left; padding: 2px 0; vertical-align: top; }
    thead th { border-bottom: 1px dashed #000; font-weight: bold; }
    .ta-r { text-align: right; }
    .item-sep td { border-bottom: 1px dashed #000; padding-top: 4px; }
    .totals .row { display: grid; grid-template-columns: 1fr auto; margin: 2px 0; }
    .mb-8 { margin-bottom: 8px; }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">
    <div class="receipt">
        <div class="header center mb-8">
            <p><strong><?= htmlspecialchars($toko['nama_toko'] ?? 'Toko', ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <?php if (!empty($toko['alamat_toko'])): ?><p><?= nl2br(htmlspecialchars($toko['alamat_toko'], ENT_QUOTES, 'UTF-8')); ?></p><?php endif; ?>
        </div>
        <div class="sep"></div>
        <div class="meta mb-8">
            <div><strong>No Transaksi: <?= $idTransaksiSafe; ?></strong></div>
            <div>Tanggal: <?= $tanggal; ?></div>
            <div>Kasir: <?= $kasir; ?></div>
            <?php if ($customer !== '-' && $customer !== ''): ?><div>Member: <?= $customer; ?></div><?php endif; ?>
        </div>
        <div class="sep"></div>
        <table class="mb-6">
            <thead><tr><th>Barang</th><th class="ta-r">Subtotal</th></tr></thead>
            <tbody>
            <?php $first = true; foreach ($items as $item):
                $nama = htmlspecialchars((string)($item['nama_barang'] ?? ''), ENT_QUOTES, 'UTF-8');
                $jumlah = (int)($item['jumlah'] ?? 0);
                $total = (float)($item['total'] ?? 0.0);
                $hargaSatuan = $jumlah > 0 ? $total / $jumlah : $total;
            ?>
                <?php if (!$first): ?><tr class="item-sep"><td colspan="2"></td></tr><?php endif; $first = false; ?>
                <tr><td><?= $nama; ?></td><td class="ta-r"><?= rupiah($total); ?></td></tr>
                <tr><td><?= $jumlah; ?> × <?= rupiah($hargaSatuan); ?></td><td></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="sep"></div>
        <div class="totals">
            <div class="row"><div>Total Belanja</div><div><?= rupiah($totalBelanja); ?></div></div>
            <?php if ($diskonMemberNominal > 0): ?><div class="row"><div>Diskon Member</div><div>- <?= rupiah($diskonMemberNominal); ?></div></div><?php endif; ?>
            <?php if ($diskonPoinNominal > 0 && $poinDigunakan > 0): ?><div class="row"><div>Diskon Poin (<?= $poinDigunakan; ?> poin)</div><div>- <?= rupiah($diskonPoinNominal); ?></div></div><?php endif; ?>
            <?php $diskonLain = max(0, $diskonNominal - $diskonMemberNominal - $diskonPoinNominal); ?>
            <?php if ($diskonLain > 0): ?><div class="row"><div>Diskon Tambahan</div><div>- <?= rupiah($diskonLain); ?></div></div><?php endif; ?>
            <?php if ($diskonNominal > 0): ?><div class="row"><div><strong>Total Diskon</strong></div><div><strong>- <?= rupiah($diskonNominal); ?></strong></div></div><?php endif; ?>
            <div class="row"><div><strong>Total Akhir</strong></div><div><strong><?= rupiah($totalAkhir); ?></strong></div></div>
            <div class="row"><div>Bayar</div><div><?= rupiah($bayarNominal); ?></div></div>
            <div class="row"><div>Kembali</div><div><?= $kembalianNominal < 0 ? '<strong>- ' . rupiah(abs($kembalianNominal)) . '</strong>' : rupiah($kembalianNominal); ?></div></div>
            <?php if ($customer !== '-' && ($poinDidapat > 0 || $poinDigunakan > 0 || $poinAkhir > 0)): ?>
            <div class="sep"></div>
            <div class="row"><div>Poin Didapat</div><div><?= $poinDidapat; ?> poin</div></div>
            <?php if ($poinDigunakan > 0): ?><div class="row"><div>Poin Digunakan</div><div><?= $poinDigunakan; ?> poin</div></div><?php endif; ?>
            <div class="row"><div>Total Poin Member</div><div><?= $poinAkhir; ?> poin</div></div>
            <?php endif; ?>
        </div>
        <div class="sep"></div>
        <div class="footer center mb-4"><p>Terima kasih telah berbelanja!</p></div>
    </div>
</body>
</html>
