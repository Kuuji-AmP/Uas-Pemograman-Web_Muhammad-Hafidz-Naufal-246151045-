<?php
// 1. Mulai Session
session_start();

// 2. Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

// 3. AMBIL ROLE
$role = $_SESSION['role'];

// 4. CEK HAK AKSES
// Hanya 'admin' ATAU 'manager' yang boleh masuk. Member ditendang.
if ($role != 'admin' && $role != 'manager') {
    header('Location: ../index.php');
    exit;
}

// 5. Sertakan Koneksi
include '../assets/koneksi.php';

// --- HITUNG STATISTIK (Kita hitung semua agar dashboard terlihat hidup) ---
$res_kaiju = mysqli_query($conn, "SELECT COUNT(*) as total FROM kaiju");
$total_kaiju = mysqli_fetch_assoc($res_kaiju)['total'];

$res_film = mysqli_query($conn, "SELECT COUNT(*) as total FROM films");
$total_film = mysqli_fetch_assoc($res_film)['total'];

$res_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_user = mysqli_fetch_assoc($res_user)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .admin-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 10px;
            color: white;
            height: 100%;
        }
        .admin-card:hover { transform: translateY(-5px); }
        .card-icon { font-size: 3rem; opacity: 0.8; }
        .bg-kaiju { background: linear-gradient(45deg, #11998e, #38ef7d); }
        .bg-film { background: linear-gradient(45deg, #FF512F, #DD2476); }
        .bg-user { background: linear-gradient(45deg, #4568DC, #B06AB3); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                Kaijupedia <?php echo ($role == 'admin') ? 'Admin' : 'Manager'; ?> Panel
            </a>
            <div class="d-flex">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <span class="badge bg-light text-dark ms-1"><?php echo ucfirst($role); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../index.php"><i class="bi bi-house-fill"></i> Ke Website Utama</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../pages/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="container mt-5">
        <h2 class="mb-4">Dashboard <?php echo ucfirst($role); ?></h2>
        
        <?php
        // Hitung laporan yang belum selesai
        $cek_laporan = mysqli_query($conn, "SELECT COUNT(*) as total FROM reports WHERE status = 'pending'");
        $total_laporan = mysqli_fetch_assoc($cek_laporan)['total'];
        
        if ($total_laporan > 0): 
        ?>
            <div class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm mb-4">
                <div>
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Perhatian!</h4>
                    <p class="mb-0">Ada <strong><?php echo $total_laporan; ?></strong> laporan pelanggaran yang perlu ditinjau.</p>
                </div>
                <a href="admin_laporan.php" class="btn btn-warning fw-bold">
                    <i class="bi bi-eye"></i> Cek Laporan
                </a>
            </div>
        <?php else: ?>
            <div class="row mb-3">
                <div class="col-12 text-end">
                    <a href="admin_laporan.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-flag"></i> Riwayat Laporan
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <div class="row mb-5">
        <div class="row mb-5">
            
            <?php if ($role == 'manager' || $role == 'admin'): ?>
            
            <div class="col-md-6 mb-3">
                <div class="card admin-card bg-kaiju p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Data Kaiju</h3>
                            <h1 class="mb-0"><?php echo $total_kaiju; ?></h1>
                            <p>Monster terdaftar</p>
                        </div>
                        <i class="bi bi-reddit card-icon"></i>
                    </div>
                    <a href="admin_kaiju.php" class="btn btn-light btn-lg mt-3 text-success fw-bold w-100">
                        Kelola Kaiju <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card admin-card p-4" style="background: linear-gradient(45deg, #FF9966, #FF5E62);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Info Spesies</h3>
                            <p class="mb-0">Deskripsi General</p>
                        </div>
                        <i class="bi bi-journal-text card-icon"></i>
                    </div>
                    <a href="admin_spesies.php" class="btn btn-light btn-sm mt-3 text-danger fw-bold w-100">Kelola Spesies <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card admin-card bg-film p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Data Film</h3>
                            <h1 class="mb-0"><?php echo $total_film; ?></h1>
                            <p>Judul film</p>
                        </div>
                        <i class="bi bi-film card-icon"></i>
                    </div>
                    <a href="admin_films.php" class="btn btn-light btn-lg mt-3 text-danger fw-bold w-100">
                        Kelola Film <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <?php endif; ?>


            <?php if ($role == 'admin'): ?>

            <div class="col-md-6 mb-3">
                <div class="card admin-card bg-user p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title">Pengguna</h3>
                            <h1 class="mb-0"><?php echo $total_user; ?></h1>
                            <p>Member aktif</p>
                        </div>
                        <i class="bi bi-people-fill card-icon"></i>
                    </div>
                    <a href="admin_users.php" class="btn btn-light btn-lg mt-3 text-primary fw-bold w-100">
                        Kelola Akun User <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                 <div class="alert alert-info h-100">
                     <h4>Info Admin</h4>
                     <p>Sebagai admin, tugas Anda adalah mengelola akun pengguna, mereset password jika ada yang lupa, atau memblokir member yang melanggar aturan.</p>
                 </div>
            </div>

            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>