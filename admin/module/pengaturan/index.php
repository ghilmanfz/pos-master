<?php
$successParam = filter_input(INPUT_GET, 'success', FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES]);
$showSuccess = is_string($successParam) && $successParam !== '';
?>
<h4>Pengaturan Toko</h4>
<br>
<?php if($showSuccess){?>
<div class="alert alert-success">
	<p>Ubah Data Berhasil !</p>
</div>
<?php }?>
<div class="card">
	<div class="card-body">
                <form method="post" action="fungsi/edit/edit.php?pengaturan=ubah">
                        <?php echo csrf_field(); ?>
			<div class="row">
				<div class="col-md 6">
					<div class="form-group">
						<label for="">Nama Toko</label>
						<input class="form-control" name="namatoko" value="<?= htmlspecialchars($toko['nama_toko'] ?? '', ENT_QUOTES, 'UTF-8');?>"
									placeholder="Nama Toko">
					</div>
					<div class="form-group">
						<label for="">Alamat Toko</label>
						<input class="form-control" name="alamat" value="<?= htmlspecialchars($toko['alamat_toko'] ?? '', ENT_QUOTES, 'UTF-8');?>"
									placeholder="Alamat Toko">
					</div>
				</div>
				<div class="col-md 6">
					<div class="form-group">
						<label for="">Kontak (Hp)</label>
						<input class="form-control" name="kontak" value="<?= htmlspecialchars($toko['tlp'] ?? '', ENT_QUOTES, 'UTF-8');?>"
									placeholder="Kontak (Hp)">
					</div>
					<div class="form-group">
						<label for="">Nama Pemilik Toko</label>
						<input class="form-control" name="pemilik" value="<?= htmlspecialchars($toko['nama_pemilik'] ?? '', ENT_QUOTES, 'UTF-8');?>"
									placeholder="Nama Pemilik Toko">
					</div>
				</div>
			</div>
			<!-- Hidden fields for API settings -->
			<input type="hidden" name="api_fonte_token" value="<?= htmlspecialchars($toko['api_fonte_token'] ?? '', ENT_QUOTES, 'UTF-8');?>">
			<input type="hidden" name="api_fonte_phone" value="<?= htmlspecialchars($toko['api_fonte_phone'] ?? '', ENT_QUOTES, 'UTF-8');?>">		<input type="hidden" name="pesan_test" value="<?= htmlspecialchars($toko['pesan_test'] ?? '', ENT_QUOTES, 'UTF-8');?>">			<input type="hidden" name="reminder_aktif" value="<?= htmlspecialchars($toko['reminder_aktif'] ?? 'tidak', ENT_QUOTES, 'UTF-8');?>">
			<button id="tombol-simpan" class="btn btn-primary"><i class="fas fa-edit"></i> Update Data</button>
		</form>
	</div>
</div>

