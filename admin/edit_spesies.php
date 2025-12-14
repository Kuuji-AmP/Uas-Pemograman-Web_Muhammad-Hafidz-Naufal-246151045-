<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_GET['nama'])) { header('Location: admin_spesies.php'); exit; }
$nama_spesies = $_GET['nama']; // Ambil nama (misal: "Godzilla")

// Cek apakah info sudah ada di database?
$sql = "SELECT * FROM species_info WHERE nama_spesies = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $nama_spesies);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

// Default values jika kosong
$deskripsi = $data ? $data['deskripsi_umum'] : '';
$gambar_lama = $data ? $data['gambar_umum'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Atur Info Spesies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Atur Info General: <?php echo htmlspecialchars($nama_spesies); ?></h4>
            </div>
            <div class="card-body">
                <form action="../actions/proses_edit_spesies.php" method="POST" enctype="multipart/form-data">
                    
                    <input type="hidden" name="nama_spesies" value="<?php echo htmlspecialchars($nama_spesies); ?>">
                    <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($gambar_lama); ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Umum</label>
                        <textarea class="form-control" name="deskripsi_umum" rows="6" placeholder="Jelaskan sejarah dan karakteristik umum monster ini..."><?php echo htmlspecialchars($deskripsi); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar Header General</label><br>
                        
                        <?php if (!empty($gambar_lama)): ?>
                            <img src="../img/<?php echo htmlspecialchars($gambar_lama); ?>" width="200" class="mb-2 img-thumbnail">
                            <div class="small text-muted mb-2">Gambar saat ini</div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control" name="gambar_umum" accept="image/*">
                        <div class="form-text">Upload gambar baru untuk mengganti.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="admin_spesies.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Info</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    
    <script>
        // Cari semua textarea dan ubah jadi editor
        const textareas = document.querySelectorAll('textarea');
        
        textareas.forEach((el) => {
            ClassicEditor
                .create(el)
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
    
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
        }
    </style>
</body>
</html>