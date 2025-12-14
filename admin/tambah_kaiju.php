<?php
session_start();
// Cek apakah user adalah Manager atau Admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kaiju Baru - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Tambah Kaiju Baru</h2>
        
        <form action="../actions/proses_tambah_kaiju.php" method="POST" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Kaiju</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Era</label>
                    <input type="text" class="form-control" name="era" placeholder="Contoh: Showa, Heisei" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tinggi</label>
                    <input type="text" class="form-control" name="tinggi" placeholder="Contoh: 50 meter">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Berat</label>
                    <input type="text" class="form-control" name="berat" placeholder="Contoh: 20.000 ton">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori / Spesies Utama</label>
                <input type="text" class="form-control" name="kategori" placeholder="Contoh: Godzilla, Mothra, Ghidorah" required>
                <div class="form-text">Gunakan nama yang SAMA untuk mengelompokkan kaiju (Misal: semua versi Godzilla tulis 'Godzilla').</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar (Upload File)</label>
                <input type="file" class="form-control" name="gambar" accept="image/*" required>
                <div class="form-text">Format: JPG, PNG, JPEG.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sejarah & Deskripsi</label>
                <textarea class="form-control" name="sejarah_deskripsi" rows="5"></textarea> 
            </div>

            <div class="mb-3">
                <label class="form-label">Kekuatan & Kemampuan</label>
                <textarea class="form-control" name="kemampuan" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kelemahan</label>
                <textarea class="form-control" name="kelemahan" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Simpan Kaiju</button>
            <a href="admin_kaiju.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    
    <script>
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