<!-- Pengaturan API Fonnte untuk Reminder WhatsApp -->
<div class="card mb-3">
	<div class="card-header bg-success text-white">
		<h5><i class="fab fa-whatsapp"></i> Pengaturan API Fonnte (WhatsApp Reminder)</h5>
	</div>
	<div class="card-body">
		<div class="alert alert-info">
			<strong>Informasi:</strong> Sistem menggunakan API Fonnte untuk mengirim reminder otomatis ke member/customer via WhatsApp ketika barang yang pernah mereka beli stoknya habis/hampir habis.
			<br><br>
			<strong>Cara Kerja Reminder:</strong>
			<ul>
				<li>Sistem akan otomatis mengambil nomor WhatsApp dari data customer/member</li>
				<li>Reminder dikirim ke nomor WhatsApp yang terdaftar di profil customer</li>
				<li>Nomor testing di bawah hanya untuk test koneksi API saja</li>
			</ul>
			<strong>Cara Setup:</strong>
			<ol>
				<li>Daftar di <a href="https://fonnte.com" target="_blank">https://fonnte.com</a> (bukan fonte.com.br)</li>
				<li>Login dan hubungkan nomor WhatsApp Anda</li>
				<li>Dapatkan API Token dari menu <strong>Account → API</strong></li>
				<li>Masukkan token di bawah dan test koneksi</li>
				<li>Aktifkan fitur reminder otomatis</li>
			</ol>
		</div>
		<form method="post" action="fungsi/edit/edit.php?pengaturan=ubah">
			<?php echo csrf_field(); ?>
			<!-- Duplicate toko fields sebagai hidden untuk tidak hilang datanya -->
			<input type="hidden" name="namatoko" value="<?= htmlspecialchars($toko['nama_toko'] ?? '', ENT_QUOTES, 'UTF-8');?>">
			<input type="hidden" name="alamat" value="<?= htmlspecialchars($toko['alamat_toko'] ?? '', ENT_QUOTES, 'UTF-8');?>">
			<input type="hidden" name="kontak" value="<?= htmlspecialchars($toko['tlp'] ?? '', ENT_QUOTES, 'UTF-8');?>">
			<input type="hidden" name="pemilik" value="<?= htmlspecialchars($toko['nama_pemilik'] ?? '', ENT_QUOTES, 'UTF-8');?>">
			
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="api_fonte_token">API Token Fonnte *</label>
						<input type="text" class="form-control" name="api_fonte_token" id="api_fonte_token"
							value="<?= htmlspecialchars($toko['api_fonte_token'] ?? '', ENT_QUOTES, 'UTF-8');?>"
							placeholder="Masukkan API Token dari Fonnte">
						<small class="text-muted">Login ke <a href="https://fonnte.com" target="_blank">Fonnte.com</a> → Menu Account → API → Copy Token</small>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="api_fonte_phone">Nomor WhatsApp Testing</label>
						<input type="text" class="form-control" name="api_fonte_phone" id="api_fonte_phone"
							value="<?= htmlspecialchars($toko['api_fonte_phone'] ?? '', ENT_QUOTES, 'UTF-8');?>"
							placeholder="Contoh: 628123456789">
						<small class="text-muted">Nomor tujuan untuk test kirim pesan API. Reminder otomatis akan dikirim ke nomor yang terdaftar di data customer/member.</small>
					</div>
				</div>			<div class="col-md-12">
				<div class="form-group">
					<label for="pesan_test">Pesan Test / Template Reminder</label>
					<textarea class="form-control" name="pesan_test" id="pesan_test" rows="4"
						placeholder="Tulis pesan test yang akan dikirim saat klik Test Kirim Pesan"><?= htmlspecialchars($toko['pesan_test'] ?? 'Halo! Ini adalah pesan test dari sistem POS Anda. Jika menerima pesan ini, API sudah berfungsi dengan baik.', ENT_QUOTES, 'UTF-8');?></textarea>
					<small class="text-muted">Pesan ini akan dikirim saat test dan bisa digunakan sebagai template. Gunakan variabel: {nama}, {barang}, {toko}, {phone}</small>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="reminder_aktif">Status Reminder Otomatis</label>
					<select class="form-control" name="reminder_aktif" id="reminder_aktif">
						<option value="tidak" <?= (isset($toko['reminder_aktif']) && $toko['reminder_aktif'] == 'tidak') ? 'selected' : '';?>>Tidak Aktif</option>
						<option value="ya" <?= (isset($toko['reminder_aktif']) && $toko['reminder_aktif'] == 'ya') ? 'selected' : '';?>>Aktif</option>
					</select>
					<small class="text-muted">Aktifkan untuk kirim reminder otomatis ke member</small>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="interval_reminder">Interval Pengiriman Reminder</label>
					<div class="card mb-3">
						<p>3 Hari</p>
						
					</div>
				</div>
			</div>
				<?php if(isset($toko['reminder_terakhir']) && $toko['reminder_terakhir'] != null) { ?>
				<div class="col-md-12">
					<div class="alert alert-secondary">
						<strong>Reminder Terakhir Dikirim:</strong> <?= htmlspecialchars($toko['reminder_terakhir'], ENT_QUOTES, 'UTF-8');?>
					</div>
				</div>
				<?php } ?>
			</div>
			<button type="submit" class="btn btn-success"><i class="fab fa-whatsapp"></i> Simpan Pengaturan API</button>
		</form>
		<hr>
		<div class="mt-3">
			<h6>Test & Implementasi Reminder</h6>
			
			<!-- Mode Test - Manual -->
			<div class="card mb-3">
				<div class="card-header bg-info text-white">
					<strong>🧪 Mode Testing (Manual)</strong>
				</div>
				<div class="card-body">
					<p class="text-muted">Kirim pesan test ke nomor testing + 3 customer terbaru untuk cek koneksi API.</p>
					<button type="button" class="btn btn-info" onclick="testAPIFonte()"><i class="fa fa-paper-plane"></i> Test Kirim Pesan Sekali</button>
				</div>
			</div>
			
			<!-- Mode Implementasi - Auto Reminder -->
			<div class="card mb-3">
				<div class="card-header bg-success text-white">
					<strong>🚀 Mode Implementasi (Reminder Otomatis ke Semua Member)</strong>
				</div>
				<div class="card-body">
					<div class="alert alert-warning">
						<strong>⚠️ Perhatian:</strong><br>
						• Pesan akan dikirim ke <strong>SEMUA customer aktif</strong> yang pernah beli barang stok habis<br>
						• Variabel {nama}, {barang}, {toko}, {phone} akan diganti otomatis per customer<br>
						• Interval pengiriman sesuai pengaturan di atas (1/3/7 hari)<br>
						• Script harus dijalankan via Cron Job atau Task Scheduler
					</div>
					<div class="form-group">
						<label>
							<input type="checkbox" id="auto_reminder" onchange="toggleAutoReminder(this.checked)">
							Aktifkan Reminder Otomatis (Kirim per interval yang dipilih)
						</label>
					</div>
					<button type="button" class="btn btn-success" onclick="sendReminderNow()"><i class="fa fa-rocket"></i> Kirim Reminder Sekarang (ke Semua Member)</button>
				</div>
			</div>
			
			<div id="test-result" class="mt-2"></div>
		</div>
	</div>
