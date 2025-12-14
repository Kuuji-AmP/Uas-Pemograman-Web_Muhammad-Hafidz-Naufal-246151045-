<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $isi = htmlspecialchars($_POST['isi_komentar']); // Amankan teks
    $tipe = $_POST['halaman_tipe']; // 'kaiju' atau 'film'
    $halaman_id = $_POST['halaman_id']; // ID dari kaiju/film tersebut

    // Validasi sederhana
    if (!empty($isi)) {
        $sql = "INSERT INTO comments (isi_komentar, user_id, halaman_tipe, halaman_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sisi", $isi, $user_id, $tipe, $halaman_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Berhasil
            $_SESSION['success_comment'] = "Komentar terkirim!";
        } else {
            $_SESSION['error_comment'] = "Gagal mengirim komentar.";
        }
    }
    
    // Redirect kembali ke halaman asal
    if ($tipe == 'kaiju') {
        header("Location: ../pages/kaiju.php?id=$halaman_id");
    } else {
        header("Location: ../pages/film.php?id=$halaman_id");
    }
    exit;
}
?>