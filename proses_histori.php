<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'delete') {
        $stmt = $conn->prepare("DELETE FROM transaction_history WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: histori.php");
        exit;
    } elseif ($action == 'edit') {
        // Tampilkan form edit
        $stmt = $conn->prepare("SELECT * FROM transaction_history WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Edit Riwayat</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body class="bg-light">
            <div class="container mt-5">
                <h3 class="text-center mb-4">Edit Riwayat Transaksi</h3>
                <form action="proses_histori.php?action=update" method="post">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <div class="mb-3">
                        <label for="action" class="form-label">Aksi</label>
                        <select class="form-select" name="action" required>
                            <option value="Added" <?= $data['action'] == 'Added' ? 'selected' : '' ?>>Added</option>
                            <option value="Updated" <?= $data['action'] == 'Updated' ? 'selected' : '' ?>>Updated</option>
                            <option value="Deleted" <?= $data['action'] == 'Deleted' ? 'selected' : '' ?>>Deleted</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="histori.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </body>

        </html>
        <?php
        exit;
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    $stmt = $conn->prepare("UPDATE transaction_history SET action = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $action, $id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: histori.php");
    exit;
}
?>