<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Akses
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

// 2. Ambil ID dari URL
if (!isset($_GET['id'])) {
    header('Location: admin_kaiju.php');
    exit;
}
$id = $_GET['id'];

// 3. Ambil Data Lama dari Database
$sql = "SELECT * FROM kaiju WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan
if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kaiju - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Edit Data Kaiju: <?php echo htmlspecialchars($data['nama']); ?></h2>
        
        <form action="../actions/proses_edit_kaiju.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            <input type="hidden" name="gambar_lama" value="<?php echo $data['gambar']; ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Kaiju</label>
                    <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Era</label>
                    <input type="text" class="form-control" name="era" value="<?php echo htmlspecialchars($data['era']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tinggi</label>
                    <input type="text" class="form-control" name="tinggi" value="<?php echo htmlspecialchars($data['tinggi']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Berat</label>
                    <input type="text" class="form-control" name="berat" value="<?php echo htmlspecialchars($data['berat']); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori / Spesies Utama</label>
                <input type="text" class="form-control" name="kategori" placeholder="Contoh: Godzilla, Mothra, Ghidorah" required value="<?php echo isset($data) ? htmlspecialchars($data['kategori']) : ''; ?>">
                <div class="form-text">Gunakan nama yang SAMA untuk mengelompokkan kaiju (Misal: semua versi Godzilla tulis 'Godzilla').</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini</label><br>
                <img src="../img/<?php echo htmlspecialchars($data['gambar']); ?>" width="150" class="img-thumbnail mb-2">
                
                <label class="form-label d-block">Ganti Gambar (Kosongkan jika tidak ingin mengganti)</label>
                <input type="file" class="form-control" name="gambar" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label">Sejarah & Deskripsi</label>
                <textarea class="form-control" name="sejarah_deskripsi" rows="5" required><?php echo htmlspecialchars($data['sejarah_deskripsi']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kekuatan & Kemampuan</label>
                <textarea class="form-control" name="kemampuan" rows="3"><?php echo htmlspecialchars($data['kemampuan']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kelemahan</label>
                <textarea class="form-control" name="kelemahan" rows="3"><?php echo htmlspecialchars($data['kelemahan']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Perubahan</button>
            <a href="admin_kaiju.php" class="btn btn-secondary">Batal</a>
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