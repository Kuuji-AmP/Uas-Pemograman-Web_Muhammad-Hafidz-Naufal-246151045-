<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Login & Role (HANYA ADMIN)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// 2. Ambil Semua User
$sql = "SELECT * FROM users ORDER BY role ASC, username ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Kaijupedia</title>
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
        <h2 class="mb-4">Kelola Pengguna</h2>

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
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role Saat Ini</th>
                                <th>Aksi (Ubah Role)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="<?php echo ($row['is_deleted'] == 1) ? 'table-danger text-muted' : ''; ?>">
                                    <td><?php echo $row['id']; ?></td>
                                    <td class="fw-bold">
                                        <i class="bi bi-person-circle text-secondary me-2"></i>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                        
                                        <?php if ($row['id'] == $_SESSION['user_id']) echo '<span class="badge bg-info ms-2">Saya</span>'; ?>
                                        
                                        <?php if ($row['is_deleted'] == 1) echo '<span class="badge bg-danger ms-2">Non-Aktif</span>'; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo ucfirst($row['role']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                            
                                            <div class="d-flex gap-2">
                                                <?php if ($row['is_deleted'] == 0): ?>
                                                    <form action="../actions/proses_ubah_role.php" method="POST" class="d-flex gap-2">
                                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                        <select name="role_baru" class="form-select form-select-sm" style="width: 100px;">
                                                            <option value="member" <?php if($row['role']=='member') echo 'selected'; ?>>Member</option>
                                                            <option value="manager" <?php if($row['role']=='manager') echo 'selected'; ?>>Manager</option>
                                                            <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Simpan Role"><i class="bi bi-check-lg"></i></button>
                                                    </form>

                                                    <a href="../actions/hapus_user.php?id=<?php echo $row['id']; ?>" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Non-aktifkan user ini?');"
                                                    title="Non-aktifkan User">
                                                        <i class="bi bi-trash"></i>
                                                    </a>

                                                <?php else: ?>
                                                    <a href="../actions/restore_user.php?id=<?php echo $row['id']; ?>" 
                                                    class="btn btn-sm btn-success w-100"
                                                    onclick="return confirm('Aktifkan kembali user ini?');">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Restore User
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                        <?php else: ?>
                                            <span class="text-muted small"><i>Tidak ada aksi</i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>