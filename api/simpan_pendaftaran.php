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

    // 2. Proses Konversi File Fisik ke Base64 (Solusi untuk penyimpanan Read-Only Vercel)
    function uploadFileKeBase64($file_key) {
        if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $file_path = $_FILES[$file_key]['tmp_name'];
        $file_type = $_FILES[$file_key]['type'];
        $file_data = file_get_contents($file_path);
        
        // Menghasilkan string teks panjang data gambar/pdf
        return 'data:' . $file_type . ';base64,' . base64_encode($file_data);
    }

    // Mengubah file fisik menjadi teks teks panjang untuk disimpan langsung di MySQL Clever Cloud
    $file_ijazah = uploadFileKeBase64('fileIjazah');
    $file_kk = uploadFileKeBase64('fileKK');
    $file_akta = uploadFileKeBase64('fileAK');
    $file_pembayaran = uploadFileKeBase64('filePembayaran');

    // Validasi dasar: pastikan semua data wajib terisi
    if (empty($email) || empty($nama) || empty($jalur) || empty($jurusan) || !$file_ijazah || !$file_kk || !$file_akta || !$file_pembayaran) {
        echo json_with_status("gagal", "Mohon lengkapi semua data biodata dan unggah seluruh berkas persyaratan!");
        exit;
    }

    // 3. Simpan ke Database (Kolom file akan diisi string Base64)
    $sql = "INSERT INTO pendaftar (nomor_daftar, email, nama_lengkap, tempat_lahir, tanggal_lahir, no_hp, jalur, jurusan, file_ijazah, file_kk, file_akta, file_pembayaran, status_verifikasi, status_kelulusan) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'MENUNGGU', 'MENUNGGU')";
            
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
