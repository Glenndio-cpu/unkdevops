<?php
session_start();
include "config/db.php";

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];

    // Ambil data transaksi berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $transaction_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
    } else {
        echo "Transaksi tidak ditemukan!";
        exit;
    }

    $stmt->close();

    // Proses update atau delete jika ada aksi
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update'])) {
            // Ambil data yang diupdate
            $type = $_POST['type'];
            $amount = $_POST['amount'];
            $description = $_POST['description'];
            $category = $_POST['category'];

            // Update transaksi
            $update_stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, description = ?, category = ? WHERE id = ? AND user_id = ?");
            $update_stmt->bind_param("sdssii", $type, $amount, $description, $category, $transaction_id, $user_id);

            if ($update_stmt->execute()) {
                // Insert ke transaction_history
                $action = 'Updated';
                $action_time = date('Y-m-d H:i:s');
                $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
                $history_stmt->bind_param("iiss", $transaction_id, $user_id, $action, $action_time);
                $history_stmt->execute();

                // Redirect ke dashboard setelah sukses
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error: " . $update_stmt->error;
            }

            $update_stmt->close();
        } elseif (isset($_POST['delete'])) {
            // Hapus transaksi
            $delete_stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
            $delete_stmt->bind_param("ii", $transaction_id, $user_id);

            if ($delete_stmt->execute()) {
                // Insert ke transaction_history
                $action = 'Deleted';
                $action_time = date('Y-m-d H:i:s');
                $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
                $history_stmt->bind_param("iiss", $transaction_id, $user_id, $action, $action_time);
                $history_stmt->execute();

                // Redirect ke dashboard setelah sukses
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error: " . $delete_stmt->error;
            }

            $delete_stmt->close();
        }
    }
} else {
    echo "ID transaksi tidak diberikan!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Update Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-center mb-4">Update Transaksi</h3>

        <div class="card shadow">
            <div class="card-body">
                <form action="update.php?id=<?php echo $transaction['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Transaksi</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="pemasukan" <?php echo ($transaction['type'] == 'pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                            <option value="pengeluaran" <?php echo ($transaction['type'] == 'pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah (Rp)</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                            value="<?php echo $transaction['amount']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select name="category" id="category" class="form-select" required>
                            <option value="Gaji" <?php echo ($transaction['category'] == 'Gaji') ? 'selected' : ''; ?>>
                                Gaji</option>
                            <option value="Hadiah" <?php echo ($transaction['category'] == 'Hadiah') ? 'selected' : ''; ?>>Hadiah</option>
                            <option value="Investasi" <?php echo ($transaction['category'] == 'Investasi') ? 'selected' : ''; ?>>Investasi</option>
                            <option value="Kebutuhan" <?php echo ($transaction['category'] == 'Kebutuhan') ? 'selected' : ''; ?>>Kebutuhan</option>
                            <option value="Lainnya" <?php echo ($transaction['category'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3"
                            required><?php echo $transaction['description']; ?></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" name="update" class="btn btn-primary">Update Transaksi</button>
                        <button type="submit" name="delete" class="btn btn-danger">Hapus Transaksi</button>
                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>

</html>