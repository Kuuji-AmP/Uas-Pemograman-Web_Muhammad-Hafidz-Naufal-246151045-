<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php'); exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Jangan hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        header('Location: ../admin/admin_users.php'); exit;
    }

    // --- SOFT DELETE: Ubah is_deleted jadi 1 ---
    $sql = "UPDATE users SET is_deleted = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "User berhasil dinonaktifkan (Soft Delete).";
    } else {
        $_SESSION['message'] = "Gagal menonaktifkan user.";
    }
}

header('Location: ../admin/admin_users.php');
?>