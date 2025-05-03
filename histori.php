<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat transaksi dari tabel transaction_history
$query = "SELECT th.*, t.type, t.amount, t.category, t.description
          FROM transaction_history th
          JOIN transactions t ON th.transaction_id = t.id
          WHERE th.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Riwayat Transaksi</h3>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Jumlah (Rp)</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                    <th>Waktu Aksi</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Tombol Update dan Hapus untuk setiap transaksi -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['transaction_id'] ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td><?= $row['category'] ?></td>
                        <td><?= number_format($row['amount'], 2, ',', '.') ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['action'] ?></td>
                        <td><?= $row['action_time'] ?></td>
                        <td>
                            <!-- Formulir Update -->
                            <form action="proses_histori.php" method="POST" style="display:inline;">
                                <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                <input type="hidden" name="action" value="update">
                                <button type="submit" class="btn btn-warning btn-sm">Update</button>
                            </form>

                            <!-- Formulir Delete -->
                            <form action="proses_histori.php" method="POST" style="display:inline;">
                                <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    </div>

</body>

</html>