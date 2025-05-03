<?php
// ======= File: histori.php =======
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM transaction_history WHERE user_id = ? ORDER BY action_time DESC";
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
                    <th>ID Transaksi</th>
                    <th>Aksi</th>
                    <th>Waktu Aksi</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['transaction_id'] ?></td>
                        <td><?= $row['action'] ?></td>
                        <td><?= $row['action_time'] ?></td>
                        <td>
                            <form action="proses_histori.php" method="POST" onsubmit="return confirm('Hapus histori ini?')">
                                <input type="hidden" name="history_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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