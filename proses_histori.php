<?php
session_start();
include "config/db.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action']; // action: 'update', 'delete'
    $transaction_id = $_POST['transaction_id'];

    // Handle Update
    if ($action == 'update') {
        $type = $_POST['type'];
        $amount = $_POST['amount'];
        $description = $_POST['description'];
        $category = $_POST['category'];

        // Update transaksi
        $stmt = $conn->prepare("UPDATE transactions SET type = ?, amount = ?, description = ?, category = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sdssii", $type, $amount, $description, $category, $transaction_id, $user_id);
        if ($stmt->execute()) {
            // Insert ke transaction_history
            $action = 'Updated';
            $action_time = date('Y-m-d H:i:s');
            $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
            $history_stmt->bind_param("iiss", $transaction_id, $user_id, $action, $action_time);
            $history_stmt->execute();
            $history_stmt->close();
        }
        $stmt->close();
    }

    // Handle Delete
    elseif ($action == 'delete') {
        // Delete transaksi
        $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $transaction_id, $user_id);
        if ($stmt->execute()) {
            // Insert ke transaction_history
            $action = 'Deleted';
            $action_time = date('Y-m-d H:i:s');
            $history_stmt = $conn->prepare("INSERT INTO transaction_history (transaction_id, user_id, action, action_time) VALUES (?, ?, ?, ?)");
            $history_stmt->bind_param("iiss", $transaction_id, $user_id, $action, $action_time);
            $history_stmt->execute();
            $history_stmt->close();
        }
        $stmt->close();
    }

    header("Location: histori.php");
    exit;
}
?>