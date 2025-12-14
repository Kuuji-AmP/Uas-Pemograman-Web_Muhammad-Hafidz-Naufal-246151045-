<?php
session_start();
include '../assets/koneksi.php';

// Cek Era di URL
if (!isset($_GET['era'])) {
    header('Location: ../index.php'); exit;
}
$era = $_GET['era'];

// LOGIKA BARU: Cek apakah Era = "All"
if ($era == 'All') {
    // Jika "All", ambil SEMUA film tanpa filter era
    $sql = "SELECT * FROM films WHERE is_deleted = 0 ORDER BY tahun_rilis DESC"; // Urutkan terbaru
    $stmt = mysqli_prepare($conn, $sql);
    // Tidak perlu bind_param karena tidak ada variabel ? di query ini
} else {
    // Jika Era Spesifik (Showa, Heisei, dll), pakai filter
    $sql = "SELECT * FROM films WHERE era = ? AND is_deleted = 0 ORDER BY tahun_rilis ASC"; // Urutkan terlama (kronologis)
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $era);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Judul Halaman Dinamis
$judul_halaman = ($era == 'All') ? "Semua Film" : "Filmografi: Era " . htmlspecialchars($era);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Film Era <?php echo htmlspecialchars($era); ?> - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
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
        <h2 class="mb-4 border-bottom pb-2">Filmografi: Era <?php echo htmlspecialchars($era); ?></h2>
        
        <div class="row">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($film = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="../img/<?php echo htmlspecialchars($film['poster_url']); ?>" 
                                 class="card-img-top" style="height: 300px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title fs-6 fw-bold"><?php echo htmlspecialchars($film['judul']); ?></h5>
                                <p class="small text-muted"><?php echo htmlspecialchars($film['tahun_rilis']); ?></p>
                                <a href="film.php?id=<?php echo $film['id']; ?>" class="btn btn-sm btn-danger w-100 stretched-link">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data film untuk era ini.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Kaijupedia</h5>
                    <p class="small text-secondary">Ensiklopedia Kaiju terlengkap. Dibuat oleh fans, untuk fans.</p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h5>Navigasi</h5>
                    <ul class="list-unstyled small">
                        <li><a href="../index.php" class="text-secondary text-decoration-none">Home</a></li>
                        <li><a href="../index.php" class="text-secondary text-decoration-none">Daftar Kaiju</a></li>
                        <li><a href="list_films.php?era=All" class="text-secondary text-decoration-none">Filmografi</a></li>
                        <li><a href="profil.php" class="text-secondary text-decoration-none">Profil Saya</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h5>Tentang</h5>
                    <p class="small text-secondary">
                        &copy; <?php echo date('Y'); ?> Kaijupedia.<br>
                        Godzilla is a trademark of Toho Co., Ltd.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-secondary"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-secondary"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-secondary"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>