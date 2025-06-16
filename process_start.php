<?php
session_start();

// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_sbd"; // ganti dengan nama database Anda

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_POST['no_meja']) {
    $_SESSION['no_meja'] = $_POST['no_meja'];

    // Simpan sesi pemesanan
    $id_sesi = $_SESSION['id_sesi'];
    $no_meja = $_POST['no_meja'];
    $id_pelanggan = 'P' . date('YmdHis');

    // Pastikan koneksi database sudah dibuat
    // include 'config.php'; // uncomment jika diperlukan

    // Cek apakah pelanggan sudah ada di tabel pelanggan
    $check_query = "SELECT ID_Pelanggan FROM pelanggan WHERE ID_Pelanggan = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $id_pelanggan);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    // Jika pelanggan belum ada, insert ke tabel pelanggan terlebih dahulu
    if (mysqli_num_rows($result) == 0) {
        $insert_pelanggan_query = "INSERT INTO pelanggan (ID_Pelanggan, Nama_Pelanggan, No_Telepon) VALUES (?, 'Guest', '')";
        $insert_pelanggan_stmt = mysqli_prepare($conn, $insert_pelanggan_query);
        mysqli_stmt_bind_param($insert_pelanggan_stmt, "s", $id_pelanggan);
        
        if (!mysqli_stmt_execute($insert_pelanggan_stmt)) {
            echo "Error creating customer: " . mysqli_error($conn);
            exit();
        }
    }

    // Insert ke tabel sesi_pemesanan dengan nama kolom yang benar
    $query = "INSERT INTO sesi_pemesanan (ID_Sesi, No_Meja, ID_Pelanggan) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $id_sesi, $no_meja, $id_pelanggan);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['id_pelanggan'] = $id_pelanggan;
        header('Location: shop_selection.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header('Location: index.php');
    exit();
}
?>