<?php
header('Content-Type: application/json');
include 'koneksi.php'; // Menyertakan koneksi database kamu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil data teks dari form termasuk email user yang aktif
    $email = $_POST['emailActive'] ?? '';
    $nama = $_POST['namaLengkap'] ?? '';
    $tempat_lahir = $_POST['tempatLahir'] ?? '';
    $tanggal_lahir = $_POST['tanggalLahir'] ?? '';
    $no_hp = $_POST['noHP'] ?? '';
    $jalur = $_POST['jalurDaftar'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';
    
    // Generate Nomor Pendaftaran otomatis secara backend untuk keamanan
    $random_id = "PPDB2026-" . rand(1000, 9000);

    // 2. Proses Upload Berkas
    $target_dir = "uploads/";
    
    // Fungsi pembantu untuk memproses upload file
    function uploadFile($file_key, $target_dir, $prefix) {
        if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
        $new_filename = $prefix . "_" . time() . "_" . rand(100, 999) . "." . $ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
            return $new_filename;
        }
        return null;
    }

    $file_ijazah = uploadFile('fileIjazah', $target_dir, 'ijazah');
    $file_kk = uploadFile('fileKK', $target_dir, 'kk');
    $file_akta = uploadFile('fileAK', $target_dir, 'akta');
    $file_pembayaran = uploadFile('filePembayaran', $target_dir, 'bayar');

    // Validasi dasar: pastikan semua data wajib terisi
    if (empty($email) || empty($nama) || empty($jalur) || empty($jurusan) || !$file_ijazah || !$file_kk || !$file_akta || !$file_pembayaran) {
        echo json_with_status("gagal", "Mohon lengkapi semua data biodata dan unggah seluruh berkas persyaratan!");
        exit;
    }

    // 3. Simpan ke Database (Menambahkan kolom 'email')
    $sql = "INSERT INTO pendaftar (nomor_daftar, email, nama_lengkap, tempat_lahir, tanggal_lahir, no_hp, jalur, jurusan, file_ijazah, file_kk, file_akta, file_pembayaran, status_verifikasi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Seleksi / Verifikasi')";
            
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt) {
        // Terdapat 12 parameter tanda tanya (?)
        $stmt->bind_param("ssssssssssss", $random_id, $email, $nama, $tempat_lahir, $tanggal_lahir, $no_hp, $jalur, $jurusan, $file_ijazah, $file_kk, $file_akta, $file_pembayaran);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "sukses",
                "pesan" => "Data pendaftaran berhasil disimpan!",
                "data" => [
                    "id" => $random_id,
                    "nama" => $nama,
                    "jalur" => $jalur,
                    "jurusan" => $jurusan
                ]
            ]);
        } else {
            echo json_with_status("gagal", "Gagal menyimpan data ke database: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo json_with_status("gagal", "Terjadi kesalahan sistem pada persiapan database.");
    }
} else {
    echo json_with_status("gagal", "Metode request tidak valid.");
}

function json_with_status($status, $pesan) {
    return json_encode(["status" => $status, "pesan" => $pesan]);
}
?>