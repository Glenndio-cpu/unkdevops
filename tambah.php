<?php
session_start();
include "config/db.php";

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type']; // pemasukan atau pengeluaran
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Insert transaksi ke dalam tabel transactions
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $type, $amount, $description, $category);

    if ($stmt->execute()) {
        // Insert ke transaction_history
        $action = 'Added';
        $action_time = date('Y-m-d H:i:s');
        $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
        $history_stmt->bind_param("iiss", $conn->insert_id, $user_id, $action, $action_time);
        $history_stmt->execute();

        // Redirect ke dashboard setelah sukses
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Tambah Transaksi</h3>

        <div class="card shadow">
            <div class="card-body">
                <form action="tambah.php" method="POST">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Transaksi</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah (Rp)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <input type="text" name="category" id="category" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

</body>

</html>