<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $era = $_POST['era'];
    $tinggi = $_POST['tinggi'];
    $berat = $_POST['berat'];
    $deskripsi = $_POST['sejarah_deskripsi'];
    $kemampuan = $_POST['kemampuan'];
    $kelemahan = $_POST['kelemahan'];
    $gambar_lama = $_POST['gambar_lama'];
    $kategori = $_POST['kategori'];

    // 2. Cek Apakah Ada Gambar Baru Diupload?
    if ($_FILES['gambar']['error'] === 4) {
        // Error 4 artinya: "Tidak ada file yang diupload"
        // Maka, kita pakai gambar lama
        $gambar_final = $gambar_lama;
    } else {
        // Ada file baru! Proses upload.
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        
        // Buat nama unik
        $gambar_final = time() . '_' . $nama_file;
        $path_upload = "../img/" . $gambar_final;

        // Upload file baru
        move_uploaded_file($tmp_file, $path_upload);

        // (Opsional) Hapus gambar lama dari folder agar hemat penyimpanan
        // if (file_exists("img/" . $gambar_lama)) { unlink("img/" . $gambar_lama); }
    }

    // 3. Update Database
    $sql = "UPDATE kaiju SET 
            nama=?, kategori=?, era=?, tinggi=?, berat=?, gambar=?, 
            sejarah_deskripsi=?, kemampuan=?, kelemahan=? 
            WHERE id=?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssi", $nama, $kategori, $era, $tinggi, $berat, $gambar_final, $deskripsi, $kemampuan, $kelemahan, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Data <strong>$nama</strong> berhasil diupdate!";
        header('Location: ../admin/admin_kaiju.php');
    } else {
        $_SESSION['message'] = "Gagal mengupdate data.";
        header('Location: ../admin/edit_kaiju.php?id=' . $id);
    }

} else {
    header('Location: ../admin/admin_kaiju.php');
}
?>