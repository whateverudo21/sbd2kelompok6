<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Menangkap nomor pendaftaran dari URL parameter (?nomor=...)
$nomor_daftar = $_GET['nomor'] ?? '';

if (empty($nomor_daftar)) {
    echo json_encode(["status" => "gagal", "pesan" => "Nomor pendaftaran tidak boleh kosong!"]);
    exit;
}

$nomor_daftar = mysqli_real_escape_string($koneksi, $nomor_daftar);

// QUERY FIX: Mengambil semua kolom yang dibutuhkan termasuk status_verifikasi dan status_kelulusan
$query = "SELECT nomor_daftar, nama_lengkap, jurusan, jalur, status_verifikasi, status_kelulusan FROM pendaftar WHERE nomor_daftar = '$nomor_daftar'";
$eksekusi = mysqli_query($koneksi, $query);

if (mysqli_num_rows($eksekusi) > 0) {
    $data = mysqli_fetch_assoc($eksekusi);
    echo json_encode([
        "status" => "sukses",
        "data" => [
            "nomor_daftar" => $data['nomor_daftar'],
            "nama_lengkap" => $data['nama_lengkap'],
            "jurusan" => $data['jurusan'],
            "jalur" => $data['jalur'],
            "status_berkas" => $data['status_verifikasi'],  // Kita samakan key-nya dengan JavaScript
            "status_kelulusan" => $data['status_kelulusan'] // Mengirim data kelulusan seleksi
        ]
    ]);
} else {
    echo json_encode([
        "status" => "gagal",
        "pesan" => "Nomor pendaftaran tidak ditemukan! Periksa kembali pengetikan."
    ]);
}
?>