<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_spesies = $_POST['nama_spesies'];
    $deskripsi = $_POST['deskripsi_umum'];
    $gambar_lama = $_POST['gambar_lama'];
    
    // 1. Cek Upload Gambar
    $gambar_final = $gambar_lama;
    
    if (isset($_FILES['gambar_umum']) && $_FILES['gambar_umum']['error'] !== 4) {
        $gambar = $_FILES['gambar_umum']['name'];
        $tmp = $_FILES['gambar_umum']['tmp_name'];
        $baru = time() . '_GEN_' . $gambar;
        
        if (move_uploaded_file($tmp, "../img/" . $baru)) {
            $gambar_final = $baru;
            // Hapus gambar lama jika ada dan diganti
            if (!empty($gambar_lama) && file_exists("../img/" . $gambar_lama)) {
                unlink("../img/" . $gambar_lama);
            }
        }
    }

    // 2. CEK APAKAH DATA SUDAH ADA?
    $check = mysqli_query($conn, "SELECT id FROM species_info WHERE nama_spesies = '$nama_spesies'");
    
    if (mysqli_num_rows($check) > 0) {
        // --- JIKA ADA: UPDATE ---
        $sql = "UPDATE species_info SET deskripsi_umum=?, gambar_umum=? WHERE nama_spesies=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $deskripsi, $gambar_final, $nama_spesies);
    } else {
        // --- JIKA BELUM ADA: INSERT ---
        $sql = "INSERT INTO species_info (deskripsi_umum, gambar_umum, nama_spesies) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $deskripsi, $gambar_final, $nama_spesies);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Info spesies <strong>$nama_spesies</strong> berhasil disimpan.";
    } else {
        $_SESSION['message'] = "Gagal menyimpan info.";
    }
}

header('Location: ../admin/admin_spesies.php');
?>