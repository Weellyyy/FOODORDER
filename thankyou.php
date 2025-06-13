<?php
$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil!</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .thankyou-container { text-align: center; padding: 50px; }
        .thankyou-container h1 { color: var(--primary-color); }
        .thankyou-container a { display: inline-block; margin-top: 20px; padding: 12px 25px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="thankyou-container">
        <h1>Terima Kasih!</h1>
        <p>Pesanan Anda telah kami terima dan akan segera diproses.</p>
        <?php if ($order_id): ?>
            <p>Nomor Pesanan Anda adalah: <strong>#<?= $order_id ?></strong></p>
        <?php endif; ?>
        <p>Anda bisa melacak status pesanan di halaman 'Order History'.</p>
        <a href="index.php">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>