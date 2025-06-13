<?php
session_start(); // Wajib untuk menggunakan $_SESSION
require_once 'config/db_connect.php';

// Set header agar respon dikembalikan sebagai JSON
header('Content-Type: application/json');

// --- OPTIONAL: Aktifkan error reporting saat pengembangan ---
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Pastikan ada food_id dari POST
if (isset($_POST['food_id'])) {
    $food_id = (int)$_POST['food_id'];

    // Ambil data produk dari database
    $query = "SELECT id, name, price, image_url FROM foods WHERE id = $food_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $food = mysqli_fetch_assoc($result);

        // Tambahkan ke keranjang
        if (isset($_SESSION['cart'][$food_id])) {
            $_SESSION['cart'][$food_id]['quantity']++;
        } else {
            $_SESSION['cart'][$food_id] = [
                'name' => $food['name'],
                'price' => (float)$food['price'],
                'image_url' => $food['image_url'],
                'quantity' => 1
            ];
        }

        // Kirim respons JSON
        echo json_encode([
            'cart' => $_SESSION['cart'],
            'totals' => calculate_totals()
        ]);
        exit;
    } else {
        // Jika makanan tidak ditemukan di database
        http_response_code(404);
        echo json_encode(['error' => 'Item not found.']);
        exit;
    }
} else {
    // Jika tidak ada food_id yang dikirim
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

// --- Fungsi menghitung total belanja ---
function calculate_totals() {
    $sub_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $sub_total += $item['price'] * $item['quantity'];
    }

    $tax = $sub_total * 0.10; // 10% pajak
    $total = $sub_total + $tax;

    return [
        'sub_total' => $sub_total,
        'tax' => $tax,
        'total' => $total
    ];
}
