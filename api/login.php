<?php
header('Content-Type: application/json');
error_reporting(0); 
ini_set('display_errors', 0);

include 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['email']) && isset($data['password'])) {
    
    $email = mysqli_real_escape_string($koneksi, $data['email']);
    $password = mysqli_real_escape_string($koneksi, $data['password']); 
    
    $query = "SELECT * FROM akun_siswa WHERE email='$email' AND password='$password'";
    $eksekusi = mysqli_query($koneksi, $query);
    
    if($eksekusi && mysqli_num_rows($eksekusi) > 0) {
        $user = mysqli_fetch_assoc($eksekusi);
        $role = isset($user['role']) ? $user['role'] : 'Siswa'; 

        $sudah_daftar = false;
        $data_daftar = null;
        
        if($role === 'Siswa') {
            // Mengambil nomor_daftar, nama, jalur, jurusan, dan status_verifikasi
           $query_cek_daftar = "SELECT nomor_daftar, nama_lengkap, jalur, jurusan, status_verifikasi, status_kelulusan FROM pendaftar WHERE email='$email'";
            $eksekusi_cek = mysqli_query($koneksi, $query_cek_daftar);
            
            if($eksekusi_cek && mysqli_num_rows($eksekusi_cek) > 0) {
                $sudah_daftar = true;
                $row_daftar = mysqli_fetch_assoc($eksekusi_cek);
                
                // Paksa status_verifikasi menjadi HURUF BESAR semua agar mudah dicek di Javascript
                $status_bersih = strtoupper(trim($row_daftar['status_verifikasi']));
                
                $data_daftar = [
                    "nomor_daftar" => $row_daftar['nomor_daftar'],
                    "nama_lengkap" => $row_daftar['nama_lengkap'],
                    "jalur" => $row_daftar['jalur'],
                    "jurusan" => $row_daftar['jurusan'],
                    "status_verifikasi" => $status_bersih
                ];
            }
        }
        
        echo json_encode([
            "status" => "sukses", 
            "pesan" => "Login berhasil!",
            "email" => $email,
            "role" => $role,
            "sudah_daftar" => $sudah_daftar,
            "data_daftar" => $data_daftar
        ]);
        
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Email atau Password salah!"]);
    }
    
} else {
    echo json_encode(["status" => "gagal", "pesan" => "Data login tidak lengkap!"]);
}
?>