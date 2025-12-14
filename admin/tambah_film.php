<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Film Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Tambah Film Baru</h2>
        
        <form action="../actions/proses_tambah_film.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Film</label>
                <input type="text" class="form-control" name="judul" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Era / Series</label>
                <select class="form-select" name="era" required>
                    <option value="">-- Pilih Era --</option>
                    <option value="Showa">Showa (1954–1975)</option>
                    <option value="Heisei">Heisei (1984–1995)</option>
                    <option value="Millennium">Millennium (1999–2004)</option>
                    <option value="Reiwa">Reiwa (2016–Sekarang)</option>
                    <option value="Monsterverse">Monsterverse (Legendary)</option>
                    <option value="Lainnya">Lainnya (Tristar/Animasi)</option>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Rilis</label>
                    <input type="text" class="form-control" name="tahun_rilis" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sutradara</label>
                    <input type="text" class="form-control" name="sutradara">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster (Upload Gambar)</label>
                <input type="file" class="form-control" name="poster" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Sinopsis</label>
                <textarea class="form-control" name="sinopsis" rows="5"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Monster yang Muncul di Film Ini:</label>
                <div class="card p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    <?php
                    // Ambil semua data kaiju untuk dijadikan pilihan
                    include '../assets/koneksi.php'; // Pastikan include koneksi ada
                    $q_kaiju = mysqli_query($conn, "SELECT id, nama FROM kaiju ORDER BY nama ASC");
                    
                    if (mysqli_num_rows($q_kaiju) > 0) {
                        while ($k = mysqli_fetch_assoc($q_kaiju)) {
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="kaiju_id[]" value="<?php echo $k['id']; ?>" id="k_<?php echo $k['id']; ?>">
                                <label class="form-check-label" for="k_<?php echo $k['id']; ?>">
                                    <?php echo htmlspecialchars($k['nama']); ?>
                                </label>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-muted small">Belum ada data Kaiju. Tambahkan Kaiju dulu.</p>';
                    }
                    ?>
                </div>
                <div class="form-text">Centang monster yang tampil di film ini.</div>
            </div>
            <button type="submit" class="btn btn-danger">Simpan Film</button>
            <a href="admin_films.php" class="btn btn-secondary">Batal</a>
        </form>
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