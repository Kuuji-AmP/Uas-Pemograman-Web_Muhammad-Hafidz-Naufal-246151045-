<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $era = $_POST['era'];
    $tahun = $_POST['tahun_rilis'];
    $sutradara = $_POST['sutradara'];
    $sinopsis = $_POST['sinopsis'];

    // Upload Poster
    $poster_name = $_FILES['poster']['name'];
    $poster_tmp = $_FILES['poster']['tmp_name'];
    $poster_baru = time() . '_' . $poster_name; // Rename unik
    
    if (move_uploaded_file($poster_tmp, "../img/" . $poster_baru)) {
        
        // Update Query INSERT (Tambah kolom era)
        $sql = "INSERT INTO films (judul, era, tahun_rilis, sutradara, sinopsis, poster_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $judul, $era, $tahun, $sutradara, $sinopsis, $poster_baru);

        if (mysqli_stmt_execute($stmt)) {
            
            // 1. Ambil ID Film yang baru saja dibuat
            $film_id_baru = mysqli_insert_id($conn);

            // 2. Cek apakah ada Kaiju yang dicentang?
            if (isset($_POST['kaiju_id'])) {
                $kaiju_yang_dipilih = $_POST['kaiju_id']; // Ini berbentuk array [1, 3, 5...]

                // 3. Looping untuk menyimpan setiap hubungan ke tabel 'film_kaiju'
                foreach ($kaiju_yang_dipilih as $k_id) {
                    // Masukkan ke tabel penghubung
                    // Pastikan $k_id aman (integer)
                    $k_id = (int)$k_id; 
                    $sql_relasi = "INSERT INTO film_kaiju (film_id, kaiju_id) VALUES ($film_id_baru, $k_id)";
                    mysqli_query($conn, $sql_relasi);
                }
            }

            $_SESSION['message'] = "Film dan data monsternya berhasil ditambahkan!";
            header('Location: ../admin/admin_films.php');
            
        } else {
             // ... error handling lama Anda ...
             echo "Gagal insert DB: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal upload gambar.";
    }
}
?>