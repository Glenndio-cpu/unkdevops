<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// Simpan transaksi jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    // Insert transaksi baru ke tabel transactions
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $user_id, $type, $amount, $description, $category);

    if ($stmt->execute()) {
        $transaction_id = $stmt->insert_id;

        $action = 'Added';
        $action_time = date('Y-m-d H:i:s');

        // Insert ke transaction_history
        $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time, description) VALUES (?, ?, ?, ?, ?)");
        $history_stmt->bind_param("iisss", $transaction_id, $user_id, $action, $action_time, $description);

        if (!$history_stmt->execute()) {
            echo "Gagal menyimpan ke transaction_history: " . $history_stmt->error;
        } else {
            $message = "Riwayat berhasil ditambahkan.";
        }

        $message = "Transaksi berhasil ditambahkan!";
    } else {
        $message = "Gagal menyimpan transaksi.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Proses Tambah Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3 class="text-center mb-4">Status Transaksi</h3>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <a href="tambah.php" class="btn btn-primary">Kembali ke Form</a>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

</body>

</html>