</div>

<!-- Log Reminder -->
<div class="card">
	<div class="card-header bg-warning text-dark">
		<h5><i class="fa fa-history"></i> Log Reminder WhatsApp</h5>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="example1">
				<thead>
					<tr>
						<th>No</th>
						<th>Customer</th>
						<th>Barang</th>
						<th>No Telepon</th>
						<th>Pesan</th>
						<th>Status</th>
						<th>Tanggal Kirim</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$no = 1;
					$logs = $lihat->reminder_log();
					if($logs && count($logs) > 0) {
						foreach($logs as $log) {
					?>
					<tr>
						<td><?= htmlspecialchars((string)$no++, ENT_QUOTES, 'UTF-8');?></td>
						<td><?= htmlspecialchars($log['nama_customer'] ?? '-', ENT_QUOTES, 'UTF-8');?></td>
						<td><?= htmlspecialchars($log['nama_barang'] ?? '-', ENT_QUOTES, 'UTF-8');?></td>
						<td><?= htmlspecialchars($log['no_telepon'] ?? '-', ENT_QUOTES, 'UTF-8');?></td>
						<td><?= htmlspecialchars(substr($log['pesan'] ?? '', 0, 50), ENT_QUOTES, 'UTF-8');?>...</td>
						<td>
							<?php 
							$status = $log['status'] ?? 'pending';
							if($status == 'berhasil') {
								echo '<span class="badge badge-success">Berhasil</span>';
							} elseif($status == 'gagal') {
								echo '<span class="badge badge-danger">Gagal</span>';
							} else {
								echo '<span class="badge badge-warning">Pending</span>';
							}
							?>
						</td>
						<td><?= htmlspecialchars($log['tanggal_kirim'] ?? '-', ENT_QUOTES, 'UTF-8');?></td>
					</tr>
					<?php 
						}
					} else {
						echo '<tr><td colspan="7" class="text-center">Belum ada log reminder</td></tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
let autoReminderInterval = null;
let autoTestInterval = null;

function testAPIFonte() {
	const token = document.getElementById('api_fonte_token').value;
	const phone = document.getElementById('api_fonte_phone').value;
	const message = document.getElementById('pesan_test').value;
	const resultDiv = document.getElementById('test-result');
	
	if(!token || !phone) {
		resultDiv.innerHTML = '<div class="alert alert-danger">Harap isi API Token dan Nomor WhatsApp terlebih dahulu!</div>';
		return;
	}
	
	if(!message || message.trim() === '') {
		resultDiv.innerHTML = '<div class="alert alert-danger">Harap isi Pesan Test terlebih dahulu!</div>';
		return;
	}
	
	resultDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Mengirim pesan ke nomor testing + 3 customer...</div>';
	
	// Send test message
	fetch('fungsi/test_api_fonte.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			token: token,
			phone: phone,
			message: message,
			csrf_token: window.csrfToken || ''
		})
	})
	.then(response => response.json())
	.then(data => {
		const timestamp = new Date().toLocaleTimeString('id-ID');
		if(data.success) {
			let html = '<div class="alert alert-success">';
			html += '<strong>[' + timestamp + '] ' + data.message + '</strong><br>';
			if(data.details) {
				html += '<hr><small>' + data.details + '</small>';
			}
			html += '<br><em>Total penerima: ' + (data.total_recipients || 0) + '</em>';
			html += '</div>';
			resultDiv.innerHTML = html;
		} else {
			let html = '<div class="alert alert-danger">';
			html += '<strong>[' + timestamp + '] ' + data.message + '</strong>';
			if(data.details) {
				html += '<br><small>' + data.details + '</small>';
			}
			html += '</div>';
			resultDiv.innerHTML = html;
		}
	})
	.catch(error => {
		resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
	});
}

