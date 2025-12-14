<?php
// 1. Mulai Session & Cek Login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 2. Sertakan Koneksi
include '../assets/koneksi.php';

// 3. Cek ID di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}
$id = $_GET['id'];

// 4. AMBIL DATA FILM
$sql_film = "SELECT * FROM films WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql_film);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result_film = mysqli_stmt_get_result($stmt);
$film = mysqli_fetch_assoc($result_film);

// Jika film tidak ditemukan
if (!$film) {
    echo "Film tidak ditemukan.";
    exit;
}

// 5. AMBIL DATA KAIJU YANG MUNCUL (JOIN TABLE)
// Kita ambil nama, id, dan gambar kaiju yang terhubung lewat tabel 'film_kaiju'
$sql_kaiju = "SELECT kaiju.id, kaiju.nama, kaiju.gambar 
              FROM kaiju 
              JOIN film_kaiju ON kaiju.id = film_kaiju.kaiju_id 
              WHERE film_kaiju.film_id = ?";

$stmt_kaiju = mysqli_prepare($conn, $sql_kaiju);
mysqli_stmt_bind_param($stmt_kaiju, "i", $id);
mysqli_stmt_execute($stmt_kaiju);
$result_kaiju = mysqli_stmt_get_result($stmt_kaiju);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($film['judul']); ?> - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .film-poster {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .kaiju-thumb {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
    </style>
</head>
<body>

        <div class="cusban banner p-5 text-light">

        <div class="row align-items-center">
            <div class="col-sm-2">
                <img src="../img/logo3.png" alt="Logo" width="90%" class="img-fluid">
            </div>
            
            <div class="col-sm-10">
                <h1 class="display-4">Kaijupedia</h1>
                <p class="lead">Sumber informasi lengkap tentang Godzilla dan kaiju lainnya.</p>
            </div>
        </div>

    </div>

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

    <div class="container mb-5">
        <h1 class="mb-1"><?php echo htmlspecialchars($film['judul']); ?></h1>
        <p class="text-muted fs-5 mb-4">Dirilis tahun <?php echo htmlspecialchars($film['tahun_rilis']); ?></p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <img src="../img/<?php echo htmlspecialchars($film['poster_url']); ?>" 
                     alt="<?php echo htmlspecialchars($film['judul']); ?>" 
                     class="film-poster mb-3">
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Info Film</h5>
                        <hr>
                        <p><strong>Sutradara:</strong><br> <?php echo htmlspecialchars($film['sutradara']); ?></p>
                        </div>
                </div>
            </div>

            <div class="col-md-8">
                
                <div class="mb-5">
                    <h3>Sinopsis</h3>
                    <p class="lead fs-6">
                        <?php echo $film['sinopsis']; ?>
                    </p>
                </div>

                <hr>

                <h3 class="mb-3">Monster di Film Ini</h3>
                
                <div class="row">
                    <?php if (mysqli_num_rows($result_kaiju) > 0): ?>
                        <?php while ($k = mysqli_fetch_assoc($result_kaiju)): ?>
                            
                            <div class="col-6 col-md-4 col-lg-3 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <a href="kaiju.php?id=<?php echo $k['id']; ?>">
                                        <img src="../img/<?php echo htmlspecialchars($k['gambar']); ?>" 
                                             class="kaiju-thumb" 
                                             alt="<?php echo htmlspecialchars($k['nama']); ?>">
                                    </a>
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title mb-0">
                                            <a href="kaiju.php?id=<?php echo $k['id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($k['nama']); ?>
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            Belum ada data Kaiju yang terhubung ke film ini.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        
        <div class="mt-4">
            <a href="../index.php" class="btn btn-secondary">&laquo; Kembali ke Halaman Utama</a>
        </div>

        <hr class="my-5">

        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4">Komentar Diskusi</h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <form action="../actions/proses_komentar.php" method="POST">
                                <input type="hidden" name="halaman_tipe" value="film">
                                <input type="hidden" name="halaman_id" value="<?php echo $film['id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tulis Komentar</label>
                                    <textarea name="isi_komentar" class="form-control" rows="3" placeholder="Apa pendapatmu tentang monster ini?" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Kirim Komentar</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Silakan <a href="login.php">Login</a> untuk menulis komentar.
                    </div>
                <?php endif; ?>

                <?php
                // Query ambil komentar + nama user pengirim
                $sql_komen = "SELECT c.*, u.username, u.role 
                              FROM comments c 
                              JOIN users u ON c.user_id = u.id 
                              WHERE c.halaman_tipe = 'film' AND c.halaman_id = ? 
                              ORDER BY c.tanggal_posting DESC";
                
                $stmt_k = mysqli_prepare($conn, $sql_komen);
                mysqli_stmt_bind_param($stmt_k, "i", $id); 
                mysqli_stmt_execute($stmt_k);
                $res_komen = mysqli_stmt_get_result($stmt_k);

                if (mysqli_num_rows($res_komen) > 0) {
                    while ($k = mysqli_fetch_assoc($res_komen)) {
                        ?>
                        <div class="card mb-3 border-0 bg-light">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="card-title fw-bold mb-1">
                                        <i class="bi bi-person-circle text-secondary"></i> 
                                        <?php echo htmlspecialchars($k['username']); ?>
                                        
                                        <?php if($k['role'] == 'admin') echo '<span class="badge bg-primary ms-1" style="font-size:0.6em">Admin</span>'; ?>
                                        <?php if($k['role'] == 'manager') echo '<span class="badge bg-success ms-1" style="font-size:0.6em">Manager</span>'; ?>
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.8em;">
                                        <?php echo date('d M Y, H:i', strtotime($k['tanggal_posting'])); ?>
                                    </small>
                                </div>
                                <p class="card-text mt-2"><?php echo nl2br(htmlspecialchars($k['isi_komentar'])); ?></p>

                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $k['user_id']): ?>
                                    <div class="text-end">
                                        <a href="lapor.php?tipe=comment&id=<?php echo $k['id']; ?>" class="text-danger text-decoration-none small" title="Laporkan komentar ini">
                                            <i class="bi bi-flag"></i> Lapor
                                        </a>
                                    </div>
                                <?php endif; ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p class="text-muted">Belum ada komentar. Jadilah yang pertama!</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>🦎 Kaijupedia</h5>
                    <p class="small text-secondary">Ensiklopedia monster raksasa terlengkap. Dibuat oleh fans, untuk fans.</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
<?php
mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_kaiju);
mysqli_close($conn);
?>