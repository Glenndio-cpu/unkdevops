<?php
$host = "localhost";
$user = "root";
$pass = "Gks77777_";
$db = "keuangan";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>