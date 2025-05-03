<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Selamat Datang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="text-center">
            <h1 class="mb-4">Sistem Pencatatan Keuangan Harian</h1>
            <p class="mb-4">Silakan login atau daftar untuk menggunakan aplikasi</p>
            <a href="login.php" class="btn btn-primary me-2">Login</a>
            <a href="register.php" class="btn btn-success">Daftar</a>
        </div>
    </div>

</body>

</html>