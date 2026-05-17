<?php
include 'koneksi.php';

// Menangkap data yang dikirim dari Javascript form register
$data = json_decode(file_get_contents("php://input"), true);

// HAPUS isset($data['nama']) dari pengecekan di bawah ini
if(isset($data['email']) && isset($data['password'])) {
    
    // Karena tidak dikirim dari front-end, variabel $nama bisa dihapus 
    // atau diisi string kosong/bawaan email jika kolom di databasemu NOT NULL
    $email = mysqli_real_escape_string($koneksi, $data['email']);
    $password = mysqli_real_escape_string($koneksi, $data['password']);
    
    // Cek dulu apakah email sudah pernah didaftarkan atau belum
    $cek_email = mysqli_query($koneksi, "SELECT * FROM akun_siswa WHERE email='$email'");
    if(mysqli_num_rows($cek_email) > 0) {
        echo json_encode(["status" => "gagal", "pesan" => "Email sudah terdaftar! Gunakan email lain."]);
        exit;
    }
    
    // Hapus kolom 'nama' dan nilainya dari query INSERT di bawah ini
    $query = "INSERT INTO akun_siswa (email, password, role) VALUES ('$email', '$password', 'Siswa')";
    $eksekusi = mysqli_query($koneksi, $query);
    
    if($eksekusi) {
        echo json_encode([
            "status" => "sukses", 
            "pesan" => "Akun berhasil dibuat! Silakan login."
        ]);
    } else {
        echo json_encode([
            "status" => "gagal", 
            "pesan" => "Gagal membuat akun: " . mysqli_error($koneksi)
        ]);
    }
} else {
    echo json_encode(["status" => "error", "pesan" => "Data pendaftaran akun tidak lengkap."]);
}
?>