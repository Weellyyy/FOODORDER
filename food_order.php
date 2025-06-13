<?php
// food_order.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Food Order</title>
  <link rel="stylesheet" href="css/styles.css">
  <script defer src="js/script.js"></script>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">foodislice</div>
      <nav>
        <ul>
          <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><a href="index.php">Dashboard</a></li>
          <li class="<?= $current_page == 'food_order.php' ? 'active' : '' ?>"><a href="food_order.php">Food Order</a></li>
          <li class="<?= $current_page == 'feedback.php' ? 'active' : '' ?>"><a href="feedback.php">Feedback</a></li>
          <li class="<?= $current_page == 'order_history.php' ? 'active' : '' ?>"><a href="order_history.php">Order History</a></li>
          <li class="<?= $current_page == 'payment_details.php' ? 'active' : '' ?>"><a href="payment_details.php">Payment Details</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main content -->
    <main>
      <header>
        <input type="text" placeholder="Search your favorite food...">
      </header>

      <section>
        <h2>Food Order Page</h2>
        <p>Silakan pilih makanan favoritmu dari daftar yang tersedia di halaman utama.</p>
        <p>Halaman ini dapat dikembangkan untuk menampilkan daftar semua makanan dengan kategori dan fitur filtering.</p>
      </section>
    </main>

    <!-- Right Panel (Invoice atau detail keranjang) -->
    <aside>
      <h3>Invoice Preview</h3>
      <div class="invoice" id="invoice-items">
        <p class="empty-cart-message">Keranjang Anda masih kosong.</p>
      </div>
      <div class="payment-summary">
        <div class="summary-row">
          <span>Subtotal:</span><span id="sub-total">$0.00</span>
        </div>
        <div class="summary-row">
          <span>Tax (10%):</span><span id="tax">$0.00</span>
        </div>
        <div class="summary-row total">
          <span>Total:</span><span id="total-payment">$0.00</span>
        </div>
        <button class="place-order-btn" disabled>Place Order</button>
      </div>
    </aside>
  </div>
</body>
</html>
