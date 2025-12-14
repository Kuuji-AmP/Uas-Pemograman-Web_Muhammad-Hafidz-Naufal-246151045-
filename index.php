<?php
// 1. Selalu mulai session
session_start();

// 2. Cek apakah pengguna BELUM login
if (!isset($_SESSION['user_id'])) {
    header('Location: pages/login.php');
    exit;
}

// 5. Sertakan koneksi database
include 'assets/koneksi.php';

// ==========================================
// LOGIKA PAGINATION (HALAMAN 1, 2, 3...)
// ==========================================

// Tentukan batas jumlah kaiju per halaman (Misal: 10 item = 2 baris x 5 kolom)
$batas = 10; 

// Ambil nomor halaman dari URL (jika tidak ada, default ke halaman 1)
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

// Hitung TOTAL data dulu (untuk tahu berapa jumlah halamannya)
// Kita gunakan COUNT(DISTINCT kategori) karena kita menampilkan per kategori
$query_jumlah = mysqli_query($conn, "SELECT COUNT(DISTINCT kategori) as jumlah FROM kaiju WHERE is_deleted = 0");
$data_jumlah = mysqli_fetch_assoc($query_jumlah);
$total_data = $data_jumlah['jumlah'];

// Hitung total halaman (Total Data / Batas) -> dibulatkan ke atas (ceil)
$total_halaman = ceil($total_data / $batas);

// ==========================================
// QUERY DATA KAIJU (DENGAN LIMIT)
// ==========================================

// Tambahkan "LIMIT ..., ..." di akhir query untuk membatasi tampilan
$sql = "SELECT kategori, MIN(id) as id, MIN(gambar) as gambar, COUNT(*) as total_versi 
        FROM kaiju 
        WHERE is_deleted = 0 
        GROUP BY kategori 
        ORDER BY kategori ASC
        LIMIT $halaman_awal, $batas";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaijuPedia - Ensiklopedia Monster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/mainpage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

    <div class="cusban banner p-5 text-light">
        <div class="row align-items-center">
            <div class="col-sm-2">
                <img src="img/logo3.png" alt="Logo" width="90%" class="img-fluid">
            </div>
            <div class="col-sm-10">
                <h1 class="display-4">Kaijupedia</h1>
                <p class="lead">Sumber informasi lengkap tentang Godzilla dan kaiju lainnya.</p>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">Kaijupedia</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
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
                                echo '<li><a class="dropdown-item" href="pages/spesies.php?nama='.urlencode($menu['kategori']).'">'.htmlspecialchars($menu['kategori']).'</a></li>';
                            }
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php">Lihat Semua Index</a></li>
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
                                    echo '<li><a class="dropdown-item" href="pages/list_films.php?era='.urlencode($row_era['era']).'">'.htmlspecialchars($row_era['era']).'</a></li>';
                                }
                            }
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#section-film">Lihat Semua Film</a></li>
                        </ul>
                    </li>
                </ul>

                <form class="d-flex me-3" action="pages/search.php" method="GET">
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
                            <li><a class="dropdown-item" href="pages/profil.php">Profil Saya</a></li>
                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/admin_panel.php">Panel Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="pages/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h2>Daftar Kaiju</h2>
            <span class="text-muted small">Halaman <?php echo $halaman; ?> dari <?php echo $total_halaman; ?></span>
        </div>
        
        <div class="row row-cols-2 row-cols-md-5 g-3">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($kaiju = mysqli_fetch_assoc($result)) {
            ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 hover-effect">
                        <img src="img/<?php echo htmlspecialchars($kaiju['gambar']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($kaiju['kategori']); ?>"
                             style="height: 160px; object-fit: cover;">
                        
                        <div class="card-body text-center p-2">
                            <h6 class="card-title fw-bold text-truncate" style="font-size: 0.95rem;">
                                <?php echo htmlspecialchars($kaiju['kategori']); ?>
                            </h6>
                            
                            <?php
                            if ($kaiju['total_versi'] > 1) {
                                $link = "pages/spesies.php?nama=" . urlencode($kaiju['kategori']);
                                $text_tombol = "Lihat Varian";
                                $warna_tombol = "btn-dark"; 
                                $info_text = $kaiju['total_versi'] . " Varian";
                            } else {
                                $link = "pages/kaiju.php?id=" . $kaiju['id'];
                                $text_tombol = "Detail";
                                $warna_tombol = "btn-primary"; 
                                $info_text = "Tunggal";
                            }
                            ?>

                            <p class="card-text text-muted mb-2" style="font-size: 0.75rem;">
                                <?php echo $info_text; ?>
                            </p>
                            
                            <a href="<?php echo $link; ?>" class="btn <?php echo $warna_tombol; ?> btn-sm w-100 stretched-link py-1" style="font-size: 0.8rem;">
                                <?php echo $text_tombol; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                } 
            } else {
                echo '<div class="alert alert-warning w-100">Tidak ada data kaiju.</div>';
            }
            ?>
        </div>

        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($halaman <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="<?php if($halaman > 1) echo "?halaman=".($halaman - 1); ?>">Previous</a>
                </li>

                <?php for($x = 1; $x <= $total_halaman; $x++): ?>
                    <li class="page-item <?php if($halaman == $x) echo 'active'; ?>">
                        <a class="page-link" href="?halaman=<?php echo $x; ?>"><?php echo $x; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php if($halaman >= $total_halaman) echo 'disabled'; ?>">
                    <a class="page-link" href="<?php if($halaman < $total_halaman) echo "?halaman=".($halaman + 1); ?>">Next</a>
                </li>
            </ul>
        </nav>

    </div>

    <div class="container mt-5 mb-5" id="section-film">
        <h2 class="mb-4 border-bottom pb-2">Daftar Film</h2>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <?php
            // Tampilkan 8 film terbaru saja di home
            $sql_film = "SELECT * FROM films WHERE is_deleted = 0 ORDER BY tahun_rilis DESC LIMIT 8";
            $result_film = mysqli_query($conn, $sql_film);

            if (mysqli_num_rows($result_film) > 0) {
                while ($film = mysqli_fetch_assoc($result_film)) {
            ?>
                <div class="col"> 
                    <div class="card h-100 shadow-sm border-0">
                        <img src="img/<?php echo htmlspecialchars($film['poster_url']); ?>" 
                             class="card-img-top" 
                             style="height: 300px; object-fit: cover;"
                             alt="<?php echo htmlspecialchars($film['judul']); ?>">
                        <div class="card-body text-center p-3">
                            <h6 class="card-title fw-bold text-truncate"><?php echo htmlspecialchars($film['judul']); ?></h6>
                            <p class="card-text text-muted small mb-2">
                                <?php echo htmlspecialchars($film['tahun_rilis']); ?>
                            </p>
                            <a href="pages/film.php?id=<?php echo $film['id']; ?>" class="btn btn-sm btn-outline-danger w-100 stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo '<p class="text-muted">Belum ada data film.</p>';
            }
            ?>
        </div>
        <div class="text-center mt-3">
            <a href="pages/list_films.php?era=All" class="btn btn-secondary">Lihat Seluruh Koleksi Film &raquo;</a>
        </div>
    </div>

    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Kaijupedia</h5>
                    <p class="small text-secondary">Ensiklopedia monster raksasa terlengkap. Dibuat oleh fans, untuk fans.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Navigasi</h5>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-secondary text-decoration-none">Home</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Daftar Kaiju</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Film</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Tentang</h5>
                    <p class="small text-secondary">&copy; <?php echo date('Y'); ?> Kaijupedia. Godzilla is trademark of Toho Co., Ltd.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>