<?php
header('Content-Type: application/json');
include 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['nomor_daftar']) && isset($data['status']) && isset($data['jenis'])) {
    $nomor_daftar = mysqli_real_escape_string($koneksi, $data['nomor_daftar']);
    $status = mysqli_real_escape_string($koneksi, $data['status']);
    $jenis = $data['jenis'];

    // Tentukan kolom mana yang akan di-update berdasarkan input 'jenis'
    if ($jenis === 'berkas') {
        $query = "UPDATE pendaftar SET status_verifikasi = '$status' WHERE nomor_daftar = '$nomor_daftar'";
        $pesan_sukses = "Status verifikasi berkas berhasil diperbarui!";
    } else {
        $query = "UPDATE pendaftar SET status_kelulusan = '$status' WHERE nomor_daftar = '$nomor_daftar'";
        $pesan_sukses = "Status kelulusan seleksi berhasil diperbarui!";
    }
    
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(["status" => "sukses", "pesan" => $pesan_sukses]);
    } else {
        echo json_encode(["status" => "gagal", "pesan" => mysqli_error($koneksi)]);
    }
}
?> else {
    echo json_encode(["status" => "error", "pesan" => "Data tidak lengkap."]);
}