<?php
// 1. Mulai Session
session_start();

// 2. Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

// 3. Cek Hak Akses (Admin & Manager Boleh Masuk)
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager') {
    header('Location: ../index.php');
    exit;
}

// 4. Koneksi Database
include '../assets/koneksi.php';

// 5. Ambil semua data kaiju (Urutkan dari yang terbaru/ID terbesar)
$sql = "SELECT * FROM kaiju ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kaiju - Kaijupedia Admin</title>
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
            <h2>Kelola Data Kaiju</h2>
            <a href="tambah_kaiju.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Kaiju Baru
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); // Hapus pesan setelah tampil
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Gambar</th>
                                <th width="25%">Nama</th>
                                <th width="20%">Era</th>
                                <th width="15%">Tinggi</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    
                                    <tr class="<?php echo ($row['is_deleted'] == 1) ? 'table-danger text-muted' : ''; ?>">
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <img src="../img/<?php echo htmlspecialchars($row['gambar']); ?>" 
                                                class="img-thumbnail" 
                                                style="height: 60px; width: 60px; object-fit: cover; opacity: <?php echo ($row['is_deleted'] == 1) ? '0.5' : '1'; ?>;">
                                        </td>
                                        <td class="fw-bold">
                                            <?php echo htmlspecialchars($row['nama']); ?>
                                            <?php if ($row['is_deleted'] == 1): ?>
                                                <span class="badge bg-danger">Terhapus</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['era']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tinggi']); ?></td>
                                        <td>
                                            <?php if ($row['is_deleted'] == 0): ?>
                                                <a href="edit_kaiju.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white me-1">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <a href="../actions/hapus_kaiju.php?id=<?php echo $row['id']; ?>" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Sembunyikan data ini?');">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            <?php else: ?>
                                                <a href="../actions/restore_kaiju.php?id=<?php echo $row['id']; ?>" 
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Kembalikan data ini agar tampil lagi?');">
                                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
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