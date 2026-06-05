<?php
declare(strict_types=1);
@ob_start();
session_start();

if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require 'config.php';
include $view;

$lihat = new view($config);
$toko  = $lihat->toko();

function rupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

$noTransaksiRaw = filter_input(INPUT_GET, 'no_transaksi', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$noTransaksi = is_string($noTransaksiRaw) ? trim($noTransaksiRaw) : '';

$items = [];
if ($noTransaksi !== '') {
    $stmt = $config->prepare("SELECT nota.*, barang.nama_barang, barang.satuan_barang, barang.harga_jual, member.nm_member, customer.nama_customer
        FROM nota
        LEFT JOIN barang ON barang.id_barang = nota.id_barang
        LEFT JOIN member ON member.id_member = nota.id_member
        LEFT JOIN customer ON customer.id_customer = nota.id_customer
        WHERE nota.no_transaksi = ?
        ORDER BY nota.id_nota ASC");
    $stmt->execute([$noTransaksi]);
    $items = $stmt->fetchAll();
}

// Fallback untuk link lama: cetak dari keranjang yang belum dihapus.
if (empty($items)) {
    $stmt = $config->prepare("SELECT penjualan.*, barang.nama_barang, barang.satuan_barang, barang.harga_jual, member.nm_member, NULL as nama_customer
        FROM penjualan
        LEFT JOIN barang ON barang.id_barang = penjualan.id_barang
        LEFT JOIN member ON member.id_member = penjualan.id_member
        ORDER BY penjualan.id_penjualan ASC");
    $stmt->execute();
    $items = $stmt->fetchAll();
}

$nmMember = (string) filter_input(INPUT_GET, 'nm_member', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
$bayarInput = filter_input(INPUT_GET, 'bayar', FILTER_VALIDATE_FLOAT);
$kembaliInput = filter_input(INPUT_GET, 'kembali', FILTER_VALIDATE_FLOAT);

$totalBelanja = 0.0;
foreach ($items as $item) {
    $totalBelanja += (float)($item['total'] ?? 0);
}

$first = $items[0] ?? [];
$kasir = htmlspecialchars((string)($first['nm_member'] ?? $nmMember ?: '-'), ENT_QUOTES, 'UTF-8');
$customer = htmlspecialchars((string)($first['nama_customer'] ?? '-'), ENT_QUOTES, 'UTF-8');
$tanggal = htmlspecialchars((string)($first['tanggal_input'] ?? date('j F Y, G:i')), ENT_QUOTES, 'UTF-8');
$noTransaksiDisplay = htmlspecialchars((string)($first['no_transaksi'] ?? ($noTransaksi ?: '-')), ENT_QUOTES, 'UTF-8');

$diskonPersen = (float)($first['diskon_persen'] ?? (filter_input(INPUT_GET, 'diskon_persen', FILTER_VALIDATE_FLOAT) ?: 0));
$diskonNominal = (float)($first['diskon_nominal'] ?? (filter_input(INPUT_GET, 'diskon_nominal', FILTER_VALIDATE_FLOAT) ?: 0));
$diskonMemberNominal = (float)($first['diskon_member_nominal'] ?? (filter_input(INPUT_GET, 'diskon_member_nominal', FILTER_VALIDATE_FLOAT) ?: 0));
$diskonPoinNominal = (float)($first['diskon_poin_nominal'] ?? (filter_input(INPUT_GET, 'diskon_poin_nominal', FILTER_VALIDATE_FLOAT) ?: 0));
$poinDigunakan = (int)($first['poin_digunakan'] ?? (filter_input(INPUT_GET, 'poin_digunakan', FILTER_VALIDATE_INT) ?: 0));
$poinDidapat = (int)($first['poin_didapat'] ?? (filter_input(INPUT_GET, 'poin_didapat', FILTER_VALIDATE_INT) ?: 0));
$poinAkhir = (int)($first['poin_akhir'] ?? (filter_input(INPUT_GET, 'poin_akhir', FILTER_VALIDATE_INT) ?: 0));
$bayarNominal = (float)($first['bayar'] ?? (($bayarInput !== false && $bayarInput !== null) ? $bayarInput : 0));
$kembaliNominal = (float)($first['kembalian'] ?? (($kembaliInput !== false && $kembaliInput !== null) ? $kembaliInput : 0));
$totalAkhir = $totalBelanja - $diskonNominal;
if (isset($first['total_akhir']) && count($items) === 1) {
    $totalAkhir = (float)$first['total_akhir'];
}
if ($totalAkhir < 0) $totalAkhir = 0.0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Struk Pembelian <?= $noTransaksiDisplay; ?></title>
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
            <?php if (!empty($toko['alamat_toko'])): ?>
            <p><?= nl2br(htmlspecialchars($toko['alamat_toko'], ENT_QUOTES, 'UTF-8')); ?></p>
            <?php endif; ?>
        </div>
        <div class="sep"></div>
        <div class="meta mb-8">
            <div><strong>No Transaksi: <?= $noTransaksiDisplay; ?></strong></div>
            <div>Tanggal: <?= $tanggal; ?></div>
            <div>Kasir: <?= $kasir; ?></div>
            <?php if ($customer !== '-' && $customer !== ''): ?><div>Member: <?= $customer; ?></div><?php endif; ?>
        </div>
        <div class="sep"></div>
        <table class="mb-8">
            <thead><tr><th>Barang</th><th class="ta-r">Subtotal</th></tr></thead>
            <tbody>
                <?php if (is_iterable($items) && count($items) > 0): ?>
                <?php $firstRow = true; foreach ($items as $isi):
                    $nama = htmlspecialchars((string)($isi['nama_barang'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $jumlah = (int)($isi['jumlah'] ?? 0);
                    $total = (float)($isi['total'] ?? 0.0);
                    $hargaSatuan = $jumlah > 0 ? $total / $jumlah : $total;
                ?>
                <?php if (!$firstRow): ?><tr class="item-sep"><td colspan="2"></td></tr><?php endif; $firstRow = false; ?>
                <tr><td><?= $nama; ?></td><td class="ta-r"><?= rupiah($total); ?></td></tr>
                <tr><td><?= $jumlah; ?> × <?= rupiah($hargaSatuan); ?></td><td></td></tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="2" class="center">Tidak ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="sep"></div>
        <div class="totals">
            <div class="row"><div>Total Belanja</div><div><?= rupiah($totalBelanja); ?></div></div>
            <?php if ($diskonMemberNominal > 0): ?>
            <div class="row"><div>Diskon Member</div><div>- <?= rupiah($diskonMemberNominal); ?></div></div>
            <?php endif; ?>
            <?php if ($diskonPoinNominal > 0 && $poinDigunakan > 0): ?>
            <div class="row"><div>Diskon Poin (<?= $poinDigunakan; ?> poin)</div><div>- <?= rupiah($diskonPoinNominal); ?></div></div>
            <?php endif; ?>
            <?php $diskonLain = max(0, $diskonNominal - $diskonMemberNominal - $diskonPoinNominal); ?>
            <?php if ($diskonLain > 0): ?>
            <div class="row"><div>Diskon Tambahan</div><div>- <?= rupiah($diskonLain); ?></div></div>
            <?php endif; ?>
            <?php if ($diskonNominal > 0): ?>
            <div class="row"><div><strong>Total Diskon</strong></div><div><strong>- <?= rupiah($diskonNominal); ?></strong></div></div>
            <?php endif; ?>
            <div class="row"><div><strong>Total Akhir</strong></div><div><strong><?= rupiah($totalAkhir); ?></strong></div></div>
            <div class="row"><div>Bayar</div><div><?= rupiah($bayarNominal); ?></div></div>
            <div class="row"><div>Kembali</div><div><?= $kembaliNominal < 0 ? '<strong>- ' . rupiah(abs($kembaliNominal)) . '</strong>' : rupiah($kembaliNominal); ?></div></div>
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
