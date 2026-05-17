<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ppdb"; // <-- GANTI DENGAN NAMA DATABASE KAMU DI PHPMYADMIN

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    // Jangan biarkan PHP memuntahkan HTML jika gagal, kirim respon JSON agar JS tidak crash
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "pesan" => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit;
}
?>