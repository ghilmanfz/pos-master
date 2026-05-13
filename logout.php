<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
Swal.fire({
    title: "Logout Berhasil!",
    text: "Sampai jumpa kembali",
    icon: "success",
    showConfirmButton: false,
    timer: 1500
}).then(() => {
    window.location = "login.php";
});
</script>

</body>
</html>
