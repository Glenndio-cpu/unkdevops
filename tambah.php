<?php
session_start();
include "config/db.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

$categories = ['Makanan', 'Minuman', 'Transportasi', 'Kesehatan', 'Hiburan', 'Lainnya'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Tambah Transaksi</h3>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action="proses_tambah.php">
            <div class="mb-3">
                <label for="type" class="form-label">Jenis Transaksi</label>
                <select name="type" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah (Rp)</label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-control">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category ?>"><?= $category ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

</body>

</html>