<?php
// 1. Sertakan file koneksi
include '../assets/koneksi.php';

// 1. Selalu mulai session
session_start();

// 2. Cek apakah pengguna BELUM login
if (!isset($_SESSION['user_id'])) {
    
    // 3. Jika belum, tendang (redirect) mereka ke halaman login
    header('Location: login.php');
    exit; // 4. Hentikan sisa skrip agar halaman tidak dimuat
}

// 3. Ambil ID dari URL dan bersihkan
$id = $_GET['id'];

// 4. --- KEAMANAN SQL INJECTION (PENTING!) ---
//    Kita akan menggunakan "Prepared Statements" dengan mysqli.
//    Jangan pernah memasukkan variabel $_GET langsung ke query SQL.

// 4a. Buat template SQL dengan tanda tanya (?) sebagai placeholder
$sql = "SELECT * FROM kaiju WHERE id = ?";

// 4b. Siapkan statement
$stmt = mysqli_prepare($conn, $sql);

// 4c. "Bind" (ikat) variabel $id ke placeholder tanda tanya
//    "i" berarti tipe datanya adalah integer (angka)
mysqli_stmt_bind_param($stmt, "i", $id);

// 4d. Eksekusi statement
mysqli_stmt_execute($stmt);

// 4e. Ambil hasilnya
$result = mysqli_stmt_get_result($stmt);

// 4f. Ambil satu baris data sebagai array
$kaiju = mysqli_fetch_assoc($result);

// 5. Cek apakah kaiju dengan ID itu ditemukan
if (!$kaiju) {
    // Jika tidak ada data, tampilkan pesan dan hentikan skrip
    echo "Data kaiju tidak ditemukan.";
    exit;
}

// Jika berhasil, variabel $kaiju sekarang berisi semua data monster itu
// (Contoh: $kaiju['nama'], $kaiju['sejarah_deskripsi'], dll.)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($kaiju['nama']); ?> - Godzilla Wiki</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="banner_deskripsi banner p-5 text-light">
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

    <div class="container mt-4">
        <h1 class="mb-3"><?php echo htmlspecialchars($kaiju['nama']); ?></h1>
        
        <div class="row">
            
            <div class="col-md-8">
                <h3>Sejarah & Deskripsi</h3>
                <p>
                    <?php
                    echo $kaiju['sejarah_deskripsi']; ?> 
                </p>

                <h3>Kekuatan & Kemampuan</h3>
                <div class="mb-3">
                    <?php echo $kaiju['kemampuan']; ?>  </div>
                
                <h3>Kelemahan</h3>
                <div class="mb-3">
                    <?php echo $kaiju['kelemahan']; ?>  </div>
            </div>
            
            <div class="col-md-4">
                <div class="infobox">
                    <img src="../img/<?php echo htmlspecialchars($kaiju['gambar']); ?>" 
                         class="kaiju-image mb-3 img-fluid" 
                         alt="<?php echo htmlspecialchars($kaiju['nama']); ?>">
                    
                    <h5 class="text-center"><?php echo htmlspecialchars($kaiju['nama']); ?></h5>
                    <hr>
                    
                    <p><strong>Era:</strong> <?php echo htmlspecialchars($kaiju['era']); ?></p>
                    <p><strong>Tinggi:</strong> <?php echo htmlspecialchars($kaiju['tinggi']); ?></p>
                    <p><strong>Berat:</strong> <?php echo htmlspecialchars($kaiju['berat']); ?></p>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <a href="../index.php" class="btn btn-primary">&laquo; Kembali ke Daftar</a>

    <?php
    // 9. Tutup statement dan koneksi
    mysqli_stmt_close($stmt);

    ?>
    
    <hr class="my-5">

        <div class="row">
            <div class="col-md-8">
                <h3 class="mb-4">Komentar Diskusi</h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <form action="../actions/proses_komentar.php" method="POST">
                                <input type="hidden" name="halaman_tipe" value="kaiju">
                                <input type="hidden" name="halaman_id" value="<?php echo $kaiju['id']; ?>">
                                
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
                              WHERE c.halaman_tipe = 'kaiju' AND c.halaman_id = ? 
                              ORDER BY c.tanggal_posting DESC";
                
                $stmt_k = mysqli_prepare($conn, $sql_komen);
                mysqli_stmt_bind_param($stmt_k, "i", $kaiju['id']);
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