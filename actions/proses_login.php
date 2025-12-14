<?php
// 1. Mulai session
session_start();

// 2. Sertakan koneksi database
include '../assets/koneksi.php'; // Pastikan path ini benar

// 3. Cek apakah data dikirim via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 4. Ambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 5. Siapkan query untuk mencari user
    $sql = "SELECT id, username, pass, role FROM users WHERE username = ? AND is_deleted = 0";
    $stmt = mysqli_prepare($conn, $sql);
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // 6. Cek apakah user ditemukan
    if (mysqli_num_rows($result) == 1) {
        
        // 7. Ambil data user
        $user = mysqli_fetch_assoc($result);

        // 8. VERIFIKASI PASSWORD (Perbandingan teks biasa)
        if ($password == $user['pass']) {
            
            // === LOGIN BERHASIL ===
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header('Location: ../index.php');
            exit;

        } else {
            // === LOGIN GAGAL (Password Salah) ===
            $_SESSION['error_message'] = "Password yang Anda masukkan salah.";
            header('Location: ../pages/login.php');
            exit;
        }

    } else {
        // === LOGIN GAGAL (Username tidak ditemukan) ===
        $_SESSION['error_message'] = "Username tidak ditemukan.";
        header('Location: ../pages/login.php');
        exit;
    }

} else {
    header('Location: ../pages/login.php');
    exit;
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>