<?php
session_start();
include "config/db.php";

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil username dari database
$stmt_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->bind_result($username);
$stmt_user->fetch();
$stmt_user->close();

// Query untuk menghitung total pemasukan dan pengeluaran
$query = "
    SELECT 
        SUM(CASE WHEN type = 'pemasukan' THEN amount ELSE 0 END) AS total_pemasukan,
        SUM(CASE WHEN type = 'pengeluaran' THEN amount ELSE 0 END) AS total_pengeluaran
    FROM transactions
    WHERE user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_pemasukan, $total_pengeluaran);
$stmt->fetch();
$stmt->close();

$total_pemasukan = $total_pemasukan ?? 0;
$total_pengeluaran = $total_pengeluaran ?? 0;
$saldo = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Keuangan Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">

                        <h4 class="text-end">Hai, <strong><?= htmlspecialchars($username) ?></strong> 👋</h4>
                        <h3 class="text-center mb-4">Dashboard Keuangan</h3>

                        <div class="row">
                            <div class="col-md-4">
                                <h5>Total Pemasukan</h5>
                                <p>Rp <?= number_format($total_pemasukan, 2, ',', '.') ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Total Pengeluaran</h5>
                                <p>Rp <?= number_format($total_pengeluaran, 2, ',', '.') ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Saldo</h5>
                                <p>Rp <?= number_format($saldo, 2, ',', '.') ?></p>
                            </div>
                        </div>

                        <hr>

                        <a href="tambah.php" class="btn btn-primary">Tambah Transaksi</a>
                        <a href="histori.php" class="btn btn-secondary">Lihat Transaksi</a>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>