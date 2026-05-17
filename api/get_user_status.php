<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

// Pastikan user sudah login, sesuaikan key session dengan sistem loginmu
// Misal saat login kamu menyimpan $_SESSION['nomor_daftar']
if (!isset($_SESSION['nomor_daftar'])) {
    echo json_encode(["status" => "gagal", "pesan" => "Sesi habis, silakan login kembali."]);
    exit;
}

$nomor_daftar = $_SESSION['nomor_daftar'];

$query = "SELECT nomor_daftar, nama_lengkap, jalur, jurusan, status_verifikasi FROM pendaftar WHERE nomor_daftar = '$nomor_daftar'";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode([
        "status" => "sukses",
        "status_verifikasi" => $data['status_verifikasi']
    ]);
} else {
    echo json_encode(["status" => "gagal", "pesan" => "Data tidak ditemukan."]);
}
?>