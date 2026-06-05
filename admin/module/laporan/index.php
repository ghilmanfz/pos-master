 <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $bulan_tes =array(
                '01'=>"Januari",
                '02'=>"Februari",
                '03'=>"Maret",
                '04'=>"April",
                '05'=>"Mei",
                '06'=>"Juni",
                '07'=>"Juli",
                '08'=>"Agustus",
                '09'=>"September",
                '10'=>"Oktober",
                '11'=>"November",
                '12'=>"Desember"
        );

        $cariParamRaw = filter_input(INPUT_GET, 'cari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $cariActive = is_string($cariParamRaw) && $cariParamRaw !== '';

        $hariParamRaw = filter_input(INPUT_GET, 'hari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $hariActive = ($hariParamRaw === 'cek');

        $bulanPostRaw = filter_input(INPUT_GET, 'bln', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        if ($bulanPostRaw === null) { $bulanPostRaw = filter_input(INPUT_POST, 'bln', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]); }
        $bulanPost = (is_string($bulanPostRaw) && preg_match('/^(0[1-9]|1[0-2])$/', $bulanPostRaw)) ? $bulanPostRaw : '';

        $tahunPostRaw = filter_input(INPUT_GET, 'thn', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        if ($tahunPostRaw === null) { $tahunPostRaw = filter_input(INPUT_POST, 'thn', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]); }
        $tahunPost = (is_string($tahunPostRaw) && preg_match('/^\d{4}$/', $tahunPostRaw)) ? $tahunPostRaw : '';

        $hariPostRaw = filter_input(INPUT_GET, 'tgl', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        if ($hariPostRaw === null) { $hariPostRaw = filter_input(INPUT_POST, 'hari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]); }
        $hariPost = is_string($hariPostRaw) ? trim($hariPostRaw) : '';

        // Date Range
        $dateRangeParamRaw = filter_input(INPUT_GET, 'daterange', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $dateRangeActive = ($dateRangeParamRaw === 'cek');

        $dariPostRaw = filter_input(INPUT_GET, 'dari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        if ($dariPostRaw === null) { $dariPostRaw = filter_input(INPUT_POST, 'dari', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]); }
        $dariPost = (is_string($dariPostRaw) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dariPostRaw)) ? $dariPostRaw : '';

        $sampaiPostRaw = filter_input(INPUT_GET, 'sampai', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        if ($sampaiPostRaw === null) { $sampaiPostRaw = filter_input(INPUT_POST, 'sampai', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]); }
        $sampaiPost = (is_string($sampaiPostRaw) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $sampaiPostRaw)) ? $sampaiPostRaw : '';

        // Day of Week Filter
        $dayofweekParamRaw = filter_input(INPUT_GET, 'dayofweek', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $dayofweekActive = ($dayofweekParamRaw === 'cek');

        $dayPost = filter_input(INPUT_GET, 'day', FILTER_VALIDATE_INT);
        if ($dayPost === null) { $dayPost = filter_input(INPUT_POST, 'day', FILTER_VALIDATE_INT); }
        $dayPost = ($dayPost !== null && $dayPost !== false && $dayPost >= 1 && $dayPost <= 7) ? $dayPost : '';
?>
<style>
@media print {
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: white !important;
    }
    /* Sembunyikan kolom Aksi saat print */
    table th:last-child,
    table td:last-child {
        display: none !important;
    }
}
</style>
<div class="row">
	<div class="col-md-12">
		<h4>
			<!--<a  style="padding-left:2pc;" href="fungsi/hapus/hapus.php?laporan=jual" onclick="javascript:return confirm('Data Laporan akan di Hapus ?');">
						<button class="btn btn-danger">RESET</button>
					</a>-->
                        <?php if($cariActive && $bulanPost !== '' && $tahunPost !== ''){ ?>
                        Data Laporan Penjualan <?= htmlspecialchars($bulan_tes[$bulanPost] ?? $bulanPost, ENT_QUOTES, 'UTF-8');?> <?= htmlspecialchars($tahunPost, ENT_QUOTES, 'UTF-8');?>
                        <?php }elseif($dateRangeActive && $dariPost !== '' && $sampaiPost !== ''){?>
                        Data Laporan Penjualan <?= htmlspecialchars($dariPost, ENT_QUOTES, 'UTF-8');?> s/d <?= htmlspecialchars($sampaiPost, ENT_QUOTES, 'UTF-8');?>
                        <?php }elseif($dayofweekActive && $dayPost !== ''){?>
                        Data Laporan Penjualan - <?php 
                            $hari_hari = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            echo htmlspecialchars($hari_hari[$dayPost], ENT_QUOTES, 'UTF-8');
                        ?>
                        <?php }elseif($hariActive && $hariPost !== ''){?>
                        Data Laporan Penjualan <?= htmlspecialchars($hariPost, ENT_QUOTES, 'UTF-8');?>
                        <?php }else{?>
                        Data Laporan Penjualan <?= htmlspecialchars($bulan_tes[date('m')], ENT_QUOTES, 'UTF-8');?> <?= date('Y');?>
                        <?php }?>
                       
		</h4>
		<br />
		<div class="card no-print">
			<div class="card-header">
				<h5 class="card-title mt-2">Cari Laporan Per Bulan</h5>
			</div>
			<div class="card-body p-0">
                                <form method="get" action="index.php">
                                        <input type="hidden" name="page" value="laporan">
                                        <input type="hidden" name="cari" value="ok">
					<table class="table table-striped">
						<tr>
							<th>
								Pilih Bulan
							</th>
							<th>
								Pilih Tahun
							</th>
							<th>
								Aksi
							</th>
						</tr>
						<tr>
							<td>
								<select name="bln" class="form-control">
									<option value="">Bulan</option>
									<?php
								$bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
								$jlh_bln=count($bulan);
								$bln1 = array('01','02','03','04','05','06','07','08','09','10','11','12');
								$no=1;
								for($c=0; $c<$jlh_bln; $c+=1){
									$selected = ($bulanPost === $bln1[$c]) ? ' selected' : '';
							echo "<option value='{$bln1[$c]}'{$selected}> {$bulan[$c]} </option>";
								$no++;}
							?>
								</select>
							</td>
							<td>
							<?php
								$now=date('Y');
								echo "<select name='thn' class='form-control'>";
							echo '<option value="">Tahun</option>';
								for ($a=2017;$a<=$now;$a++)
								{
									$selected = ($tahunPost === (string)$a) ? ' selected' : '';
		echo "<option value='$a'$selected>$a</option>";
								}
								echo "</select>";
							?>
							</td>
							<td>
								<input type="hidden" name="periode" value="ya">
								<button class="btn btn-primary">
									<i class="fa fa-search"></i> Cari
								</button>
								<a href="index.php?page=laporan" class="btn btn-success">
									<i class="fa fa-refresh"></i> Refresh</a>

                                                                <?php if($cariActive && $bulanPost !== '' && $tahunPost !== ''){?>
                                                                <a href="excel.php?cari=yes&bln=<?= urlencode($bulanPost);?>&thn=<?= urlencode($tahunPost);?>"
                                                                        class="btn btn-info"><i class="fa fa-download"></i>
                                                                        Excel</a>
								<?php }else{?>
								<a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i>
									Excel</a>
								<?php }?>
							</td>
						</tr>
					</table>
				</form>
                                <form method="get" action="index.php">
                                        <input type="hidden" name="page" value="laporan">
                                        <input type="hidden" name="hari" value="cek">
					<table class="table table-striped">
						<tr>
							<th>
								Pilih Hari
							</th>
							<th>
								Aksi
							</th>
						</tr>
						<tr>
							<td>
								<input type="date" value="<?= htmlspecialchars($hariPost !== '' ? $hariPost : date('Y-m-d'), ENT_QUOTES, 'UTF-8');?>" class="form-control" name="tgl">
							</td>
							<td>
								<input type="hidden" name="periode" value="ya">
								<button class="btn btn-primary">
									<i class="fa fa-search"></i> Cari
								</button>
								<a href="index.php?page=laporan" class="btn btn-success">
									<i class="fa fa-refresh"></i> Refresh</a>

                                                                <?php if($hariActive && $hariPost !== ''){?>
                                                                <a href="excel.php?hari=cek&tgl=<?= urlencode($hariPost);?>" class="btn btn-info"><i
                                                                                class="fa fa-download"></i>
                                                                        Excel</a>
								<?php }else{?>
								<a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i>
									Excel</a>
								<?php }?>
							</td>
						</tr>
					</table>
				</form>
				<!-- Form Date Range -->
				<form method="get" action="index.php">
					<input type="hidden" name="page" value="laporan">
					<input type="hidden" name="daterange" value="cek">
					<table class="table table-striped">
						<tr>
							<th>Pilih Rentang Tanggal</th>
							<th colspan="2">Aksi</th>
						</tr>
						<tr>
							<td>
								<div class="row">
									<div class="col-md-5">
										<label>Dari Tanggal</label>
										<input type="date" value="<?= htmlspecialchars($dariPost !== '' ? $dariPost : date('Y-m-d', strtotime('-7 days')), ENT_QUOTES, 'UTF-8');?>" class="form-control" name="dari" required>
									</div>
									<div class="col-md-5">
										<label>Sampai Tanggal</label>
										<input type="date" value="<?= htmlspecialchars($sampaiPost !== '' ? $sampaiPost : date('Y-m-d'), ENT_QUOTES, 'UTF-8');?>" class="form-control" name="sampai" required>
									</div>
									<div class="col-md-2">
										<label>&nbsp;</label>
										<button class="btn btn-primary btn-block" type="submit">
											<i class="fa fa-search"></i> Cari
										</button>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<a href="index.php?page=laporan" class="btn btn-success">
									<i class="fa fa-refresh"></i> Refresh</a>
								<?php if($dateRangeActive && $dariPost !== '' && $sampaiPost !== ''){?>
								<a href="excel.php?daterange=cek&dari=<?= urlencode($dariPost);?>&sampai=<?= urlencode($sampaiPost);?>" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
								<?php }else{?>
								<a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
								<?php }?>
							</td>
						</tr>
					</table>
				</form>
				<!-- Form Day of Week Filter -->
				<!-- <form method="post" action="index.php?page=laporan&dayofweek=cek">
					<?php echo csrf_field(); ?>
					<table class="table table-striped">
						<tr>
							<th>Pilih Hari dalam Seminggu</th>
							<th>Aksi</th>
						</tr>
						<tr>
							<td>
								<select name="day" class="form-control" required>
									<option value="">-- Pilih Hari --</option>
									<option value="1">Senin</option>
									<option value="2">Selasa</option>
									<option value="3">Rabu</option>
									<option value="4">Kamis</option>
									<option value="5">Jumat</option>
									<option value="6">Sabtu</option>
									<option value="7">Minggu</option>
								</select>
							</td>
							<td>
								<button class="btn btn-primary">
									<i class="fa fa-search"></i> Cari
								</button>
								<a href="index.php?page=laporan" class="btn btn-success">
									<i class="fa fa-refresh"></i> Refresh</a>
								<?php if($dayofweekActive && $dayPost !== ''){?>
								<a href="excel.php?dayofweek=cek&day=<?= urlencode($dayPost);?>" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
								<?php }else{?>
								<a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
								<?php }?>
							</td>
						</tr>
					</table>
				</form> -->
         <br />
         <!-- view barang -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered w-100 table-sm" id="example1">
						<thead>
							<tr style="background:#DFF0D8;color:#333;">
								<th style="background:#0bb365;color:#fff;text-align: center;"> No</th>
								<th style="background:#0bb365;color:#fff;text-align: center;"> ID Transaksi</th>
								<th style="background:#0bb365;color:#fff;text-align: center;"> Barang Dibeli</th>
								<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Jumlah</th>
								<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Modal</th>
								<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Total</th>							<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Diskon</th>								<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Bayar</th>
								<th style="width:10%;background:#0bb365;color:#fff;text-align: center;"> Kembalian</th>
								<th style="background:#0bb365;color:#fff;text-align: center;">Kasir</th>
								<th style="background:#0bb365;color:#fff;text-align: center;"> Customer/Member</th>
								<th style="background:#0bb365;color:#fff;text-align: center;"> Tanggal</th>
								<th style="background:#0bb365;color:#fff;text-align: center;">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$no=1; 
                                                                if($cariActive && $bulanPost !== '' && $tahunPost !== ''){
                                                                        $periode = $bulanPost.'-'.$tahunPost;
                                                                        $no=1;
                                                                        $jumlah = 0;
                                                                        $bayar = 0;
                                                                        $hasil = $lihat -> periode_jual($periode);
                                                                }elseif($dateRangeActive && $dariPost !== '' && $sampaiPost !== ''){
                                                                        $no=1;
                                                                        $jumlah = 0;
                                                                        $bayar = 0;
                                                                        $hasil = $lihat -> range_jual($dariPost, $sampaiPost);
                                                                }elseif($dayofweekActive && $dayPost !== ''){
                                                                        $no=1;
                                                                        $jumlah = 0;
                                                                        $bayar = 0;
                                                                        $hasil = $lihat -> day_of_week_jual($dayPost);
                                                                }elseif($hariActive && $hariPost !== ''){
                                                                        $hari = $hariPost;
									$no=1; 
									$jumlah = 0;
									$bayar = 0;
									$hasil = $lihat -> hari_jual($hari);
								}else{
									$hasil = $lihat -> jual();
								}
							?>
							<?php 
								$bayar = 0;
								$jumlah = 0;
								$modal = 0;
								foreach($hasil as $isi){ 
									$bayar += $isi['total'];
									$modal += $isi['modal_total'];
									$jumlah += $isi['jumlah_total'];
									
									// Generate ID Transaksi dari id_nota_min
									$idNotaMin = isset($isi['id_nota_min']) ? $isi['id_nota_min'] : (isset($isi['id_nota']) ? $isi['id_nota'] : 0);
									$idNotaMax = isset($isi['id_nota_max']) ? $isi['id_nota_max'] : $idNotaMin;
								$idTransaksi = !empty($isi['no_transaksi']) ? $isi['no_transaksi'] : ('TRX-' . str_pad((string)$idNotaMin, 6, '0', STR_PAD_LEFT));
									$noTransaksiEnc = urlencode($idTransaksi);
									$tanggalEnc = urlencode($isi['tanggal_input'] ?? '');
									$idMemberEnc = urlencode($isi['id_member']);
									$idCustomerEnc = urlencode($isi['id_customer'] ?? '0');
									$idNotaMinEnc = urlencode($idNotaMin);
									$idNotaMaxEnc = urlencode($idNotaMax);
							?>
							<tr>
								<td><?= htmlspecialchars((string) $no, ENT_QUOTES, 'UTF-8');?></td>
								<td><strong><?= htmlspecialchars($idTransaksi, ENT_QUOTES, 'UTF-8');?></strong></td>
								<td><?= htmlspecialchars($isi['barang_list'], ENT_QUOTES, 'UTF-8');?></td>
								<td><?= htmlspecialchars($isi['jumlah_total'], ENT_QUOTES, 'UTF-8');?> </td>
								<td>Rp <?php echo number_format($isi['modal_total']);?>,-</td>
								<td>Rp <?php echo number_format($isi['total']);?>,-</td>						<td>Rp <?php echo number_format((float)$isi['diskon_nominal']);?>,-</td>								<td>Rp <?php echo number_format((float)$isi['bayar']);?>,-</td>
								<td>Rp <?php echo number_format((float)$isi['kembalian']);?>,-</td>
								<td><?= htmlspecialchars($isi['nm_member'], ENT_QUOTES, 'UTF-8');?></td>
								<td><?= htmlspecialchars($isi['nama_customer'] ?? '-', ENT_QUOTES, 'UTF-8');?></td>
								<td><?= htmlspecialchars($isi['tanggal_input'], ENT_QUOTES, 'UTF-8');?></td>
								<td>
									<a href="print_laporan.php?no_transaksi=<?= $noTransaksiEnc;?>&tanggal=<?= $tanggalEnc;?>&id_member=<?= $idMemberEnc;?>&id_customer=<?= $idCustomerEnc;?>&id_nota_min=<?= $idNotaMinEnc;?>&id_nota_max=<?= $idNotaMaxEnc;?>" 
									   target="_blank" 
									   class="btn btn-primary btn-sm no-print"
									   title="Print Transaksi <?= htmlspecialchars($idTransaksi, ENT_QUOTES, 'UTF-8');?>">
										<i class="fa fa-print"></i> Print
									</a>
								</td>
							</tr>
							<?php $no++; }?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="3">Total Terjual</td>
								<th><?= htmlspecialchars($jumlah, ENT_QUOTES, 'UTF-8');?></td>
								<th>Rp <?php echo number_format($modal);?>,-</th>
								<th>Rp <?php echo number_format($bayar);?>,-</th>
								<th colspan="2">-</th>
								<th colspan="2" style="background:#0bb365;color:#fff;">Keuntungan</th>
								<th colspan="2" style="background:#0bb365;color:#fff;">
									Rp <?php echo number_format($bayar-$modal);?>,-</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
     </div>
 </div>