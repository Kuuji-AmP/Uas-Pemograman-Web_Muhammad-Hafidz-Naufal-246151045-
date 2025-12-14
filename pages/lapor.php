<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

// 2. Ambil Data URL
if (!isset($_GET['tipe']) || !isset($_GET['id'])) {
    header('Location: ../index.php'); exit;
}

$tipe = $_GET['tipe']; // 'comment', 'kaiju', atau 'film'
$target_id = $_GET['id'];

// 3. Ambil Info Tambahan (Opsional, biar user tau apa yang dilaporkan)
$info_target = "Konten";
if ($tipe == 'comment') {
    $q = mysqli_query($conn, "SELECT isi_komentar FROM comments WHERE id = $target_id");
    $d = mysqli_fetch_assoc($q);
    if ($d) $info_target = '"' . substr($d['isi_komentar'], 0, 50) . '..."';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporkan Konten - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Laporkan Pelanggaran</h5>
            </div>
            <div class="card-body">
                <p>Anda akan melaporkan: <strong><?php echo htmlspecialchars($info_target); ?></strong></p>
                
                <form action="../actions/proses_lapor.php" method="POST">
                    <input type="hidden" name="target_type" value="<?php echo $tipe; ?>">
                    <input type="hidden" name="target_id" value="<?php echo $target_id; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Pelaporan:</label>
                        <textarea name="alasan" class="form-control" rows="4" placeholder="Contoh: Mengandung kata kasar, spam, atau informasi palsu." required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="javascript:history.back()" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>