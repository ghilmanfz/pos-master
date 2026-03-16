 <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
<?php
        $id = $_SESSION['admin']['id_member'];
        $hasil = $lihat -> member_edit($id);
        $successParam = filter_input(INPUT_GET, 'success', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $showSuccess = is_string($successParam) && $successParam !== '';

        $successAddParam = filter_input(INPUT_GET, 'success-add', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $showSuccessAdd = is_string($successAddParam) && $successAddParam !== '';

        $removeParam = filter_input(INPUT_GET, 'remove', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $showRemove = is_string($removeParam) && $removeParam !== '';

        $notaParamRaw = filter_input(INPUT_GET, 'nota', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
        $notaAction = is_string($notaParamRaw) ? trim($notaParamRaw) : '';
        $isNotaYes = ($notaAction === 'yes');
?>
        <h4>Keranjang Penjualan</h4>
        <br>
        <?php if($showSuccess){?>
        <div class="alert alert-success">
                <p>Edit Data Berhasil !</p>
        </div>
        <?php }?>
        <?php if($showSuccessAdd){?>
        <div class="alert alert-success">
                <p>Customer berhasil ditambahkan!</p>
        </div>
        <?php }?>
        <?php if($showRemove){?>
        <div class="alert alert-danger">
                <p>Hapus Data Berhasil !</p>
        </div>
        <?php }?>
	<div class="row">
		<div class="col-sm-4">
			<div class="card card-primary mb-3">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-search"></i> Cari Barang</h5>
				</div>
				<div class="card-body">
					<input type="text" id="cari" class="form-control" name="cari" placeholder="Masukan : Kode / Nama Barang  [ENTER]">
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<div class="card card-primary mb-3">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-list"></i> Hasil Pencarian</h5>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<div id="hasil_cari"></div>
						<div id="tunggu"></div>
					</div>
				</div>
			</div>
		</div>
		
		<!--  Customer Selection -->
		<div class="col-sm-12">
			<div class="card card-success mb-3">
				<div class="card-header bg-success text-white">
					<h5><i class="fa fa-user"></i> Customer / Member</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label>Pilih Customer</label>
								<select class="form-control" id="customer_select" name="customer_id">
									<option value="">-- Pilih Customer (Opsional) --</option>
									<?php 
									$customers = $lihat->customer_aktif();
									foreach($customers as $cust) {
										$selected = '';
										if(isset($_SESSION['customer_aktif']) && $_SESSION['customer_aktif'] == $cust['id_customer']) {
											$selected = 'selected';
										}
									?>
									<option value="<?= htmlspecialchars((string)$cust['id_customer'], ENT_QUOTES, 'UTF-8');?>" 
										data-nama="<?= htmlspecialchars($cust['nama_customer'], ENT_QUOTES, 'UTF-8');?>"
										data-phone="<?= htmlspecialchars($cust['no_telepon'], ENT_QUOTES, 'UTF-8');?>"
										data-poin="<?= htmlspecialchars((string)$cust['poin_diskon'], ENT_QUOTES, 'UTF-8');?>"
										<?= $selected;?>>
										<?= htmlspecialchars($cust['nama_customer'], ENT_QUOTES, 'UTF-8');?> - <?= htmlspecialchars($cust['no_telepon'], ENT_QUOTES, 'UTF-8');?>
									</option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<label>&nbsp;</label><br>
							<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalTambahCustomerKasir">
								<i class="fa fa-plus"></i> Tambah Customer Baru
							</button>
						</div>
					</div>
					<div id="customer-info" style="display:none;" class="alert alert-info mt-2">
						<strong>Customer:</strong> <span id="info-nama"></span><br>
						<strong>No Telepon:</strong> <span id="info-phone"></span><br>
						<strong>Poin Tersedia:</strong> <span id="info-poin"></span> Poin = Rp <span id="info-nilai-poin"></span>
						<div class="mt-2">
							<label><strong>Gunakan Poin:</strong></label>
							<input type="number" class="form-control" id="poin_digunakan" min="0" value="0" placeholder="Masukkan jumlah poin">
							<small class="text-muted d-block">
								<i class="fa fa-info-circle"></i> <strong>Mekanisme Poin:</strong><br>
								• <strong>Gunakan:</strong> 1 Poin = Rp 1.000 diskon<br>
								• <strong>Dapatkan:</strong> Rp 50.000 belanja = +1 Poin<br>
								• <strong>Bonus:</strong> Diskon Member 2% otomatis!
							</small>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-12">
			<div class="card card-primary">
				<div class="card-header bg-primary text-white">
					<h5><i class="fa fa-shopping-cart"></i> KASIR</h5>
				</div>
				<div class="card-body">
					<div id="keranjang" class="table-responsive">
						<table class="table table-bordered">
							<tr>
								<td><b>Tanggal</b></td>
                                                                <td><input type="text" readonly="readonly" class="form-control" value="<?= htmlspecialchars(date('j F Y, G:i'), ENT_QUOTES, 'UTF-8');?>" name="tgl"></td>
							</tr>
						</table>
						<table class="table table-bordered w-100" id="example1">
							<thead>
								<tr>
									<td> No</td>
									<td> Nama Barang</td>
									<td style="width:10%;"> Jumlah</td>
									<td style="width:15%;"> Harga Satuan</td>
									<td style="width:20%;"> Total</td>
									<td> Kasir</td>
									<td> Aksi</td>
								</tr>
							</thead>
							<tbody>
								<?php $total_bayar=0; $no=1; $hasil_penjualan = $lihat -> penjualan();?>
								<?php foreach($hasil_penjualan  as $isi){
									$subtotal = $isi['total'];
									$hapusUrl = "fungsi/hapus/hapus.php?jual=jual&id=" . htmlspecialchars($isi['id_penjualan'], ENT_QUOTES, 'UTF-8') . 
									            "&brg=" . htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8') . 
									            "&jml=" . urlencode($isi['jumlah']) . 
									            "&csrf_token=" . urlencode(csrf_get_token());
								?>
								<tr>
									<td><?= htmlspecialchars((string) $no, ENT_QUOTES, 'UTF-8');?></td>
									<td><?= htmlspecialchars($isi['nama_barang'], ENT_QUOTES, 'UTF-8');?></td>
									<td>
                                                <form method="POST" action="#" class="form-update-jumlah">
                                                <?php echo csrf_field(); ?>
												<input type="number" name="jumlah" value="<?= htmlspecialchars($isi['jumlah'], ENT_QUOTES, 'UTF-8');?>" class="form-control" min="1">
												<input type="hidden" name="id" value="<?= htmlspecialchars($isi['id_penjualan'], ENT_QUOTES, 'UTF-8');?>" class="form-control">
												<input type="hidden" name="id_barang" value="<?= htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8');?>" class="form-control">
											</td>
											<td>Rp.<?php 
												$harga_satuan = $isi['jumlah'] > 0 ? $isi['total'] / $isi['jumlah'] : 0;
												echo number_format($harga_satuan, 0, ',', '.');
											?>,-</td>
											<td>Rp.<?php echo number_format($isi['total']);?>,-</td>
											<td><?= htmlspecialchars($isi['nm_member'], ENT_QUOTES, 'UTF-8');?></td>
											<td>
												<button type="submit" class="btn btn-warning btn-sm">Update</button>
										</form>
                                            <button type="button" 
                                            class="btn btn-danger btn-sm btn-hapus-barang mt-1" 
                                            data-url="<?= htmlspecialchars($hapusUrl, ENT_QUOTES, 'UTF-8');?>"
                                            data-nama="<?= htmlspecialchars($isi['nama_barang'], ENT_QUOTES, 'UTF-8');?>">
                                            <i class="fa fa-times"></i> Hapus
										</button>
									</td>
								</tr>
								<?php $no++; $total_bayar += $subtotal;}?>
							</tbody>
					</table>
					<br/>
					<?php $hasil = $lihat -> jumlah(); ?>
					<div id="kasirnya">
						<?php
						// proses bayar dan ke nota
                                                        $total = 0.0;
                                                        $bayar = 0.0;
                                                        $hitung = 0.0;
											$diskon_persen = 0.0;
											$diskon_nominal = 0.0;
											$total_akhir = 0.0;
											$poin_digunakan = 0;
                                                        if($isNotaYes && $_SERVER['REQUEST_METHOD'] === 'POST') {
                                                                $totalInput = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
                                                                $bayarInput = filter_input(INPUT_POST, 'bayar', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
												$customerIdInput = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
												$diskonPersenInput = filter_input(INPUT_POST, 'diskon_persen', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
												$diskonNominalInput = filter_input(INPUT_POST, 'diskon_nominal', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
												$totalAkhirInput = filter_input(INPUT_POST, 'total_akhir', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_FLAG_ALLOW_FRACTION]);
												$poinDigunakanInput = filter_input(INPUT_POST, 'poin_digunakan', FILTER_VALIDATE_INT);
												
                                                                if($totalInput !== null && $totalInput !== false && $totalInput !== '') {
                                                                        $total = (float) $totalInput;
                                                                }
                                                                if($bayarInput !== null && $bayarInput !== false && $bayarInput !== '') {
                                                                        $bayar = (float) $bayarInput;
                                                                }
												$customerId = ($customerIdInput !== null && $customerIdInput !== false) ? (int) $customerIdInput : 0;
												if($diskonPersenInput !== null && $diskonPersenInput !== false && $diskonPersenInput !== '') {
													$diskon_persen = (float) $diskonPersenInput;
												}
												if($diskonNominalInput !== null && $diskonNominalInput !== false && $diskonNominalInput !== '') {
													$diskon_nominal = (float) $diskonNominalInput;
												}
												if($totalAkhirInput !== null && $totalAkhirInput !== false && $totalAkhirInput !== '') {
													$total_akhir = (float) $totalAkhirInput;
												} else {
													$total_akhir = $total - $diskon_nominal;
												}
												if($poinDigunakanInput !== null && $poinDigunakanInput !== false) {
													$poin_digunakan = (int) $poinDigunakanInput;
												}

                                                                if($bayar > 0.0) {
                                                                        if($bayar >= $total_akhir) {
                                                                                $hitung = $bayar - $total_akhir;  // Kembalian positif
                                                                                $idBarangList = filter_input(INPUT_POST, 'id_barang', FILTER_DEFAULT, ['flags' => FILTER_REQUIRE_ARRAY]);
                                                                                $idMemberList = filter_input(INPUT_POST, 'id_member', FILTER_DEFAULT, ['flags' => FILTER_REQUIRE_ARRAY]);
                                                                                $jumlahList = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT, ['flags' => FILTER_REQUIRE_ARRAY]);
                                                                                $totalList = filter_input(INPUT_POST, 'total1', FILTER_SANITIZE_NUMBER_FLOAT, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_ALLOW_FRACTION]);
                                                                                $tglInputList = filter_input(INPUT_POST, 'tgl_input', FILTER_UNSAFE_RAW, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES]);
                                                                                $periodeList = filter_input(INPUT_POST, 'periode', FILTER_UNSAFE_RAW, ['flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES]);

                                                                                $jumlahDipilih = is_array($idBarangList) ? count($idBarangList) : 0;
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
																				// Update existing tracking
																				$freq = (int)$existing['frekuensi_beli'] + 1;
																				$sqlUpdate = "UPDATE customer_barang SET terakhir_beli = NOW(), frekuensi_beli = ?, jumlah_terakhir = ? WHERE id_customer = ? AND id_barang = ?";
																				$rowUpdate = $config->prepare($sqlUpdate);
																				$rowUpdate->execute([$freq, $jumlahItem, $customerId, $barangId]);
																			} else {
																				// Insert new tracking
																				$sqlInsert = "INSERT INTO customer_barang (id_customer, id_barang, terakhir_beli, frekuensi_beli, jumlah_terakhir) VALUES (?, ?, NOW(), 1, ?)";
																				$rowInsert = $config->prepare($sqlInsert);
																				$rowInsert->execute([$customerId, $barangId, $jumlahItem]);
																			}
																		}
                                                                                }
                                                                                }
																	
// Update total belanja customer, kurangi poin yang digunakan, dan tambah poin dari belanja baru
											if($customerId > 0) {
												// Hitung poin yang didapat dari transaksi ini
												// 1 poin untuk setiap Rp 50.000 belanja (sebelum diskon)
												$poinDidapat = floor($total / 50000);
												
												// Update: tambah total belanja, kurangi poin yang digunakan, tambah poin yang didapat
												$sqlUpdateCustomer = "UPDATE customer SET total_belanja = total_belanja + ?, poin_diskon = poin_diskon - ? + ? WHERE id_customer = ?";
												$rowUpdateCustomer = $config->prepare($sqlUpdateCustomer);
												$rowUpdateCustomer->execute([$total_akhir, $poin_digunakan, $poinDidapat, $customerId]);
												
												// Simpan info poin untuk ditampilkan ke user
												$infoPoin = '';
												if($poinDidapat > 0 || $poin_digunakan > 0) {
													$infoPoin = '\n\n💰 INFO POIN:';
													if($poin_digunakan > 0) {
														$infoPoin .= '\n- Poin digunakan: '.$poin_digunakan.' poin';
													}
													if($poinDidapat > 0) {
														$infoPoin .= '\n- Poin didapat: +'.$poinDidapat.' poin';
													}
													// Hitung sisa poin
													$sqlGetPoin = "SELECT poin_diskon FROM customer WHERE id_customer = ?";
													$rowGetPoin = $config->prepare($sqlGetPoin);
													$rowGetPoin->execute([$customerId]);
													$dataPoin = $rowGetPoin->fetch();
													if($dataPoin) {
														$infoPoin .= '\n- Sisa poin: '.$dataPoin['poin_diskon'].' poin';
													}
												}
											} else {
												$poinDidapat = 0;
												$infoPoin = '';
											}
																	
                                                                                // Auto print struk setelah bayar sukses
                                                                                $nmMember = urlencode($_SESSION['admin']['nm_member']);
                                                                                $bayarEnc = urlencode($bayar);
                                                                                $kembaliEnc = urlencode($hitung);
                                                                                echo '<script>';
                                                                                echo 'alert("Belanjaan Berhasil Di Bayar!'.$infoPoin.'");';
                                                                                echo 'window.open("print.php?nm_member='.$nmMember.'&bayar='.$bayarEnc.'&kembali='.$kembaliEnc.'", "_blank");';
                                                                                echo 'setTimeout(function(){ window.location="fungsi/hapus/hapus.php?penjualan=jual&csrf_token='.urlencode(csrf_get_token()).'"; }, 1000);';
                                                                                echo '</script>';
                                                                        } else {
                                                                                echo '<script>alert("Uang Kurang ! Rp.'.number_format($hitung, 0, ',', '.').'");</script>';
                                                                        }
                                                                }
                                                        }
						?>
						<!-- aksi ke table nota -->
                                                <form method="POST" action="#" id="form-bayar">
                                                        <?php echo csrf_field(); ?>
							<table class="table table-stripped">
								<?php foreach($hasil_penjualan as $isi){;?>
									<input type="hidden" name="id_barang[]" value="<?= htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8');?>">
									<input type="hidden" name="id_member[]" value="<?= htmlspecialchars($isi['id_member'], ENT_QUOTES, 'UTF-8');?>">
									<input type="hidden" name="jumlah[]" value="<?= htmlspecialchars($isi['jumlah'], ENT_QUOTES, 'UTF-8');?>">
									<input type="hidden" name="total1[]" value="<?= htmlspecialchars($isi['total'], ENT_QUOTES, 'UTF-8');?>">
									<input type="hidden" name="tgl_input[]" value="<?= htmlspecialchars($isi['tanggal_input'], ENT_QUOTES, 'UTF-8');?>">
                                                                        <input type="hidden" name="periode[]" value="<?= htmlspecialchars(date('m-Y'), ENT_QUOTES, 'UTF-8');?>">
								<?php $no++; }?>
								<input type="hidden" name="customer_id" id="customer_id_hidden" value="">
								<input type="hidden" name="diskon_persen" id="diskon_persen_hidden" value="0">
								<input type="hidden" name="diskon_nominal" id="diskon_nominal_hidden" value="0">
								<input type="hidden" name="total_akhir" id="total_akhir_hidden" value="<?= htmlspecialchars((string) $total_bayar, ENT_QUOTES, 'UTF-8');?>">
								<input type="hidden" name="poin_digunakan" id="poin_digunakan_hidden" value="0">
								<input type="hidden" name="bayar" id="bayar_hidden" value="0">
								
								<tr>
									<td><strong>Total Semua</strong></td>
									<td><input type="text" class="form-control" id="total_semua_display" value="Rp <?= number_format($total_bayar, 0, ',', '.');?>" readonly style="font-weight: bold;">
									<input type="hidden" name="total" id="total_semua" value="<?= htmlspecialchars((string) $total_bayar, ENT_QUOTES, 'UTF-8');?>"></td>
								</tr>
								<tr id="diskon-member-row" style="display:none;">
									<td><strong>Diskon Member (2%)</strong></td>
									<td>
										<input type="text" class="form-control" id="diskon_member" value="Rp 0" readonly style="font-weight: bold; color: #28a745;">
									</td>
								</tr>
								<tr id="diskon-poin-row" style="display:none;">
									<td><strong>Diskon Poin</strong></td>
									<td>
										<input type="text" class="form-control" id="diskon_poin" value="Rp 0" readonly style="font-weight: bold; color: #28a745;">
									</td>
								</tr>
								<tr>
									<td>Diskon Tambahan (%)</td>
									<td>
										<input type="number" step="0.01" class="form-control" id="diskon_persen" value="0" min="0" max="100">
										<small class="text-muted">Diskon tambahan selain diskon member</small>
									</td>
								</tr>
								<tr>
									<td><strong>Total Diskon</strong></td>
									<td>
										<input type="text" class="form-control" id="diskon_nominal" value="Rp 0" readonly style="font-weight: bold; color: #28a745;">
									</td>
								</tr>
								<tr>
									<td><strong>Total Akhir</strong></td>
									<td><input type="text" class="form-control" id="total_akhir" value="Rp <?= number_format($total_bayar, 0, ',', '.');?>" readonly style="font-weight: bold; font-size: 1.3em; color: #dc3545;"></td>
								</tr>
								<tr>
									<td><strong>Bayar</strong></td>
									<td><input type="text" class="form-control" id="bayar" value="" placeholder="Masukkan jumlah bayar" style="font-size: 1.1em;"></td>
									<td><button class="btn btn-success btn-lg" type="submit"><i class="fa fa-shopping-cart"></i> BAYAR</button></td>
								</tr>
							<!-- aksi ke table nota -->
							<tr>
								<td><strong>Kembali</strong></td>
								<td colspan="2"><input type="text" class="form-control" id="kembali" value="<?php echo 'Rp '.number_format($hitung, 0, ',', '.');?>" readonly style="font-weight: bold; font-size: 1.3em; color: #007bff;"></td>
							</tr>
						</table>
					</form>
						<br/>
						<br/>
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- Modal Konfirmasi Hapus Barang -->
<div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center py-4">
				<div class="mb-3">
					<i class="fa fa-trash" style="font-size:48px;color:#dc3545;"></i>
				</div>
				<h5>Yakin ingin menghapus item ini?</h5>
				<p id="nama-barang-hapus" class="mb-0"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-danger" id="btn-confirm-hapus"><i class="fa fa-trash"></i> Ya, Hapus</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Tambah Customer dari Kasir -->
<div class="modal fade" id="modalTambahCustomerKasir" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Customer Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="#" id="form-tambah-customer-kasir">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Customer *</label>
                        <input type="text" class="form-control" name="nama_customer" id="nama_customer_kasir" required>
                    </div>
                    <div class="form-group">
                        <label>No Telepon (WhatsApp) *</label>
                        <input type="text" class="form-control" name="no_telepon" id="no_telepon_kasir" required 
                            placeholder="Contoh: 628123456789">
                        <small class="text-muted">Format: 62xxx (gunakan 62 untuk kode Indonesia)</small>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat_kasir" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email_kasir">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-customer">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes slideInRight {
	from {
		transform: translateX(100%);
		opacity: 0;
	}
	to {
		transform: translateX(0);
		opacity: 1;
	}
}
</style>
	

<script>
// AJAX call for autocomplete 
$(document).ready(function(){
	// Modern notification helper function
	function showNotification(message, type, duration) {
		type = type || 'success'; // success, danger, warning, info
		duration = duration || 4000;
		
		var bgClass = 'alert-' + type;
		var icon = type === 'success' ? 'check-circle' : 
				   type === 'danger' ? 'exclamation-circle' : 
				   type === 'warning' ? 'exclamation-triangle' : 'info-circle';
		
		var notification = $('<div class="alert ' + bgClass + ' alert-dismissible" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;max-width:500px;box-shadow:0 4px 12px rgba(0,0,0,0.15);animation:slideInRight 0.3s ease-out;"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fa fa-' + icon + '"></i> ' + message + '</div>');
		$('body').append(notification);
		
		setTimeout(function(){ 
			notification.fadeOut(400, function() {
				$(this).remove();
			}); 
		}, duration);
	}
	
	// Function untuk reload keranjang
	function reloadKeranjang() {
		// Simpan nilai customer dan diskon saat ini
		var currentCustomer = $("#customer_select").val();
		var currentDiskonPersen = $("#diskon_persen").val();
		
		$.ajax({
			type: "GET",
			url: "fungsi/view/keranjang_ajax.php?keranjang=reload&_t=" + new Date().getTime(),
			dataType: 'json',
			success: function(response){
				// Update HTML keranjang
				$("#keranjang").html(response.html);
				
				// Update total fields secara eksplisit
				var total = response.total || 0;
				$("#total_semua").val(total);
				$("#total_semua_display").val("Rp " + response.total_formatted);
				$("#total_akhir_hidden").val(total);
				
				// Re-bind event handlers setelah reload
				bindKeranjangEvents();
				
				// Restore customer selection jika ada
				if(currentCustomer) {
					$("#customer_select").val(currentCustomer);
					setTimeout(function() {
						$("#customer_select").trigger('change');
					}, 50);
				}
				
				// Restore diskon persen jika ada
				if(currentDiskonPersen && parseFloat(currentDiskonPersen) > 0) {
					$("#diskon_persen").val(currentDiskonPersen);
				}
				
				// Hitung diskon dan update display
				setTimeout(function() {
					hitungDiskon();
				}, 150);
			},
			error: function(xhr, status, error){
				showNotification('Gagal memuat keranjang', 'danger');
			}
		});
	}
	
	// Variable untuk menyimpan data hapus sementara
	var deleteData = {};
	
	// Event delegation untuk button hapus barang - tidak perlu re-bind
	$(document).on('click', '.btn-hapus-barang', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var url = $(this).data('url');
		url = url + (url.indexOf('?') > -1 ? '&' : '?') + 'ajax=1';
		var namaBarang = $(this).data('nama');
		var btn = $(this);
		
		// Simpan data untuk digunakan saat confirm
		deleteData = {
			url: url,
			namaBarang: namaBarang,
			button: btn
		};
		
		// Tampilkan modal konfirmasi modern
		$('#nama-barang-hapus').html('<strong>' + namaBarang + '</strong>');
		$('#modalKonfirmasiHapus').modal('show');
	});
	
	// Handler untuk tombol konfirmasi hapus di modal
	$(document).on('click', '#btn-confirm-hapus', function(){
		var url = deleteData.url;
		var namaBarang = deleteData.namaBarang;
		var btn = deleteData.button;
		
		// Tutup modal
		$('#modalKonfirmasiHapus').modal('hide');
		
		if(url && btn) {
			btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
			
			$.ajax({
				type: "GET",
				url: url,
				dataType: 'json',
				success: function(response){
					if(response.success) {
						// Tampilkan notifikasi modern
						showNotification(namaBarang + ' berhasil dihapus dari keranjang', 'info', 3000);
						
						reloadKeranjang();
					} else {
						showNotification(response.message || 'Gagal menghapus barang', 'danger');
						btn.prop('disabled', false).html('<i class="fa fa-times"></i> Hapus');
					}
				},
				error: function(xhr, status, error){
					try {
						var response = JSON.parse(xhr.responseText);
						showNotification(response.message || 'Gagal menghapus barang', 'danger');
					} catch(e) {
						var errorMsg = 'Gagal menghapus barang';
						if(xhr.status === 0) {
							errorMsg += ' (Tidak dapat terhubung ke server)';
						} else if(xhr.status) {
							errorMsg += ' (Error ' + xhr.status + ')';
						}
						showNotification(errorMsg, 'danger');
					}
					btn.prop('disabled', false).html('<i class="fa fa-times"></i> Hapus');
				}
			});
		}
	});
	
	// Event delegation untuk form update jumlah - tidak perlu re-bind
	$(document).on('submit', '.form-update-jumlah', function(e){
		console.log('=== UPDATE FORM SUBMITTED ===');
		e.preventDefault();
		e.stopPropagation();
		
		var formData = $(this).serialize();
		var form = $(this);
		var submitBtn = form.find('button[type="submit"]');
		
		// Disable button
		submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
		
		$.ajax({
			type: "POST",
			url: "fungsi/edit/edit.php?jual=jual&ajax=1",
			data: formData,
			dataType: 'json',
			success: function(response){
				if(response.success) {
					// Tampilkan notifikasi modern
					showNotification('Jumlah berhasil diupdate', 'success', 2000);
					
					reloadKeranjang();
				} else {
					showNotification(response.message || 'Gagal update jumlah', 'danger');
					submitBtn.prop('disabled', false).html('Update');
				}
			},
			error: function(xhr, status, error){
				console.log('AJAX Error (Update):', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error
				});
				
				try {
					var response = JSON.parse(xhr.responseText);
					showNotification(response.message || 'Gagal update jumlah', 'danger');
				} catch(e) {
					var errorMsg = 'Gagal update jumlah';
					if(xhr.status === 0) {
						errorMsg += ' (Tidak dapat terhubung ke server)';
					} else if(xhr.status) {
						errorMsg += ' (Error ' + xhr.status + ')';
					}
					showNotification(errorMsg, 'danger');
					console.error('Response:', xhr.responseText);
				}
				submitBtn.prop('disabled', false).html('Update');
			}
		});
	});
	
	function bindKeranjangEvents() {
		// Function ini tidak diperlukan lagi karena sudah menggunakan event delegation
		// Tapi tetap dipertahankan untuk backward compatibility
	}
	
	// Search barang
	$("#cari").change(function(){
                $.ajax({
                        type: "POST",
                        url: "fungsi/edit/edit.php?cari_barang=yes",
                        data:{keyword: $(this).val(), csrf_token: window.csrfToken || ''},
			beforeSend: function(){
				$("#hasil_cari").hide();
				$("#tunggu").html('<p style="color:green"><blink>tunggu sebentar</blink></p>');
			},
			success: function(html){
				$("#tunggu").html('');
				$("#hasil_cari").show();
				$("#hasil_cari").html(html);
			}
		});
	});
	
	// Handler untuk tombol tambah barang - menggunakan event delegation
	$(document).on('click', '.btn-tambah-barang', function(e){
		e.preventDefault();
		e.stopPropagation();
		e.stopImmediatePropagation();
		
		var url = $(this).data('url');
		// Tambahkan parameter ajax=1
		url = url + (url.indexOf('?') > -1 ? '&' : '?') + 'ajax=1';
		
		var namaBarang = $(this).data('nama');
		var btn = $(this);
		
		// Disable button saat proses
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
		
		$.ajax({
			type: "GET",
			url: url,
			dataType: 'json',
			success: function(response){
				if(response.success) {
					showNotification(namaBarang + ' ditambahkan ke keranjang', 'success', 3000);
					reloadKeranjang();
					$("#cari").val('');
					$("#hasil_cari").html('');
				} else {
					showNotification(response.message || 'Gagal menambahkan barang', 'danger');
				}
			},
			error: function(xhr, status, error){
				try {
					var response = JSON.parse(xhr.responseText);
					showNotification(response.message || 'Gagal menambahkan barang', 'danger');
				} catch(e) {
					var errorMsg = 'Gagal menambahkan barang';
					if(xhr.status === 0) {
						errorMsg += ' (Tidak dapat terhubung ke server)';
					} else if(xhr.status === 404) {
						errorMsg += ' (File tidak ditemukan)';
					} else if(xhr.status === 500) {
						errorMsg += ' (Server error)';
					} else if(xhr.status) {
						errorMsg += ' (Error ' + xhr.status + ')';
					}
					showNotification(errorMsg, 'danger');
				}
			},
			complete: function(){
				btn.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Tambah');
			}
		});
		
		return false;
	});
	
	// Customer selection handler
	$("#customer_select").change(function(){
		var selectedOption = $(this).find('option:selected');
		var customerId = $(this).val();
		
		if(customerId) {
			var nama = selectedOption.data('nama');
			var phone = selectedOption.data('phone');
			var poin = selectedOption.data('poin') || 0;
			var nilaiPoin = poin * 1000; // 1 poin = Rp 1.000
			
			$("#info-nama").text(nama);
			$("#info-phone").text(phone);
			$("#info-poin").text(poin);
			$("#info-nilai-poin").text(nilaiPoin.toLocaleString('id-ID'));
			$("#poin_digunakan").attr('max', poin);
			$("#poin_digunakan").val(0);
			$("#customer-info").show();
			$("#customer_id_hidden").val(customerId);
			$("#diskon-member-row").show();
			$("#diskon-poin-row").show();
			
			// Hitung diskon otomatis (2% untuk member)
			hitungDiskon();
		} else {
			$("#customer-info").hide();
			$("#customer_id_hidden").val('');
			$("#diskon_persen").val(0);
			$("#poin_digunakan").val(0);
			$("#diskon-member-row").hide();
			$("#diskon-poin-row").hide();
			hitungDiskon();
		}
	});
	
	// Poin digunakan handler
	$("#poin_digunakan").on('input', function(){
		var poinMax = parseInt($(this).attr('max')) || 0;
		var poinInput = parseInt($(this).val()) || 0;
		
		if(poinInput > poinMax) {
			$(this).val(poinMax);
		}
		hitungDiskon();
	});
	
	// Handler form tambah customer dari kasir
	$("#form-tambah-customer-kasir").on('submit', function(e){
		e.preventDefault();
		
		var form = $(this);
		var formData = form.serialize();
		var submitBtn = $("#btn-submit-customer");
		
		// Disable button
		submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
		
		$.ajax({
			type: 'POST',
			url: 'fungsi/tambah/tambah.php?customer=tambah&ajax=1',
			data: formData,
			dataType: 'json',
			success: function(response){
				if(response.success) {
					// Tutup modal
					$('#modalTambahCustomerKasir').modal('hide');
					
					// Reset form
					form[0].reset();
					
					// Tampilkan notifikasi sukses
					var notification = $('<div class="alert alert-success alert-dismissible" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:300px;box-shadow:0 4px 8px rgba(0,0,0,0.2);"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Berhasil!</strong> Customer ' + response.nama_customer + ' telah ditambahkan</div>');
					$('body').append(notification);
					setTimeout(function(){ 
						notification.fadeOut(500, function() {
							$(this).remove();
						}); 
					}, 5000);
					
					// Reload customer dropdown dengan cache busting
					$.ajax({
						type: 'GET',
						url: 'fungsi/view/customer_dropdown.php?_t=' + new Date().getTime(),
						dataType: 'json',
						cache: false,
						success: function(customerData){
							if(customerData.success && customerData.customers) {
								// Update dropdown
								var select = $("#customer_select");
								select.empty();
								select.append('<option value="">-- Pilih Customer (Opsional) --</option>');
								
								$.each(customerData.customers, function(i, customer){
									select.append($('<option>', {
										value: customer.id_customer,
										text: customer.nama_customer + ' - ' + customer.no_telepon,
										'data-nama': customer.nama_customer,
										'data-phone': customer.no_telepon,
										'data-poin': customer.poin_diskon
									}));
								});
								
								// Auto select customer yang baru ditambahkan
								if(response.id_customer) {
									select.val(response.id_customer).trigger('change');
								}
								
								console.log('Dropdown customer berhasil di-refresh dengan ' + customerData.customers.length + ' customer');
							} else {
								console.error('Format response customer tidak valid:', customerData);
								showNotification('Customer berhasil ditambahkan, silakan refresh halaman untuk melihat customer baru', 'warning');
							}
						},
						error: function(xhr, status, error){
							console.error('Gagal reload dropdown customer:', {
								status: xhr.status,
								responseText: xhr.responseText,
								error: error
							});
							showNotification('Customer berhasil ditambahkan, silakan refresh halaman untuk melihat customer baru', 'warning');
						}
					});
				} else {
					showNotification(response.message || 'Gagal menambahkan customer', 'danger');
				}
			},
			error: function(xhr, status, error){
				console.log('AJAX Error (Tambah Customer):', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error
				});
				
				try {
					var response = JSON.parse(xhr.responseText);
					showNotification(response.message || 'Gagal menambahkan customer', 'danger');
				} catch(e) {
					showNotification('Terjadi kesalahan saat menambahkan customer', 'danger');
				}
			},
			complete: function(){
				submitBtn.prop('disabled', false).html('Simpan');
			}
		});
	});
	
	// Format number to Rupiah
	function formatRupiah(angka) {
		var number_string = angka.toString().replace(/[^,\d]/g, '');
		var split = number_string.split(',');
		var sisa = split[0].length % 3;
		var rupiah = split[0].substr(0, sisa);
		var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
		
		if(ribuan) {
			var separator = sisa ? '.' : '';
			rupiah += separator + ribuan.join('.');
		}
		
		rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
		return rupiah ? 'Rp ' + rupiah : 'Rp 0';
	}
	
	// Parse Rupiah string to number
	function parseRupiah(rupiah) {
		return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
	}
	
	// Format angka tanpa Rp prefix (untuk input)
	function formatAngka(angka) {
		if(!angka) return '';
		var number_string = angka.toString().replace(/[^,\d]/g, '');
		var split = number_string.split(',');
		var sisa = split[0].length % 3;
		var rupiah = split[0].substr(0, sisa);
		var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
		
		if(ribuan) {
			var separator = sisa ? '.' : '';
			rupiah += separator + ribuan.join('.');
		}
		
		return rupiah;
	}
	
