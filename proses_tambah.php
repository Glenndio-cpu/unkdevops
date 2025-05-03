<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $type, $amount, $description, $category);

    if ($stmt->execute()) {
        $transaction_id = $stmt->insert_id;

        $action = 'Added';
        $action_time = date('Y-m-d H:i:s');
        $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
        $history_stmt->bind_param("iiis", $transaction_id, $user_id, $action, $action_time);
        $history_stmt->execute();

        $message = "Transaksi berhasil ditambahkan!";
    } else {
        $message = "Gagal menyimpan transaksi.";
    }

    $stmt->close();
}
?>