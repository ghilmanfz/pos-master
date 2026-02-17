<?php
// proses simpan user baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config.php';

    $nama    = $_POST['nama'];
    $email   = $_POST['email'];
    $telepon = $_POST['telepon'];
    $nik     = $_POST['nik'];
    $alamat  = $_POST['alamat'];
    $role    = $_POST['role'];
    $user    = $_POST['user'];
    $pass    = md5($_POST['pass']); // sistem lama pakai MD5, sesuaikan dengan login.php

    // upload foto
    $foto = "default.png";
    if (!empty($_FILES['foto']['name'])) {
        $fotoName   = time().'_'.basename($_FILES['foto']['name']);
        $targetPath = "assets/img/user/".$fotoName;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
            $foto = $fotoName;
        }
    }

    try {
        $config->beginTransaction();

        // Insert ke tabel member
        $stmt = $config->prepare("INSERT INTO member 
            (nm_member, email, telepon, NIK, alamat_member, role, gambar) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $email, $telepon, $nik, $alamat, $role, $foto]);

        $id_member = $config->lastInsertId();

        // Insert ke tabel login
        $stmt2 = $config->prepare("INSERT INTO login (id_member, user, pass) VALUES (?, ?, ?)");
        $stmt2->execute([$id_member, $user, $pass]);

        $config->commit();

        echo '<script>window.location="index.php?page=kelola_user/list_user&success=1";</script>';
        exit;
    } catch (Exception $e) {
        $config->rollBack();
        die("Gagal simpan user: ".$e->getMessage());
    }
}
?>

<h4 class="mb-4">Tambah User</h4>

<div class="card shadow-sm col-md-6 offset-md-3 p-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="fa fa-user-plus"></i> Form Tambah User</span>
        <a href="index.php?page=kelola_user/list_user" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="fungsi/tambah/tambah.php?user=tambah">
            <?php echo csrf_field(); ?> 
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="telepon" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">NIK</label>
                <input type="text" name="nik" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" rows="3" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="Kasir">Kasir</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="user" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="pass" class="form-control" required>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Simpan
                </button>
                <a href="index.php?page=kelola_user/list_user" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
