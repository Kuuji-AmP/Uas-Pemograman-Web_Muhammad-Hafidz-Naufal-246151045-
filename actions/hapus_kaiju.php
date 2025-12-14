<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Keamanan
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

// 2. Pastikan ada ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // --- LOGIKA BARU (SOFT DELETE) ---
    // Kita tidak menghapus data, tapi mengubah status 'is_deleted' menjadi 1
    
    $sql = "UPDATE kaiju SET is_deleted = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Data Kaiju berhasil dihapus (disembunyikan).";
    } else {
        $_SESSION['message'] = "Gagal menghapus data.";
    }

    mysqli_stmt_close($stmt);
}

// 3. Kembali ke admin
header('Location: ../admin/admin_kaiju.php');
exit;
?>