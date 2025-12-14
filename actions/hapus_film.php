<?php
session_start();
include '../assets/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- SOFT DELETE: Ubah is_deleted jadi 1 ---
    $sql = "UPDATE films SET is_deleted = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Film berhasil dihapus (disembunyikan).";
    } else {
        $_SESSION['message'] = "Gagal menghapus film.";
    }
}

header('Location: ../admin/admin_films.php');
?>