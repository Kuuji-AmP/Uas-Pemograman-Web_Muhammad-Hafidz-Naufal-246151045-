<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

// LOGIKA BARU:
// Ambil semua kategori unik dari tabel 'kaiju'
// Lalu gabungkan (LEFT JOIN) dengan tabel 'species_info' untuk melihat apakah infonya sudah ada
$sql = "SELECT DISTINCT k.kategori, s.deskripsi_umum, s.gambar_umum 
        FROM kaiju k 
        LEFT JOIN species_info s ON k.kategori = s.nama_spesies 
        WHERE k.is_deleted = 0 
        ORDER BY k.kategori ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Info Spesies - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">⬅ Kembali ke Admin Panel</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Info General Spesies</h2>
            </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Gambar General</th>
                            <th>Kategori Spesies</th>
                            <th>Status Info</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row['gambar_umum'])): ?>
                                        <img src="../img/<?php echo htmlspecialchars($row['gambar_umum']); ?>" width="80" class="rounded border">
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Belum ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold fs-5"><?php echo htmlspecialchars($row['kategori']); ?></td>
                                <td>
                                    <?php if (!empty($row['deskripsi_umum'])): ?>
                                        <span class="badge bg-success">Sudah Diisi</span>
                                        <small class="text-muted d-block mt-1">
                                            <?php echo substr(htmlspecialchars($row['deskripsi_umum']), 0, 50) . '...'; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Belum Diisi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_spesies.php?nama=<?php echo urlencode($row['kategori']); ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Atur Info
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i> Daftar ini muncul otomatis berdasarkan kolom "Kategori" yang Anda isi saat menambah Kaiju.
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>