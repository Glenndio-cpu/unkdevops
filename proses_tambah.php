<?php
session_start();
include "config/db.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Periksa jika form disubmit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $description = $_POST['description'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Validasi dasar
    if ($type !== 'pemasukan' && $type !== 'pengeluaran') {
        die("Jenis transaksi tidak valid.");
    }

    if ($amount <= 0) {
        die("Jumlah harus lebih dari 0.");
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $type, $amount, $description);

    if ($stmt->execute()) {
        header("Location: histori.php?status=berhasil");
        exit;
    } else {
        echo "Gagal menyimpan transaksi: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Jika file ini diakses langsung tanpa POST
    header("Location: tambah.php");
    exit;
}
?>