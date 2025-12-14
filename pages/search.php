<?php
session_start();
include '../assets/koneksi.php';

// Ambil keyword
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$keyword_safe = htmlspecialchars($keyword);

// Jika kosong, kembalikan
if (empty($keyword)) {
    header('Location: ../index.php'); exit;
}

// 1. CARI DI TABEL KAIJU
$sql_k = "SELECT * FROM kaiju WHERE (nama LIKE ? OR sejarah_deskripsi LIKE ?) AND is_deleted = 0";
$stmt_k = mysqli_prepare($conn, $sql_k);
$param = "%$keyword%";
mysqli_stmt_bind_param($stmt_k, "ss", $param, $param);
mysqli_stmt_execute($stmt_k);
$res_kaiju = mysqli_stmt_get_result($stmt_k);

// 2. CARI DI TABEL FILMS
$sql_f = "SELECT * FROM films WHERE (judul LIKE ? OR sinopsis LIKE ?) AND is_deleted = 0";
$stmt_f = mysqli_prepare($conn, $sql_f);
mysqli_stmt_bind_param($stmt_f, "ss", $param, $param);
mysqli_stmt_execute($stmt_f);
$res_film = mysqli_stmt_get_result($stmt_f);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian: <?php echo $keyword_safe; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        <h2 class="mb-4">Hasil Pencarian untuk "<em><?php echo $keyword_safe; ?></em>"</h2>

        <h4 class="border-bottom pb-2 mb-3"><i class="bi bi-hdd-stack text-success"></i> Kaiju (<?php echo mysqli_num_rows($res_kaiju); ?>)</h4>
        <div class="row mb-5">
            <?php if (mysqli_num_rows($res_kaiju) > 0): ?>
                <?php while ($k = mysqli_fetch_assoc($res_kaiju)): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm">
                            <img src="../img/<?php echo htmlspecialchars($k['gambar']); ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?php echo htmlspecialchars($k['nama']); ?></h6>
                                <a href="kaiju.php?id=<?php echo $k['id']; ?>" class="btn btn-sm btn-primary w-100 stretched-link">Lihat</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><p class="text-muted">Tidak ditemukan kaiju yang cocok.</p></div>
            <?php endif; ?>
        </div>

        <h4 class="border-bottom pb-2 mb-3"><i class="bi bi-film text-danger"></i> Film (<?php echo mysqli_num_rows($res_film); ?>)</h4>
        <div class="row mb-5">
            <?php if (mysqli_num_rows($res_film) > 0): ?>
                <?php while ($f = mysqli_fetch_assoc($res_film)): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card h-100 shadow-sm">
                            <img src="../img/<?php echo htmlspecialchars($f['poster_url']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title fw-bold"><?php echo htmlspecialchars($f['judul']); ?></h6>
                                <p class="small text-muted mb-2"><?php echo htmlspecialchars($f['tahun_rilis']); ?></p>
                                <a href="film.php?id=<?php echo $f['id']; ?>" class="btn btn-sm btn-danger w-100 stretched-link">Lihat</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12"><p class="text-muted">Tidak ditemukan film yang cocok.</p></div>
            <?php endif; ?>
        </div>

        <a href="../index.php" class="btn btn-secondary mb-5">&laquo; Kembali ke Home</a>
    </div>

    

</body>
</html>