<?php
session_start();
include "config/db.php";

// Cek login
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

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = $_POST['update_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Update transaksi
    $stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, description = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssdii", $type, $amount, $description, $id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Catat histori
    $log = $conn->prepare("INSERT INTO history (transaction_id, user_id, action, action_date) VALUES (?, ?, 'Updated', NOW())");
    $log->bind_param("ii", $id, $user_id);
    $log->execute();
    $log->close();

    header("Location: update.php?updated=1");
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Cek apakah transaksi milik user
    $cek = $conn->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
    $cek->bind_param("ii", $id, $user_id);
    $cek->execute();
    $result = $cek->get_result();
    if ($result->num_rows > 0) {
        // Hapus transaksi
        $del = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        $del->bind_param("ii", $id, $user_id);
        $del->execute();
        $del->close();

        // Catat histori
        $log = $conn->prepare("INSERT INTO history (transaction_id, user_id, action, action_date) VALUES (?, ?, 'Deleted', NOW())");
        $log->bind_param("ii", $id, $user_id);
        $log->execute();
        $log->close();

        header("Location: update.php?deleted=1");
        exit;
    }
}

// Ambil semua transaksi user
$stmt = $conn->prepare("SELECT id, type, amount, description FROM transactions WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$transactions = $stmt->get_result();
$stmt->close();

// Untuk mode edit
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT id, type, amount, description FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $edit_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Update & Delete Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3 class="mb-4">Manajemen Transaksi - Halo, <strong><?= htmlspecialchars($username) ?></strong></h3>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Transaksi berhasil diperbarui!</div>
        <?php elseif (isset($_GET['deleted'])): ?>
            <div class="alert alert-warning">Transaksi berhasil dihapus!</div>
        <?php endif; ?>

        <?php if ($edit_data): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Edit Transaksi</h5>
                    <form method="POST" action="update.php">
                        <input type="hidden" name="update_id" value="<?= $edit_data['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select name="type" class="form-select" required>
                                <option value="pemasukan" <?= $edit_data['type'] === 'pemasukan' ? 'selected' : '' ?>>Pemasukan
                                </option>
                                <option value="pengeluaran" <?= $edit_data['type'] === 'pengeluaran' ? 'selected' : '' ?>>
                                    Pengeluaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="amount" class="form-control" value="<?= $edit_data['amount'] ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="description" class="form-control"
                                value="<?= htmlspecialchars($edit_data['description']) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="update.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <h5>Daftar Transaksi</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td>Rp <?= number_format($row['amount'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <a href="update.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="update.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
    </div>
</body>

</html>