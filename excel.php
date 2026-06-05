<?php
declare(strict_types=1);
@ob_start();
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';
include $view;
$lihat = new view($config);

$bulan_tes = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

function xls_safe($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah_xls($n): string
{
    return 'Rp ' . number_format((float) $n, 0, ',', '.');
}

$cariParam = trim((string) (filter_input(INPUT_GET, 'cari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]) ?? ''));
$cariActive = in_array($cariParam, ['yes', 'ok'], true);
$hariActive = trim((string) (filter_input(INPUT_GET, 'hari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]) ?? '')) === 'cek';
$dateRangeActive = trim((string) (filter_input(INPUT_GET, 'daterange', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]) ?? '')) === 'cek';

$bulanRaw = filter_input(INPUT_GET, 'bln', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$bulanParam = (is_string($bulanRaw) && preg_match('/^(0[1-9]|1[0-2])$/', $bulanRaw)) ? $bulanRaw : '';
$tahunRaw = filter_input(INPUT_GET, 'thn', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$tahunParam = (is_string($tahunRaw) && preg_match('/^\d{4}$/', $tahunRaw)) ? $tahunRaw : '';
$tanggalRaw = filter_input(INPUT_GET, 'tgl', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$tanggalParam = (is_string($tanggalRaw) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalRaw)) ? $tanggalRaw : '';
$dariRaw = filter_input(INPUT_GET, 'dari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$dariParam = (is_string($dariRaw) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dariRaw)) ? $dariRaw : '';
$sampaiRaw = filter_input(INPUT_GET, 'sampai', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$sampaiParam = (is_string($sampaiRaw) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampaiRaw)) ? $sampaiRaw : '';

if ($cariActive && $bulanParam !== '' && $tahunParam !== '') {
    $periode = $bulanParam . '-' . $tahunParam;
    $judulPeriode = ($bulan_tes[$bulanParam] ?? $bulanParam) . ' ' . $tahunParam;
    $hasil = $lihat->periode_jual($periode);
} elseif ($dateRangeActive && $dariParam !== '' && $sampaiParam !== '') {
    $judulPeriode = $dariParam . ' s/d ' . $sampaiParam;
    $hasil = $lihat->range_jual($dariParam, $sampaiParam);
} elseif ($hariActive && $tanggalParam !== '') {
    $judulPeriode = $tanggalParam;
    $hasil = $lihat->hari_jual($tanggalParam);
} else {
    $judulPeriode = ($bulan_tes[date('m')] ?? date('m')) . ' ' . date('Y');
    $hasil = $lihat->jual();
}

$filename = 'laporan-penjualan-' . preg_replace('/[^0-9A-Za-z-]+/', '-', $judulPeriode) . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);

$totalJumlah = 0;
$totalModal = 0.0;
$totalPenjualan = 0.0;
$totalDiskon = 0.0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11pt; }
        th { background: #0bb365; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #555; padding: 8px; }
        td { border: 1px solid #999; padding: 6px; vertical-align: top; }
        .title { font-size: 16pt; font-weight: bold; text-align: center; background: #e9f7ef; }
        .subtitle { font-size: 11pt; text-align: center; background: #f8f9fa; }
        .num { text-align: right; mso-number-format:'\@'; white-space: nowrap; }
        .center { text-align: center; }
        .total { font-weight: bold; background: #fff2cc; }
    </style>
</head>
<body>
<table>
    <tr><td colspan="13" class="title">LAPORAN PENJUALAN</td></tr>
    <tr><td colspan="13" class="subtitle">Periode: <?= xls_safe($judulPeriode); ?></td></tr>
    <tr><td colspan="13" class="subtitle">Tanggal Export: <?= xls_safe(date('d/m/Y H:i')); ?></td></tr>
    <tr><td colspan="13"></td></tr>
    <tr>
        <th style="width:40px;">No</th>
        <th style="width:150px;">Nomor Transaksi</th>
        <th style="width:280px;">Barang Dibeli</th>
        <th style="width:80px;">Jumlah</th>
        <th style="width:120px;">Modal</th>
        <th style="width:120px;">Total Belanja</th>
        <th style="width:120px;">Diskon</th>
        <th style="width:120px;">Bayar</th>
        <th style="width:120px;">Kembalian</th>
        <th style="width:140px;">Kasir</th>
        <th style="width:160px;">Customer/Member</th>
        <th style="width:160px;">Tanggal</th>
        <th style="width:150px;">Poin</th>
    </tr>
    <?php $no = 1; foreach ($hasil as $isi):
        $jumlah = (int) ($isi['jumlah_total'] ?? 0);
        $modal = (float) ($isi['modal_total'] ?? 0);
        $penjualan = (float) ($isi['total'] ?? 0);
        $diskon = (float) ($isi['diskon_nominal'] ?? 0);
        $totalJumlah += $jumlah;
        $totalModal += $modal;
        $totalPenjualan += $penjualan;
        $totalDiskon += $diskon;
        $idNotaMin = $isi['id_nota_min'] ?? 0;
        $noTransaksi = !empty($isi['no_transaksi']) ? $isi['no_transaksi'] : ('TRX-' . str_pad((string) $idNotaMin, 6, '0', STR_PAD_LEFT));
        $poinInfo = '-';
        if (!empty($isi['poin_didapat']) || !empty($isi['poin_digunakan']) || !empty($isi['poin_akhir'])) {
            $poinInfo = 'Dapat: ' . (int)($isi['poin_didapat'] ?? 0) . ', Pakai: ' . (int)($isi['poin_digunakan'] ?? 0) . ', Akhir: ' . (int)($isi['poin_akhir'] ?? 0);
        }
    ?>
    <tr>
        <td class="center"><?= $no++; ?></td>
        <td><?= xls_safe($noTransaksi); ?></td>
        <td><?= xls_safe($isi['barang_list'] ?? ''); ?></td>
        <td class="center"><?= xls_safe($jumlah); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($modal)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($penjualan)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($diskon)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($isi['bayar'] ?? 0)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($isi['kembalian'] ?? 0)); ?></td>
        <td><?= xls_safe($isi['nm_member'] ?? ''); ?></td>
        <td><?= xls_safe($isi['nama_customer'] ?? '-'); ?></td>
        <td><?= xls_safe($isi['tanggal_input'] ?? ''); ?></td>
        <td><?= xls_safe($poinInfo); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr class="total">
        <td colspan="3" class="center">TOTAL</td>
        <td class="center"><?= xls_safe($totalJumlah); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($totalModal)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($totalPenjualan)); ?></td>
        <td class="num"><?= xls_safe(rupiah_xls($totalDiskon)); ?></td>
        <td colspan="3" class="center">Keuntungan</td>
        <td colspan="3" class="num"><?= xls_safe(rupiah_xls($totalPenjualan - $totalModal)); ?></td>
    </tr>
</table>
</body>
</html>
