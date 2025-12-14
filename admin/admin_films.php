<?php
session_start();
include '../assets/koneksi.php';

// Cek Login & Akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

// Ambil data films
$sql = "SELECT * FROM films ORDER BY tahun_rilis DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Film - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">⬅ Kembali ke Panel Admin</a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Data Film</h2>
            <a href="tambah_film.php" class="btn btn-danger">
                <i class="bi bi-plus-circle"></i> Tambah Film Baru
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Poster</th>
                                <th>Judul</th>
                                <th>Tahun</th>
                                <th>Sutradara</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    
                                    <tr class="<?php echo ($row['is_deleted'] == 1) ? 'table-danger text-muted' : ''; ?>">
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <img src="../img/<?php echo htmlspecialchars($row['poster_url']); ?>" 
                                                 class="img-thumbnail" 
                                                 style="height: 80px; width: 60px; object-fit: cover; opacity: <?php echo ($row['is_deleted'] == 1) ? '0.5' : '1'; ?>;">
                                        </td>
                                        <td class="fw-bold">
                                            <?php echo htmlspecialchars($row['judul']); ?>
                                            <?php if ($row['is_deleted'] == 1): ?>
                                                <span class="badge bg-danger">Terhapus</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['tahun_rilis']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['sutradara']); ?></td>
                                        <td>
                                            <?php if ($row['is_deleted'] == 0): ?>
                                                <a href="edit_film.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white me-1">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <a href="../actions/hapus_film.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Sembunyikan film ini?');">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            <?php else: ?>
                                                <a href="../actions/restore_film.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Kembalikan film ini agar tampil lagi?');">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-4">Belum ada data film.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>