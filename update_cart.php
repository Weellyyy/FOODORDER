<?php
session_start();

// Pastikan ada aksi yang dikirim
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];

    // Jika aksinya adalah 'remove' dan item ada di keranjang
    if ($action == 'remove' && isset($_SESSION['cart'][$id])) {
        // Hapus item dari session cart
        unset($_SESSION['cart'][$id]);
    }

    // Jika aksinya adalah 'update' (bisa Anda kembangkan nanti)
    // if ($action == 'update') { ... }
}

// Setelah memproses, kembalikan pengguna ke halaman checkout
header('Location: checkout.php');
exit();
?>