// Setup bayar field - using event delegation, only needs to be setup once
	function setupBayarField() {
		// Event delegation untuk bayar field - tidak perlu re-bind
		if(!window.bayarFieldSetup) {
			// Format input bayar dengan event delegation
			$(document).on('keyup', '#bayar', function(){
				var angka = $(this).val().replace(/[^0-9]/g, '');
				$(this).val(formatAngka(angka));
				$("#bayar_hidden").val(angka);
				hitungKembali();
			});
			
			// Event untuk diskon persen dengan event delegation
			$(document).on('keyup change', '#diskon_persen', function(){
				hitungDiskon();
			});
			
			window.bayarFieldSetup = true;
		}
	}
	
	function hitungDiskon() {
		var total = parseFloat($("#total_semua").val()) || 0;
		var diskonPersenTambahan = parseFloat($("#diskon_persen").val()) || 0;
		var customerId = $("#customer_id_hidden").val();
		var poinDigunakan = parseInt($("#poin_digunakan").val()) || 0;
		
		// Batasi diskon maksimal 100%
		if(diskonPersenTambahan > 100) {
			diskonPersenTambahan = 100;
			$("#diskon_persen").val(100);
		}
		
		// Hitung diskon member 2% jika ada customer
		var diskonMember = 0;
		if(customerId) {
			diskonMember = (total * 2) / 100; // 2% diskon member otomatis
		}
		
		// Hitung diskon dari poin (1 poin = Rp 1000)
		var diskonPoin = poinDigunakan * 1000;
		
		// Hitung diskon tambahan
		var diskonTambahan = (total * diskonPersenTambahan) / 100;
		
		// Total diskon
		var diskonNominal = diskonMember + diskonPoin + diskonTambahan;
		
		// Total akhir
		var totalAkhir = total - diskonNominal;
		if(totalAkhir < 0) totalAkhir = 0;
		
		console.log('  Diskon nominal:', diskonNominal);
		console.log('  Total akhir:', totalAkhir);
		
		// Update tampilan dengan format Rupiah
		$("#diskon_member").val(formatRupiah(diskonMember.toFixed(0)));
		$("#diskon_poin").val(formatRupiah(diskonPoin.toFixed(0)));
		$("#diskon_nominal").val(formatRupiah(diskonNominal.toFixed(0)));
		$("#total_akhir").val(formatRupiah(totalAkhir.toFixed(0)));
		
		// Update hidden fields
		var totalDiskonPersen = ((diskonMember + diskonTambahan) / total * 100) || 0;
		$("#diskon_persen_hidden").val(totalDiskonPersen.toFixed(2));
		$("#diskon_nominal_hidden").val(diskonNominal);
		$("#total_akhir_hidden").val(totalAkhir);
		$("#poin_digunakan_hidden").val(poinDigunakan);
		
		console.log('  Fields updated, calling hitungKembali()');
		hitungKembali();
	}
	
	function hitungKembali() {
		var totalAkhir = parseFloat($("#total_akhir_hidden").val()) || 0;
		var bayar = parseFloat($("#bayar_hidden").val()) || 0;
		var kembali = bayar - totalAkhir;
		
		$("#kembali").val(formatRupiah(kembali.toFixed(0)));
	}
	
	// Form validation before submit - MENGGUNAKAN EVENT DELEGATION agar berfungsi setelah reload
	$(document).on('submit', '#form-bayar', function(e) {
		e.preventDefault();
		console.log('=== FORM BAYAR SUBMIT TRIGGERED ===');
		console.log('Form:', this);
		console.log('Button found:', $(this).find('button[type="submit"]').length);
		
		var bayar = parseFloat($("#bayar_hidden").val()) || 0;
		if(bayar <= 0) {
			showNotification('Masukkan jumlah bayar terlebih dahulu!', 'warning');
			$("#bayar").focus();
			return false;
		}
		
		var form = $(this);
		var formData = form.serialize() + '&proses_bayar_ajax=1';
		var submitBtn = form.find('button[type="submit"]');
		
		// Disable button
		submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
		
		$.ajax({
			type: 'POST',
			url: 'fungsi/edit/edit.php',
			data: formData,
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					// Format info pembayaran
					var kembalianFormatted = 'Rp ' + parseFloat(response.kembalian).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
					var bayarFormatted = 'Rp ' + parseFloat(response.bayar).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
					
					// Tampilkan notifikasi besar pembayaran berhasil
					var paymentNotif = $('<div class="alert alert-success alert-dismissible" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:400px;box-shadow:0 6px 16px rgba(0,0,0,0.2);font-size:1.1em;"><button type="button" class="close" data-dismiss="alert">&times;</button><h4><i class="fa fa-check-circle"></i> Pembayaran Berhasil!</h4><p style="margin:10px 0;"><strong>Bayar:</strong> ' + bayarFormatted + '<br><strong>Kembalian:</strong> <span style="color:#28a745;font-size:1.3em;font-weight:bold;">' + kembalianFormatted + '</span></p><p style="margin-top:10px;"><i class="fa fa-print"></i> Struk akan dicetak...</p></div>');
					$('body').append(paymentNotif);
					setTimeout(function(){ 
						paymentNotif.fadeOut(500, function() {
							$(this).remove();
						}); 
					}, 8000);
					
					// Buka print window
					var printUrl = 'print.php?nm_member=' + encodeURIComponent(response.nm_member) + 
								   '&bayar=' + encodeURIComponent(response.bayar) + 
								   '&kembali=' + encodeURIComponent(response.kembalian);
					window.open(printUrl, '_blank');
					
					// Clear keranjang via AJAX tanpa reload
					$.ajax({
						type: 'GET',
						url: 'fungsi/hapus/hapus.php?penjualan=jual&ajax=1&csrf_token=' + encodeURIComponent(window.csrfToken || ''),
						dataType: 'json',
						success: function(clearResponse) {
							// Reset form dan reload keranjang
							$("#form-bayar")[0].reset();
							$("#bayar").val('');
							$("#bayar_hidden").val('0');
							$("#kembali").val('');
							$("#customer_select").val('').trigger('change');
							$("#diskon_persen").val('0');
							
							// Reload keranjang untuk tampilkan kosong
							reloadKeranjang();
							
							// Note: Button baru dari reloadKeranjang() sudah enabled dan siap diklik
							// Event delegation otomatis menangani form baru
							console.log('Payment complete, cart reloaded, button ready');
						},
						error: function() {
							// Jika clear gagal, fallback ke reload page
							window.location.href = 'index.php?page=jual';
						}
					});
				} else {
					showNotification(response.message || 'Pembayaran gagal', 'danger', 6000);
					submitBtn.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> BAYAR');
				}
			},
			error: function(xhr, status, error) {
				console.error('Payment Error:', {
					status: xhr.status,
					statusText: xhr.statusText,
					responseText: xhr.responseText,
					error: error
				});
				
				try {
					var response = JSON.parse(xhr.responseText);
					showNotification(response.message || 'Pembayaran gagal', 'danger', 6000);
				} catch(e) {
					showNotification('Terjadi kesalahan saat memproses pembayaran', 'danger', 6000);
				}
				
				submitBtn.prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> BAYAR');
			}
		});
		
		return false;
	});
	
	// Prevent default form submission on Enter key in input fields
	$(document).on('keypress', '.form-update-jumlah input[type="number"]', function(e) {
		if(e.which === 13) { // Enter key
			e.preventDefault();
			$(this).closest('form').submit();
			return false;
		}
	});
	
	// Bind events saat halaman pertama kali load
	bindKeranjangEvents();
	
	// Setup bayar field saat pertama kali load
	setupBayarField();
	
	// Hitung total dan diskon saat page load
	setTimeout(function() {
		console.log('Initial hitungDiskon on page load');
		hitungDiskon();
	}, 200);
	
	// Auto-select customer if session exists
	<?php if(isset($_SESSION['customer_aktif'])) { ?>
		$("#customer_select").trigger('change');
	<?php } ?>
});
//To select country name
</script>
