<?php
session_start();
include '../assets/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Ambil data teks dari form
    $nama = $_POST['nama'];
    $era = $_POST['era'];
    $tinggi = $_POST['tinggi'];
    $berat = $_POST['berat'];
    $deskripsi = $_POST['sejarah_deskripsi'];
    $kemampuan = $_POST['kemampuan'];
    $kelemahan = $_POST['kelemahan'];
    $kategori = $_POST['kategori'];

    // 2. PROSES UPLOAD GAMBAR
    $gambar = $_FILES['gambar']['name'];
    $tmp_gambar = $_FILES['gambar']['tmp_name'];
    
    // Ganti nama file agar unik (misal: 173829_godzilla.jpg)
    $nama_baru = time() . '_' . $gambar;
    $path_upload = "../img/" . $nama_baru;

    // Cek apakah folder img ada, jika tidak user harus buat manual
    // Pindahkan file dari folder sementara ke folder img/
    if (move_uploaded_file($tmp_gambar, $path_upload)) {
        
        // 3. JIKA UPLOAD SUKSES, SIMPAN KE DATABASE
        $sql = "INSERT INTO kaiju (nama, kategori, era, tinggi, berat, gambar, sejarah_deskripsi, kemampuan, kelemahan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        // Cek jika prepare gagal
        if (!$stmt) {
            die("Error Prepare Statement: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "sssssssss", $nama, $kategori, $era, $tinggi, $berat, $nama_baru, $deskripsi, $kemampuan, $kelemahan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Berhasil menambahkan <strong>$nama</strong>!";
            header('Location: ../admin/admin_kaiju.php');
        } else {
            // --- MODIFIKASI UNTUK CEK ERROR ---
            // Jangan redirect dulu, tampilkan error di layar
            die("Gagal menyimpan ke database. Error: " . mysqli_stmt_error($stmt));
        }

    } else {
        // Error Upload Gambar
        die("Gagal mengupload gambar. Error code: " . $_FILES['gambar']['error'] . ". Pastikan folder 'kaijupedia/img/' ada dan bisa ditulis.");
    }

} else {
    header('Location: ../admin/admin_kaiju.php');
}
?>