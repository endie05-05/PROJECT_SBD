<?php
// koneksi.php - Koneksi database sederhana
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'database_bc_unand';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
echo "<!-- Koneksi berhasil -->";
?>
