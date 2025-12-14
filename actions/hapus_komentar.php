<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

if (isset($_GET['id']) && isset($_GET['report_id'])) {
    $comment_id = $_GET['id'];
    $report_id = $_GET['report_id'];

    // 1. Hapus Komentar
    $sql_del = "DELETE FROM comments WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_del);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // 2. Jika sukses, tandai Laporan sebagai 'Resolved'
        $sql_upd = "UPDATE reports SET status = 'resolved' WHERE id = ?";
        $stmt_upd = mysqli_prepare($conn, $sql_upd);
        mysqli_stmt_bind_param($stmt_upd, "i", $report_id);
        mysqli_stmt_execute($stmt_upd);

        $_SESSION['message'] = "Komentar dihapus dan laporan ditandai selesai.";
    }
}

header('Location: ../admin/admin_laporan.php');
?>