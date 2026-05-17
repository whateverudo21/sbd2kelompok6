<?php
header('Content-Type: application/json');
include 'koneksi.php';

// CONTOH KODE DI FILE PHP PENYEDIA DATA SISWA:
$query = "SELECT nomor_daftar, nama_lengkap, jalur, jurusan, status_verifikasi, status_kelulusan FROM pendaftar";
$eksekusi = mysqli_query($koneksi, $query);

$pendaftar = [];
while ($row = mysqli_fetch_assoc($eksekusi)) {
    $pendaftar[] = $row;
}
echo json_encode(["status" => "sukses", "data" => $pendaftar]);
?>