function sendReminderNow() {
	if(!confirm('Kirim reminder ke SEMUA customer yang pernah beli barang stok habis?')) {
		return;
	}
	
	const resultDiv = document.getElementById('test-result');
	resultDiv.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Mengirim reminder ke semua member...</div>';
	
	fetch('fungsi/send_reminder_now.php', {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			csrf_token: window.csrfToken || ''
		})
	})
	.then(response => response.json())
	.then(data => {
		const timestamp = new Date().toLocaleTimeString('id-ID');
		if(data.success) {
			let html = '<div class="alert alert-success">';
			html += '<strong>[' + timestamp + '] Berhasil!</strong><br>';
			html += data.message;
			if(data.details) {
				html += '<hr><small>' + data.details + '</small>';
			}
			html += '</div>';
			resultDiv.innerHTML = html;
		} else {
			resultDiv.innerHTML = '<div class="alert alert-danger"><strong>[' + timestamp + ']</strong> ' + data.message + '</div>';
		}
	})
	.catch(error => {
		resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
	});
}

function toggleAutoReminder(enabled) {
	const interval = document.getElementById('interval_reminder').value;
	const intervalDays = parseInt(interval);
	const intervalMs = intervalDays * 24 * 60 * 60 * 1000; // days to milliseconds
	
	if(enabled) {
		const resultDiv = document.getElementById('test-result');
		resultDiv.innerHTML = '<div class="alert alert-warning"><strong>⏱️ Auto-reminder aktif!</strong> Mengirim reminder setiap ' + intervalDays + ' hari...</div>';
		
		// Send immediately
		sendReminderNow();
		
		// Then every interval
		autoReminderInterval = setInterval(function() {
			sendReminderNow();
		}, intervalMs);
	} else {
		// Stop auto reminder
		if(autoReminderInterval) {
			clearInterval(autoReminderInterval);
			autoReminderInterval = null;
			const resultDiv = document.getElementById('test-result');
			resultDiv.innerHTML = '<div class="alert alert-secondary">Auto-reminder dihentikan.</div>';
		}
	}
}

// Stop auto reminder when leaving page
window.addEventListener('beforeunload', function() {
	if(autoReminderInterval) {
		clearInterval(autoReminderInterval);
	}
});
</script>