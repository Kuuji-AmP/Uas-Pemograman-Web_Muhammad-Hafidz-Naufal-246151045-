<?php
// 1. Mulai session
session_start();

// 2. Cek Login (Penjaga Sesi)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

// 3. Sertakan koneksi database
include '../assets/koneksi.php';

// 4. Hanya proses jika metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil ID user dari session
    $user_id = $_SESSION['user_id'];

    // Ambil data dari form
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // === VALIDASI 1: Cek Password Baru vs Konfirmasi ===
    if ($password_baru != $konfirmasi_password) {
        $_SESSION['error_message'] = "Password baru dan konfirmasi tidak cocok.";
        header('Location: ../pages/profil.php');
        exit;
    }

    // === VALIDASI 2: Cek Panjang Password (Opsional) ===
    if (strlen($password_baru) < 5) { // Misal minimal 5 karakter
        $_SESSION['error_message'] = "Password baru terlalu pendek (minimal 5 karakter).";
        header('Location: ../pages/profil.php');
        exit;
    }

    // === VALIDASI 3: Cek Password Lama Benar/Salah ===
    
    // Ambil password saat ini dari database
    $sql = "SELECT pass FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Cek apakah password lama yang diketik COCOK dengan di database
    if ($password_lama == $user['pass']) {
        
        // === PASSWORD LAMA BENAR, LAKUKAN UPDATE ===
        
        $sql_update = "UPDATE users SET pass = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        // Bind parameter (s = string untuk password, i = integer untuk id)
        mysqli_stmt_bind_param($stmt_update, "si", $password_baru, $user_id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            // Sukses!
            $_SESSION['success_message'] = "Password berhasil diubah!";
            header('Location: ../pages/profil.php');
            exit;
        } else {
            // Gagal Update
            $_SESSION['error_message'] = "Terjadi kesalahan sistem. Coba lagi nanti.";
            header('Location: ../pages/profil.php');
            exit;
        }

    } else {
        // === PASSWORD LAMA SALAH ===
        $_SESSION['error_message'] = "Password lama yang Anda masukkan salah.";
        header('Location: ../pages/profil.php');
        exit;
    }

} else {
    // Jika file dibuka langsung tanpa submit form
    header('Location: ../pages/profil.php');
    exit;
}

// Tutup koneksi (Good Practice)
mysqli_stmt_close($stmt);
if (isset($stmt_update)) mysqli_stmt_close($stmt_update);
mysqli_close($conn);
?>