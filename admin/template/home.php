<h3>Dashboard</h3>
<br/>
<?php 
    $sql="SELECT * FROM barang WHERE stok <= 3";
    $row = $config->prepare($sql);
    $row->execute();
    $r = $row->rowCount();
    if($r > 0){
        echo "
        <div class='alert alert-warning'>
            <span class='glyphicon glyphicon-info-sign'></span> 
            Ada <span style='color:red'>$r</span> barang yang Stok tersisa sudah kurang dari 3 items. silahkan pesan lagi !!
            <span class='pull-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
        </div>
        ";  
    }
?>

<?php 
    $hasil_barang = $lihat->barang_row();
    $hasil_kategori = $lihat->kategori_row();
    $stok = $lihat->barang_stok_row();
    $jual = $lihat->jual_row();
    $role = $_SESSION['admin']['role']; // ambil role user
?>

<div class="row">
    <!-- STATISTIK UNTUK SEMUA ROLE -->
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fas fa-cubes"></i> Nama Barang</h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($hasil_barang);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="pt-2"><i class="fas fa-chart-bar"></i> Stok Barang</h6>
            </div>
            <div class="card-body">
                <center>
                    <h1><?php echo number_format($stok['jml']);?></h1>
                </center>
            </div>
            <div class="card-footer">
                <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
            </div>
        </div>
    </div>

    <!-- STATISTIK KHUSUS ADMIN -->
    <?php if($role === 'admin') { ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="pt-2"><i class="fas fa-upload"></i> Telah Terjual</h6>
                </div>
                <div class="card-body">
                    <center>
                        <h1><?php echo number_format($jual['stok']);?></h1>
                    </center>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=laporan'>Tabel laporan <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="pt-2"><i class="fa fa-bookmark"></i> Kategori Barang</h6>
                </div>
                <div class="card-body">
                    <center>
                        <h1><?php echo number_format($hasil_kategori);?></h1>
                    </center>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=kategori'>Tabel Kategori <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
