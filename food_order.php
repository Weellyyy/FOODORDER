<?php
session_start();
require_once 'config/db_connect.php';

$current_page = 'food_order.php';

// Ambil kategori
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// Ambil semua makanan
$foods_query = "SELECT * FROM foods";
$foods_result = mysqli_query($conn, $foods_query);

// Fungsi hitung total (sama seperti di index.php & handle_cart.php)
function calculate_totals() {
    $sub_total = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $sub_total += $item['price'] * $item['quantity'];
    }
    $tax = $sub_total * 0.10;
    $total = $sub_total + $tax;
    return [
        'sub_total' => $sub_total,
        'tax' => $tax,
        'total' => $total
    ];
}

$cart_totals = calculate_totals();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Food Order - Welly Restaurant</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h1 class="logo">Welly Restaurant</h1>
        <nav>
            <ul>
                <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><a href="index.php">Dashboard</a></li>
                <li class="<?= $current_page == 'food_order.php' ? 'active' : '' ?>"><a href="food_order.php">Food Order</a></li>
                <li class="<?= $current_page == 'message.php' ? 'active' : '' ?>"><a href="message.php">Message</a></li>
                <li class="<?= $current_page == 'order_history.php' ? 'active' : '' ?>"><a href="order_history.php">Order History</a></li>
                <li class="<?= $current_page == 'payment_details.php' ? 'active' : '' ?>"><a href="payment_details.php">Payment Details</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <div class="search-bar">
                <input type="text" placeholder="Search food, category, etc.">
            </div>
            <div class="user-profile">
                <span>Wellyyy</span>
            </div>
        </header>

        <section>
            <h2>Semua Makanan</h2>
            <div class="items-grid">
                <?php while ($food = mysqli_fetch_assoc($foods_result)) : ?>
                    <div class="food-card">
                        <img src="<?= htmlspecialchars($food['image_url']) ?>" alt="<?= htmlspecialchars($food['name']) ?>">
                        <h3><?= htmlspecialchars($food['name']) ?></h3>
                        <div class="price">$<?= number_format($food['price'], 2) ?></div>
                        <div class="card-actions">
                            <button class="wishlist-btn">Wishlist</button>
                            <button class="order-btn" data-id="<?= $food['id'] ?>">Order Now</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <aside class="invoice">
        <h2>Invoice</h2>
        <div class="invoice-items" id="invoice-items">
            <?php if (empty($_SESSION['cart'])): ?>
                <p class="empty-cart-message">Keranjang Anda masih kosong.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="invoice-item">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-details">
                            <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="item-quantity">x <?= $item['quantity'] ?></p>
                        </div>
                        <strong class="item-price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="payment-summary">
            <div class="summary-row">
                <span>Sub Total</span>
                <span id="sub-total">$<?= number_format($cart_totals['sub_total'], 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax</span>
                <span id="tax">$<?= number_format($cart_totals['tax'], 2) ?></span>
            </div>
            <hr>
            <div class="summary-row total">
                <span>Total Payment</span>
                <span id="total-payment">$<?= number_format($cart_totals['total'], 2) ?></span>
            </div>
        </div>

        <a href="checkout.php" class="place-order-link">
            <button class="place-order-btn" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>
                Place An Order
            </button>
        </a>
    </aside>
</div>

<script src="js/script.js"></script>
</body>
</html>
