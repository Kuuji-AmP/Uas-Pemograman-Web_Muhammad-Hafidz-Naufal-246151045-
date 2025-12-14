<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php'); exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- RESTORE: Kembalikan is_deleted jadi 0 ---
    $sql = "UPDATE users SET is_deleted = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "User berhasil diaktifkan kembali (Restore).";
    }
}

header('Location: ../admin/admin_users.php');
?>