<?php
// 1. Selalu mulai session
session_start();

// 2. Cek apakah pengguna BELUM login (Penjaga Sesi)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 3. Sertakan koneksi database
include '../assets/koneksi.php';

// 4. Ambil ID pengguna yang sedang login
$user_id = $_SESSION['user_id'];

// 5. Ambil data lengkap pengguna dari database
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Jika karena alasan aneh user tidak ditemukan, logout paksa
if (!$user) {
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/style.css" rel="stylesheet"> <style>
        .profile-container {
            max-width: 600px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Kaijupedia</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="../index.php">Home</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="kaijuDropdown" role="button" data-bs-toggle="dropdown">
                            Kaiju
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Kategori Populer</h6></li>
                            <?php
                            // Ambil 5 kategori teratas untuk menu agar tidak kepanjangan
                            $sql_menu = "SELECT DISTINCT kategori FROM kaiju WHERE is_deleted = 0 ORDER BY kategori ASC LIMIT 5";
                            $res_menu = mysqli_query($conn, $sql_menu);
                            while ($menu = mysqli_fetch_assoc($res_menu)) {
                                echo '<li><a class="dropdown-item" href="../pages/spesies.php?nama='.urlencode($menu['kategori']).'">'.htmlspecialchars($menu['kategori']).'</a></li>';
                            }
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../index.php">Lihat Semua Index</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="filmDropdown" role="button" data-bs-toggle="dropdown">
                            Films
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Berdasarkan Era</h6></li>
                            <?php
                            $sql_era = "SELECT DISTINCT era FROM films WHERE is_deleted = 0 AND era != '' ORDER BY tahun_rilis ASC";
                            $res_era = mysqli_query($conn, $sql_era);
                            if ($res_era && mysqli_num_rows($res_era) > 0) {
                                while ($row_era = mysqli_fetch_assoc($res_era)) {
                                    echo '<li><a class="dropdown-item" href="../pages/list_films.php?era='.urlencode($row_era['era']).'">'.htmlspecialchars($row_era['era']).'</a></li>';
                                }
                            }
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#section-film">Lihat Semua Film</a></li>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex me-3" action="../pages/search.php" method="GET">
                    <div class="input-group input-group-sm">
                        <input class="form-control" type="search" name="keyword" placeholder="Cari..." required>
                        <button class="btn btn-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../pages/profil.php">Profil Saya</a></li>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../admin/admin_panel.php">Panel Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../pages/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="profile-container">
            <h2 class="mb-4">Profil Saya</h2>
            
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled readonly>
            </div>

            <hr class="my-4">

            <h4 class="mb-3">Ganti Password</h4>
            <form action="../actions/proses_ganti_password.php" method="POST">
                <div class="mb-3">
                    <label for="password_lama" class="form-label">Password Lama</label>
                    <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                </div>
                <div class="mb-3">
                    <label for="password_baru" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                </div>
                <div class="mb-3">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan Password</button>
            </form>

        </div>
    </div>
    
    <?php
    // Tutup statement dan koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>