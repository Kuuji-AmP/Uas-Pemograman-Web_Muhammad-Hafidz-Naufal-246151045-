<?php
// Mulai session di bagian paling atas
session_start();

// Jika pengguna SUDAH login, jangan biarkan dia ke halaman login lagi.
// Arahkan dia ke halaman utama (index.php) atau dashboard (nanti).
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Godzilla Wiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/login.css" rel="stylesheet">
    <style>
        /* Style tambahan untuk form login */
        .login-container {
            max-width: 450px;
            margin: 5rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Kaijupedia</a>
        </div>
    </nav>

    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Login</h2>
            
            <?php
            // Menampilkan pesan error jika ada (dari proses_login.php)
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                // Hapus pesan error setelah ditampilkan
                unset($_SESSION['error_message']);
            }
            ?>

            <form action="../actions/proses_login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="pass" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            
            <hr>
            <p class="text-center">Belum punya akun? 
                <a href="register.php">Daftar di sini</a> 
            </p>
        </div>
    </div>
    

</body>
</html>