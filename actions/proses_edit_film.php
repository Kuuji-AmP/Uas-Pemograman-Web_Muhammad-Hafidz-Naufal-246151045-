<?php
session_start();
include '../assets/koneksi.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'admin')) {
    header('Location: ../index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    
    // 1. TANGKAP ERA (Ini yang sebelumnya kurang)
    $era = $_POST['era']; 
    
    $tahun = $_POST['tahun_rilis'];
    $sutradara = $_POST['sutradara'];
    $sinopsis = $_POST['sinopsis'];
    $poster_lama = $_POST['poster_lama'];

    // 2. Cek Upload Poster Baru
    if ($_FILES['poster']['error'] === 4) {
        $poster_final = $poster_lama; // Pakai lama
    } else {
        $nama_file = $_FILES['poster']['name'];
        $tmp_file = $_FILES['poster']['tmp_name'];
        $poster_final = time() . '_' . $nama_file;
        // Perhatikan path upload (../img/)
        move_uploaded_file($tmp_file, "../img/" . $poster_final);
        
        // Hapus poster lama (Opsional)
        if (file_exists("../img/" . $poster_lama)) { unlink("../img/" . $poster_lama); }
    }

    // 3. Update Data Film (Tambahkan kolom era=?)
    $sql = "UPDATE films SET judul=?, era=?, tahun_rilis=?, sutradara=?, sinopsis=?, poster_url=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    // 4. Update Bind Param (tambah satu 's' untuk era, dan masukkan variabel $era)
    // Urutan: judul(s), era(s), tahun(s), sutradara(s), sinopsis(s), poster(s), id(i) => "ssssssi"
    mysqli_stmt_bind_param($stmt, "ssssssi", $judul, $era, $tahun, $sutradara, $sinopsis, $poster_final, $id);
    
    if (mysqli_stmt_execute($stmt)) {

        // 5. UPDATE RELASI KAIJU (Reset & Re-insert)
        
        // A. Hapus semua koneksi lama dulu
        mysqli_query($conn, "DELETE FROM film_kaiju WHERE film_id = $id");

        // B. Masukkan koneksi baru (jika ada yang dicentang)
        if (isset($_POST['kaiju_id'])) {
            $kaiju_list = $_POST['kaiju_id'];
            foreach ($kaiju_list as $k_id) {
                $k_id = (int)$k_id;
                mysqli_query($conn, "INSERT INTO film_kaiju (film_id, kaiju_id) VALUES ($id, $k_id)");
            }
        }

        $_SESSION['message'] = "Film berhasil diupdate!";
        header('Location: ../admin/admin_films.php');

    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}
?>