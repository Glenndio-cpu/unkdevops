<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil daftar transaksi
$stmt = $conn->prepare("SELECT type, amount, description, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Histori Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Histori Transaksi</h3>

        <a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah (Rp)</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td><?= number_format($row['amount'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>