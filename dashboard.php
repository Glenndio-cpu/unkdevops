<?php
session_start();
include "config/db.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil username
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Hitung total pemasukan & pengeluaran
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Glenndio Umboh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="text-end">Hai, <strong><?= htmlspecialchars($username) ?></strong> 👋</h4>
                        <h3 class="text-center mb-4">Dashboard Keuangan Harian</h3>

                        <div class="row text-center mb-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-white">
                                    <h6>Total Pemasukan</h6>
                                    <p class="text-success fw-bold">Rp
                                        <?= number_format($total_pemasukan, 2, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-white">
                                    <h6>Total Pengeluaran</h6>
                                    <p class="text-danger fw-bold">Rp
                                        <?= number_format($total_pengeluaran, 2, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-white">
                                    <h6>Saldo Saat Ini</h6>
                                    <p class="fw-bold">Rp <?= number_format($saldo, 2, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="tambah.php" class="btn btn-primary">Tambah Transaksi</a>
                            <a href="update.php" class="btn btn-warning text-white">Update Transaksi</a>
                            <a href="histori.php" class="btn btn-secondary">Histori Transaksi</a>
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>