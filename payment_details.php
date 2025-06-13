<?php
session_start();
require_once 'config/db_connect.php';
$current_page = 'payment_details.php';

// Handle upload bukti
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['payment_proof']['name']);
    $target_file = $upload_dir . time() . "_" . $file_name;

    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
        $_SESSION['payment_proof'] = $target_file;
        $_SESSION['payment_status'] = 'uploaded';
    }
}

// Fungsi perhitungan total
function calculate_totals() {
    $sub_total = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $sub_total += $item['price'] * $item['quantity'];
    }
    $tax = $sub_total * 0.10;
    $total = $sub_total + $tax;
    return ['sub_total' => $sub_total, 'tax' => $tax, 'total' => $total];
}
$totals = calculate_totals();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Payment Details - Welly Restaurant</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .proof-preview img { max-width: 250px; margin-top: 10px; }
    .qris-section img { max-width: 200px; }
  </style>
</head>
<body>
<div class="container">
  <!-- SIDEBAR -->
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

  <!-- MAIN -->
  <main class="main-content">
    <header>
      <div class="search-bar"><input type="text" placeholder="Search something..."></div>
      <div class="user-profile"><span>Wellyyy</span></div>
    </header>

    <section>
      <h2>Payment Details</h2>

      <?php if (empty($_SESSION['cart'])): ?>
        <p class="empty-cart-message">Tidak ada pesanan saat ini.</p>
      <?php else: ?>
        <div class="summary-row"><strong>Sub Total:</strong> $<?= number_format($totals['sub_total'], 2) ?></div>
        <div class="summary-row"><strong>Tax (10%):</strong> $<?= number_format($totals['tax'], 2) ?></div>
        <div class="summary-row total"><strong>Total Payment:</strong> $<?= number_format($totals['total'], 2) ?></div>

        <hr>

        <h3>QRIS (Scan untuk bayar)</h3>
        <div class="qris-section">
          <img src="images/qris.g" alt="QRIS Payment">
        </div>

        <hr>

        <h3>Upload Bukti Transfer</h3>
        <?php if (!empty($_SESSION['payment_proof'])): ?>
          <p><strong>Status:</strong> Sudah diupload</p>
          <div class="proof-preview">
            <img src="<?= htmlspecialchars($_SESSION['payment_proof']) ?>" alt="Bukti Transfer">
          </div>
        <?php else: ?>
          <form method="post" enctype="multipart/form-data">
            <input type="file" name="payment_proof" accept="image/*" required>
            <button type="submit">Upload Bukti</button>
          </form>
        <?php endif; ?>

        <hr>
        <a href="checkout.php" class="place-order-link">
          <button class="place-order-btn">Lanjut ke Checkout</button>
        </a>
      <?php endif; ?>
    </section>
  </main>

  <!-- SIDEBAR INVOICE -->
  <aside class="invoice">
    <h2>Invoice</h2>
    <div class="invoice-items">
      <?php if (!empty($_SESSION['cart'])): ?>
        <?php foreach ($_SESSION['cart'] as $item): ?>
          <div class="invoice-item">
            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="item-details">
              <p class="item-name"><?= htmlspecialchars($item['name']) ?></p>
              <p class="item-quantity">x <?= $item['quantity'] ?></p>
            </div>
            <strong class="item-price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="empty-cart-message">Tidak ada item dalam keranjang.</p>
      <?php endif; ?>
    </div>
    <div class="payment-summary">
      <div class="summary-row"><span>Sub Total</span><span>$<?= number_format($totals['sub_total'], 2) ?></span></div>
      <div class="summary-row"><span>Tax</span><span>$<?= number_format($totals['tax'], 2) ?></span></div>
      <div class="summary-row total"><span>Total</span><span>$<?= number_format($totals['total'], 2) ?></span></div>
    </div>
  </aside>
</div>
</body>
</html>
