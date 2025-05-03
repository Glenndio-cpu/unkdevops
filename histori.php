<?php
session_start();
include "config/db.php";

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query untuk mengambil riwayat transaksi pengguna
$query = "
    SELECT t.id, t.type, t.amount, t.category, t.description, t.created_at 
    FROM transactions t
    WHERE t.user_id = ? 
    ORDER BY t.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Histori Transaksi - Keuangan Harian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Histori Transaksi</h3>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah (Rp)</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= ucfirst($row['type']) ?></td>
                        <td>Rp <?= number_format($row['amount'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

</body>

</html>

<?php
$stmt->close();
?>