<?php
session_start();
include '../assets/koneksi.php';

// Cek Login (Opsional, jika publik hapus ini)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

// Ambil nama kategori dari URL
if (!isset($_GET['nama'])) {
    header('Location: ../index.php'); exit;
}
$kategori = $_GET['nama'];
$kategori_safe = htmlspecialchars($kategori);

// --- QUERY 1: AMBIL INFO GENERAL SPESIES ---
$sql_general = "SELECT * FROM species_info WHERE nama_spesies = ?";
$stmt_gen = mysqli_prepare($conn, $sql_general);
mysqli_stmt_bind_param($stmt_gen, "s", $kategori);
mysqli_stmt_execute($stmt_gen);
$result_gen = mysqli_stmt_get_result($stmt_gen);
$general_info = mysqli_fetch_assoc($result_gen);

// --- QUERY 2: AMBIL SEMUA VARIAN ---
$sql_varian = "SELECT * FROM kaiju WHERE kategori = ? AND is_deleted = 0 ORDER BY era ASC, nama ASC";
$stmt_var = mysqli_prepare($conn, $sql_varian);
mysqli_stmt_bind_param($stmt_var, "s", $kategori);
mysqli_stmt_execute($stmt_var);
$result_varian = mysqli_stmt_get_result($stmt_var);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spesies: <?php echo $kategori_safe; ?> - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        /* Samakan style gambar general dengan infobox kaiju.php */
        .general-image-container {
            background-color: #eee; /* Warna background infobox */
            border: 1px solid #ccc; /* Border infobox */
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .general-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 3px solid #ddd; /* Border gambar */
            margin-bottom: 1rem;
        }
        
        /* Style untuk judul deskripsi agar sama dengan kaiju.php */
        .description-title {
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .variant-card-img {
            height: 180px;
            object-fit: cover;
            border-bottom: 3px solid #dc3545;
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
        <h1 class="mb-3"><?php echo $kategori_safe; ?></h1>

        <div class="row">
            
            <div class="col-md-8">
                <h3 class="description-title">Deskripsi Umum</h3>
                <div>
                    <?php 
                    if ($general_info && !empty($general_info['deskripsi_umum'])) {
                        // Tampilkan langsung karena asumsinya sudah format HTML dari editor
                        echo $general_info['deskripsi_umum']; 
                    } else {
                        echo '<p class="text-muted fst-italic">Deskripsi umum untuk spesies ini belum tersedia dalam database.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="general-image-container"> <?php if ($general_info && !empty($general_info['gambar_umum'])): ?>
                        <img src="../img/<?php echo htmlspecialchars($general_info['gambar_umum']); ?>" 
                             alt="<?php echo $kategori_safe; ?> General" 
                             class="general-image">
                        <h5 class="fw-bold"><?php echo $kategori_safe; ?></h5>
                        <hr>
                        <p class="text-muted small">Representasi Umum</p>
                    <?php else: ?>
                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 250px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <h3 class="mb-4">Daftar Inkarnasi / Varian</h3>
        
        <div class="row">
            <?php if (mysqli_num_rows($result_varian) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_varian)): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 hover-card">
                            <img src="../img/<?php echo htmlspecialchars($row['gambar']); ?>" 
                                 class="card-img-top variant-card-img" 
                                 alt="<?php echo htmlspecialchars($row['nama']); ?>">
                            <div class="card-body text-center bg-light">
                                <h6 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($row['nama']); ?></h6>
                                
                                <?php
                                    $era_color = 'bg-secondary';
                                    $era_lower = strtolower($row['era']);
                                    if(strpos($era_lower, 'showa') !== false) $era_color = 'bg-warning text-dark border border-dark';
                                    if(strpos($era_lower, 'heisei') !== false) $era_color = 'bg-primary border border-light';
                                    if(strpos($era_lower, 'reiwa') !== false) $era_color = 'bg-danger border border-light';
                                    if(strpos($era_lower, 'millennium') !== false) $era_color = 'bg-success border border-light';
                                ?>
                                <span class="badge <?php echo $era_color; ?> mb-3 px-3 py-2 rounded-pill"><?php echo htmlspecialchars($row['era']); ?></span>
                                <br>
                                
                                <a href="kaiju.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-dark w-100 stretched-link fw-bold btn-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    Belum ada data varian yang terdaftar untuk kategori ini.
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <a href="../index.php" class="btn btn-secondary">&laquo; Kembali ke Halaman Utama</a>
        </div>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
mysqli_stmt_close($stmt_gen);
mysqli_stmt_close($stmt_var);
mysqli_close($conn);
?>