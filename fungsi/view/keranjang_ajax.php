<?php
// Prevent caching of AJAX responses
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: application/json');

session_start();
if (!empty($_SESSION['admin'])) {
    require '../../config.php';
    require_once __DIR__.'/../csrf.php';
    
    if (!empty($_GET['keranjang']) && $_GET['keranjang'] === 'reload') {
        $id_member = $_SESSION['admin']['id_member'];
        
        // Get keranjang items
        $sql = "SELECT penjualan.*, barang.nama_barang, member.nm_member 
                FROM penjualan 
                INNER JOIN barang ON penjualan.id_barang = barang.id_barang 
                INNER JOIN member ON penjualan.id_member = member.id_member 
                ORDER BY penjualan.id_penjualan DESC";
        $row = $config->prepare($sql);
        $row->execute();
        $hasil_penjualan = $row->fetchAll();
        
        $total_bayar = 0;
        $no = 1;
        
        // Start output buffering untuk HTML
        ob_start();
        ?>
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
                <?php foreach($hasil_penjualan as $isi){
                    $subtotal = $isi['total'];
                    $total_bayar += $subtotal;
                    $hapusUrl = "fungsi/hapus/hapus.php?jual=jual&id=" . htmlspecialchars($isi['id_penjualan'], ENT_QUOTES, 'UTF-8') . 
                                "&brg=" . htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8') . 
                                "&jml=" . urlencode($isi['jumlah']) . 
                                "&csrf_token=" . urlencode(csrf_get_token());
                ?>
                <tr>
                    <td><?= htmlspecialchars((string) $no, ENT_QUOTES, 'UTF-8');?></td>
                    <td><?= htmlspecialchars($isi['nama_barang'], ENT_QUOTES, 'UTF-8');?></td>
                    <td>
                        <form method="POST" action="fungsi/edit/edit.php?jual=jual" class="form-update-jumlah">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="jumlah" value="<?= htmlspecialchars($isi['jumlah'], ENT_QUOTES, 'UTF-8');?>" class="form-control" min="1">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($isi['id_penjualan'], ENT_QUOTES, 'UTF-8');?>">
                            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8');?>">
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
                <?php $no++; }?>
            </tbody>
        </table>
        <br/>
        <div id="kasirnya">
            <!-- aksi ke table nota -->
            <form method="POST" action="#" id="form-bayar">
                <?php echo csrf_field(); ?>
                <table class="table table-stripped">
                    <?php 
                    $no2 = 1;
                    foreach($hasil_penjualan as $isi) { ?>
                        <input type="hidden" name="id_barang[]" value="<?= htmlspecialchars($isi['id_barang'], ENT_QUOTES, 'UTF-8');?>">
                        <input type="hidden" name="id_member[]" value="<?= htmlspecialchars($isi['id_member'], ENT_QUOTES, 'UTF-8');?>">
                        <input type="hidden" name="jumlah[]" value="<?= htmlspecialchars($isi['jumlah'], ENT_QUOTES, 'UTF-8');?>">
                        <input type="hidden" name="total1[]" value="<?= htmlspecialchars($isi['total'], ENT_QUOTES, 'UTF-8');?>">
                        <input type="hidden" name="tgl_input[]" value="<?= htmlspecialchars($isi['tanggal_input'], ENT_QUOTES, 'UTF-8');?>">
                        <input type="hidden" name="periode[]" value="<?= htmlspecialchars(date('m-Y'), ENT_QUOTES, 'UTF-8');?>">
                    <?php $no2++; } ?>
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
                        <td colspan="2"><input type="text" class="form-control" id="kembali" value="Rp 0" readonly style="font-weight: bold; font-size: 1.3em; color: #007bff;"></td>
                    </tr>
                </table>
            </form>
            <br/>
            <br/>
        </div>
        <?php
        $html = ob_get_clean();
        
        // Return combined data
        echo json_encode([
            'html' => $html,
            'total' => $total_bayar,
            'total_formatted' => number_format($total_bayar, 0, ',', '.')
        ]);
        exit;
    }
}
?>
