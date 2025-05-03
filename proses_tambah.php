<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'] ?? '';
    $category_id = intval($_POST['category_id']);
    $user_id = $_SESSION['user_id'];

    if ($type !== 'pemasukan' && $type !== 'pengeluaran') {
        die("Jenis transaksi tidak valid.");
    }

    if ($amount <= 0) {
        die("Jumlah harus lebih dari 0.");
    }

    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsi", $user_id, $type, $amount, $description, $category_id);

    if ($stmt->execute()) {
        header("Location: histori.php?status=berhasil");
        exit;
    } else {
        echo "Gagal menyimpan transaksi: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: tambah.php");
    exit;
}
?>