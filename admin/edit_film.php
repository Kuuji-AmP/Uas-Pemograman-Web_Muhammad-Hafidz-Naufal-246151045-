<?php
session_start();
include '../assets/koneksi.php';

// 1. Cek Login & Role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php');
    exit;
}

// 2. Ambil ID Film
if (!isset($_GET['id'])) {
    header('Location: admin_films.php');
    exit;
}
$id = $_GET['id'];

// 3. Ambil Data Film Lama
$sql = "SELECT * FROM films WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$film = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$film) { echo "Film tidak ditemukan!"; exit; }

// 4. AMBIL DAFTAR KAIJU YANG SUDAH TERHUBUNG (PENTING)
// Kita butuh ini untuk mencentang checkbox secara otomatis
$sql_connected = "SELECT kaiju_id FROM film_kaiju WHERE film_id = ?";
$stmt_conn = mysqli_prepare($conn, $sql_connected);
mysqli_stmt_bind_param($stmt_conn, "i", $id);
mysqli_stmt_execute($stmt_conn);
$result_conn = mysqli_stmt_get_result($stmt_conn);

// Simpan ID kaiju yang terhubung ke dalam array sederhana [1, 5, 8]
$connected_kaiju_ids = [];
while ($row = mysqli_fetch_assoc($result_conn)) {
    $connected_kaiju_ids[] = $row['kaiju_id'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Film - Kaijupedia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5 mb-5">
        <h2 class="mb-4">Edit Film: <?php echo htmlspecialchars($film['judul']); ?></h2>
        
        <form action="../actions/proses_edit_film.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $film['id']; ?>">
            <input type="hidden" name="poster_lama" value="<?php echo $film['poster_url']; ?>">

            <div class="mb-3">
                <label class="form-label">Judul Film</label>
                <input type="text" class="form-control" name="judul" value="<?php echo htmlspecialchars($film['judul']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Era / Series</label>
                <select class="form-select" name="era" required>
                    <option value="">-- Pilih Era --</option>
                    <?php
                    $eras = ["Showa", "Heisei", "Millennium", "Reiwa", "Monsterverse", "Lainnya"];
                    foreach ($eras as $e) {
                        $selected = ($film['era'] == $e) ? 'selected' : '';
                        echo "<option value='$e' $selected>$e</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tahun Rilis</label>
                    <input type="text" class="form-control" name="tahun_rilis" value="<?php echo htmlspecialchars($film['tahun_rilis']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sutradara</label>
                    <input type="text" class="form-control" name="sutradara" value="<?php echo htmlspecialchars($film['sutradara']); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster Saat Ini</label><br>
                <img src="../img/<?php echo htmlspecialchars($film['poster_url']); ?>" width="100" class="img-thumbnail mb-2">
                <input type="file" class="form-control" name="poster" accept="image/*">
                <div class="form-text">Biarkan kosong jika tidak ingin mengganti poster.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sinopsis</label>
                <textarea class="form-control" name="sinopsis" rows="5"><?php echo htmlspecialchars($film['sinopsis']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Monster di Film Ini:</label>
                <div class="card p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                    <?php
                    $q_all_kaiju = mysqli_query($conn, "SELECT id, nama FROM kaiju ORDER BY nama ASC");
                    while ($k = mysqli_fetch_assoc($q_all_kaiju)) {
                        // Logika Cek: Apakah ID kaiju ini ada di array $connected_kaiju_ids?
                        // Jika ya, tambahkan atribut 'checked'
                        $checked = in_array($k['id'], $connected_kaiju_ids) ? 'checked' : '';
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kaiju_id[]" 
                                   value="<?php echo $k['id']; ?>" 
                                   id="k_<?php echo $k['id']; ?>" 
                                   <?php echo $checked; ?>>
                            <label class="form-check-label" for="k_<?php echo $k['id']; ?>">
                                <?php echo htmlspecialchars($k['nama']); ?>
                            </label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Film</button>
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