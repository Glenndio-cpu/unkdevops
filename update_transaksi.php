<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pastikan ada parameter transaction_id yang dikirim
if (!isset($_GET['transaction_id'])) {
    header("Location: histori.php");
    exit;
}

$transaction_id = $_GET['transaction_id'];

// Ambil data transaksi yang akan diupdate
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $transaction_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    // Jika transaksi tidak ditemukan
    header("Location: histori.php");
    exit;
}

$categories = ['Makanan', 'Minuman', 'Transportasi', 'Kesehatan', 'Hiburan', 'Lainnya'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Update Transaksi</h3>

        <form action="proses_histori.php" method="POST">
            <input type="hidden" name="transaction_id" value="<?= $transaction['id'] ?>">
            <input type="hidden" name="action" value="update">

            <div class="mb-3">
                <label for="type" class="form-label">Jenis Transaksi</label>
                <select name="type" class="form-select" required>
                    <option value="pemasukan" <?= $transaction['type'] == 'pemasukan' ? 'selected' : '' ?>>Pemasukan
                    </option>
                    <option value="pengeluaran" <?= $transaction['type'] == 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Jumlah (Rp)</label>
                <input type="number" name="amount" step="0.01" class="form-control"
                    value="<?= $transaction['amount'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-control" value="<?= $transaction['description'] ?>">
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category ?>" <?= $category == $transaction['category'] ? 'selected' : '' ?>>
                            <?= $category ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="histori.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

</body>

</html>