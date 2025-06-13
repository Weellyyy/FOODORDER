<?php
session_start(); // Wajib ada di setiap halaman yang menggunakan session
require_once 'config/db_connect.php';

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Pastikan ada food_id yang dikirim
if (isset($_POST['food_id'])) {
    $food_id = (int)$_POST['food_id'];

    // Ambil data produk dari DB untuk memastikan valid
    $query = "SELECT id, name, price, image_url FROM foods WHERE id = $food_id";
    $result = mysqli_query($conn, $query);
    $food = mysqli_fetch_assoc($result);

    if ($food) {
        // Jika produk sudah ada di keranjang, tambah jumlahnya
        if (isset($_SESSION['cart'][$food_id])) {
            $_SESSION['cart'][$food_id]['quantity']++;
        } else {
            // Jika belum ada, tambahkan ke keranjang
            $_SESSION['cart'][$food_id] = [
                'name' => $food['name'],
                'price' => $food['price'],
                'image_url' => $food['image_url'],
                'quantity' => 1
            ];
        }
    }
}

// Fungsi untuk menghitung total
function calculate_total() {
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

// Mengembalikan data keranjang dalam format JSON untuk diolah JavaScript
header('Content-Type: application/json');
echo json_encode([
    'cart' => $_SESSION['cart'],
    'total' => calculate_total()
]);