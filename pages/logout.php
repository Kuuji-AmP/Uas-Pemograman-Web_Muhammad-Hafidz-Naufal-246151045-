<?php
// 1. Selalu mulai session di paling atas
session_start();

// 2. Hapus semua variabel session
session_unset();

// 3. Hancurkan sesi itu sendiri
session_destroy();

// 4. Arahkan pengguna kembali ke halaman utama (index.php)
//    Anda juga bisa arahkan ke 'login.php' jika mau
header('Location: ../index.php');
exit; // Pastikan skrip berhenti di sini
?>