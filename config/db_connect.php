<?php
// Pengaturan Database
$db_host = 'localhost';
$db_user = 'root'; // Default user XAMPP
$db_pass = '';     // Default password XAMPP
$db_name = 'db_foodslice';

// Membuat koneksi
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>