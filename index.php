<?php
// Memulai session di baris paling atas
session_start();

// Memasukkan file koneksi database
require_once 'config/db_connect.php';

// --- PENGAMBILAN DATA DARI DATABASE ---

// 1. Mengambil data semua kategori
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// 2. Mengambil data makanan yang populer (misalnya, limit 3)
$popular_foods_query = "SELECT * FROM foods WHERE is_popular = TRUE LIMIT 3";
$popular_foods_result = mysqli_query($conn, $popular_foods_query);

// 3. Mengambil semua data makanan (bisa juga ditambahkan pagination nanti)
$all_foods_query = "SELECT * FROM foods WHERE is_popular = FALSE";
$all_foods_result = mysqli_query($conn, $all_foods_query);


// --- FUNGSI BANTU UNTUK KERANJANG ---
function calculate_totals() {
    $sub_total = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $sub_total += $item['price'] * $item['quantity'];
        }
    }
    // Pajak bisa diatur di sini, misalnya 10%
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodislice - Pesan Makanan Favoritmu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="container">
        <aside class="sidebar">
            <h1 class="logo">foodislice</h1>
            <nav>
                <ul>
                    <li class="active"><a href="index.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-utensils"></i> Food Order</a></li>
                    <li><a href="#"><i class="fas fa-comment-dots"></i> Feedback</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a href="order_history.php"><i class="fas fa-history"></i> Order History</a></li>
                    <li><a href="#"><i class="fas fa-credit-card"></i> Payment Details</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Customization</a></li>
                </ul>
            </nav>
            <div class="sidebar-promo">
                 </div>
        </aside>

        <main class="main-content">
            <header>
                <div class="search-bar">
                    <input type="text" placeholder="Search food, category, etc.">
                </div>
                <div class="user-profile">
                    <span>David Brown</span>
                    </div>
            </header>

            <section class="categories">
                <h2>Explore Categories</h2>
                <div class="category-list">
                    <?php while ($category = mysqli_fetch_assoc($categories_result)) : ?>
                        <div class="category-item">
                            <span><?= htmlspecialchars($category['name']) ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <section class="food-list">
                <div class="list-header">
                    <h2>Popular</h2>
                    </div>
                <div class="items-grid" id="food-container-popular">
                    <?php while ($food = mysqli_fetch_assoc($popular_foods_result)) : ?>
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
            
            <section class="food-list">
                 <h2>Semua Menu</h2>
                <div class="items-grid" id="food-container-all">
                     <?php while ($food = mysqli_fetch_assoc($all_foods_result)) : ?>
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
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" width="50" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="item-details">
                                <span><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</span>
                                <strong>$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                            </div>
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
            
            <div class="payment-method">
                <p>Payment Method</p>
                </div>
            
            <a href="checkout.php" class="place-order-link">
                <button class="place-order-btn" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?> >
                    Place An Order
                </button>
            </a>
        </aside>
    </div>

    <script src="js/script.js"></script>
</body>
</html>