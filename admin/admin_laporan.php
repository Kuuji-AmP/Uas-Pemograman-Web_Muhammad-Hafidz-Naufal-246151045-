<?php
session_start();
include '../assets/koneksi.php';

// Cek Admin/Manager
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

// Ambil semua laporan (Pending di atas)
$sql = "SELECT r.*, u.username as pelapor 
        FROM reports r 
        JOIN users u ON r.reporter_user_id = u.id 
        ORDER BY r.status ASC, r.reported_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Moderasi Laporan - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">⬅ Kembali ke Panel Admin</a>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Laporan Masuk</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Pelapor</th>
                            <th>Tipe</th>
                            <th>Alasan</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['pelapor']); ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $row['target_type']; ?></span>
                                    <small class="text-muted d-block">ID: <?php echo $row['target_id']; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['reported_at'])); ?></td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php elseif($row['status'] == 'resolved'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Diabaikan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <div class="btn-group btn-group-sm">
                                            <?php if($row['target_type'] == 'comment'): ?>
                                                <a href="../actions/hapus_komentar.php?id=<?php echo $row['target_id']; ?>&report_id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-danger" 
                                                   onclick="return confirm('Hapus komentar ini selamanya?');">Hapus Konten</a>
                                            <?php endif; ?>
                                            
                                            <a href="../actions/proses_status_laporan.php?id=<?php echo $row['id']; ?>&status=ignored" class="btn btn-secondary">Abaikan</a>
                                            
                                            <a href="../actions/proses_status_laporan.php?id=<?php echo $row['id']; ?>&status=resolved" class="btn btn-success"><i class="bi bi-check"></i></a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>