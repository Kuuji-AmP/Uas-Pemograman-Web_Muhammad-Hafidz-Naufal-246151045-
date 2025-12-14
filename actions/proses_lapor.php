<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['target_type'];
    $id = $_POST['target_id'];
    $alasan = htmlspecialchars($_POST['alasan']);

    $sql = "INSERT INTO reports (reporter_user_id, target_type, target_id, alasan, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isis", $user_id, $type, $id, $alasan);
    
    if (mysqli_stmt_execute($stmt)) {
        // Script JS untuk alert dan kembali 2 halaman (ke halaman asal)
        echo "<script>
                alert('Terima kasih! Laporan Anda telah dikirim ke admin.');
                window.history.go(-2); 
              </script>";
    } else {
        echo "Gagal mengirim laporan.";
    }
}
?>