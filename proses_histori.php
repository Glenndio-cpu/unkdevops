<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $history_id = $_POST['history_id'];

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM transaction_history WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $history_id, $user_id);

        if (!$stmt->execute()) {
            echo "Gagal menghapus riwayat: " . $stmt->error;
            exit;
        }

        $stmt->close();
    }

    header("Location: histori.php");
    exit;
}
?>