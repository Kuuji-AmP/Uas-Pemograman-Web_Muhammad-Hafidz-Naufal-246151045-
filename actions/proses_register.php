<?php
// 1. Mulai session
session_start();

// 2. Sertakan koneksi (menggunakan path yang konsisten)
include '../assets/koneksi.php';

// 3. Hanya proses jika metodenya POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 4. Ambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // <-- Password mentah
    $confirm_password = $_POST['confirm_password'];

    // === VALIDASI INPUT ===

    // 4a. Cek apakah password dan konfirmasi sama
    if ($password != $confirm_password) {
        $_SESSION['error_message'] = "Password dan Konfirmasi Password tidak cocok.";
        header('Location: ../pages/register.php');
        exit;
    }

    // 4b. Cek kekuatan password (misal, minimal 8 karakter)
    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password harus memiliki minimal 8 karakter.";
        header('Location: ../pages/register.php');
        exit;
    }

    // 4c. Cek apakah username SUDAH ADA
    $sql_check_user = "SELECT id FROM users WHERE username = ?";
    $stmt_check_user = mysqli_prepare($conn, $sql_check_user);
    mysqli_stmt_bind_param($stmt_check_user, "s", $username);
    mysqli_stmt_execute($stmt_check_user);
    mysqli_stmt_store_result($stmt_check_user); 

    if (mysqli_stmt_num_rows($stmt_check_user) > 0) {
        $_SESSION['error_message'] = "Username ini sudah digunakan. Silakan pilih yang lain.";
        header('Location: ../pages/register.php');
        exit;
    }
    mysqli_stmt_close($stmt_check_user);

    // 4d. Cek apakah email SUDAH ADA
    $sql_check_email = "SELECT id FROM users WHERE email = ?";
    $stmt_check_email = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_store_result($stmt_check_email);

    if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
        $_SESSION['error_message'] = "Email ini sudah terdaftar. Silakan gunakan email lain.";
        header('Location: ../pages/register.php');
        exit;
    }
    mysqli_stmt_close($stmt_check_email);

    // 6. Masukkan user baru ke database (menggunakan kolom 'pass')
    $sql_insert = "INSERT INTO users (username, email, pass) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    
    // "sss" berarti tiga-tiganya string
    mysqli_stmt_bind_param($stmt_insert, "sss", $username, $email, $password);

    // 7. Eksekusi query
    if (mysqli_stmt_execute($stmt_insert)) {
        
        // === REGISTRASI BERHASIL ===
        // Langsung loginkan pengguna
        $_SESSION['user_id'] = mysqli_insert_id($conn); 
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'member'; // Otomatis 'member'

        header('Location: ../index.php');
        exit;

    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        header('Location: ../pages/register.php');
        exit;
    }

} else {
    // Jika file diakses langsung, tendang ke form register
    header('Location: ../pages/register.php');
    exit;
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt_insert);
mysqli_close($conn);
?>