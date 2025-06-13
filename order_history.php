<?php
session_start();
require_once 'config/db_connect.php';

// Ambil semua data pesanan, diurutkan dari yang paling baru
$query = "SELECT * FROM orders ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pesanan - Foodislice</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Style tambahan khusus untuk halaman ini */
        .main-content-page {
            padding: 25px;
            background-color: #fff;
            border-radius: 15px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .order-table th {
            background-color: #f9f9f9;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        /* Anda bisa menambahkan class status lain seperti .status-completed, .status-cancelled, dll. */
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h1 class="logo">foodislice</h1>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-utensils"></i> Food Order</a></li>
                    <li><a href="#"><i class="fas fa-comment-dots"></i> Feedback</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Message</a></li>
                    <li class="active"><a href="order_history.php"><i class="fas fa-history"></i> Order History</a></li>
                    <li><a href="#"><i class="fas fa-credit-card"></i> Payment Details</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Customization</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content-page">
            <h2>Riwayat Pesanan Anda</h2>
            <p>Di sini Anda dapat melihat semua pesanan yang pernah Anda buat.</p>

            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Nama Pemesan</th>
                        <th>Total Pembayaran</th>
                        <th>Status</th>
                        <th>Tanggal Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td>$<?= number_format($order['total_price'], 2) ?></td>
                                <td>
                                    <span class="status-pending"><?= htmlspecialchars($order['order_status']) ?></span>
                                </td>
                                <td><?= date('d M Y, H:i', strtotime($order['order_date'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Anda belum memiliki riwayat pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>