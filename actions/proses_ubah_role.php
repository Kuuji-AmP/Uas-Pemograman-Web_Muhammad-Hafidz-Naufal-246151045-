<?php
session_start();
include '../assets/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $role_baru = $_POST['role_baru'];

    // Update database
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $role_baru, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Role user berhasil diubah menjadi <strong>$role_baru</strong>.";
    } else {
        $_SESSION['message'] = "Gagal mengubah role.";
    }
}

header('Location: ../admin/admin_users.php');
?>