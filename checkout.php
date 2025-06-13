<?php
session_start();
require_once 'config/db_connect.php';

// KEAMANAN: Jika keranjang kosong, jangan biarkan akses halaman ini.
// Arahkan kembali ke halaman utama.
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// --- LOGIKA PEMROSESAN ORDER SAAT FORM DISUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan bersihkan data dari form
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_address = mysqli_real_escape_string($conn, $_POST['customer_address']);
    
    // 2. Hitung ulang total di sisi server (JANGAN PERCAYA TOTAL DARI CLIENT)
    $sub_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $sub_total += $item['price'] * $item['quantity'];
    }
    $tax = $sub_total * 0.10; // Pajak 10%
    $total_price = $sub_total + $tax;

    // 3. Simpan order ke database menggunakan TRANSACTION
    // Transaction memastikan semua query berhasil, jika satu gagal, semua akan dibatalkan.
    mysqli_begin_transaction($conn);

    try {
        // Query 1: Masukkan ke tabel `orders`
        $sql_order = "INSERT INTO orders (customer_name, total_price) VALUES (?, ?)";
        $stmt_order = mysqli_prepare($conn, $sql_order);
        mysqli_stmt_bind_param($stmt_order, "sd", $customer_name, $total_price);
        mysqli_stmt_execute($stmt_order);

        // Ambil ID dari order yang baru saja dibuat
        $order_id = mysqli_insert_id($conn);

        // Query 2: Masukkan setiap item di keranjang ke tabel `order_items`
        $sql_items = "INSERT INTO order_items (order_id, food_id, quantity, price_per_item) VALUES (?, ?, ?, ?)";
        $stmt_items = mysqli_prepare($conn, $sql_items);

        foreach ($_SESSION['cart'] as $food_id => $item) {
            mysqli_stmt_bind_param($stmt_items, "iiid", $order_id, $food_id, $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt_items);
        }

        // Jika semua query berhasil, commit transaction
        mysqli_commit($conn);

        // 4. Kosongkan keranjang belanja
        unset($_SESSION['cart']);

        // 5. Arahkan ke halaman terima kasih
        header('Location: thankyou.php?order_id=' . $order_id);
        exit();

    } catch (mysqli_sql_exception $exception) {
        // Jika ada error, batalkan semua perubahan di database
        mysqli_rollback($conn);
        $error_message = "Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.";
        // Tampilkan error atau log error
        // die($exception->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Foodislice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> 
    <style>
        /* Tambahan style khusus untuk halaman checkout */
        body { background-color: #F7F8FC; }
        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .checkout-container h2 { text-align: center; margin-bottom: 30px; color: var(--primary-color); }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { font-weight: 600; color: #888; }
        .cart-table img { border-radius: 8px; }
        .cart-table .remove-link { color: #ff4d4d; text-decoration: none; font-size: 14px; }
        .checkout-form { margin-top: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .place-order-btn { width: 100%; } /* Gunakan style dari tombol utama */
    </style>
</head>
<body>

    <div class="checkout-container">
        <h2>Review Pesanan & Checkout</h2>

        <?php if(isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?= $error_message ?></p>
        <?php endif; ?>

        <div class="cart-review">
            <h3>Item di Keranjang Anda</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th colspan="2">Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sub_total_display = 0;
                    foreach ($_SESSION['cart'] as $id => $item):
                        $item_subtotal = $item['price'] * $item['quantity'];
                        $sub_total_display += $item_subtotal;
                    ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['image_url']) ?>" width="60" alt=""></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item_subtotal, 2) ?></td>
                        <td>
                            <a href="update_cart.php?action=remove&id=<?= $id ?>" class="remove-link">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="payment-summary">
                <?php
                    $tax_display = $sub_total_display * 0.10;
                    $total_display = $sub_total_display + $tax_display;
                ?>
                <div class="summary-row"><span>Sub Total</span> <span>$<?= number_format($sub_total_display, 2) ?></span></div>
                <div class="summary-row"><span>Pajak (10%)</span> <span>$<?= number_format($tax_display, 2) ?></span></div>
                <hr>
                <div class="summary-row total"><span>Total Pembayaran</span> <span>$<?= number_format($total_display, 2) ?></span></div>
            </div>
        </div>

        <div class="checkout-form">
            <h3>Data Pemesan</h3>
            <form action="checkout.php" method="POST">
                <div class="form-group">
                    <label for="customer_name">Nama Lengkap</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label for="customer_address">Alamat Pengiriman</label>
                    <input type="text" id="customer_address" name="customer_address" required>
                </div>
                <button type="submit" class="place-order-btn">Buat Pesanan Sekarang</button>
            </form>
        </div>
    </div>

</body>
</html>