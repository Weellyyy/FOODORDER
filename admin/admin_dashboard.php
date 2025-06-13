<?php
// admin/admin_dashboard.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .dashboard-header {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }
        .dashboard-header h2 {
            font-size: 1.8rem;
            margin-bottom: 6px;
        }
        .dashboard-stats {
            display: flex;
            gap: 20px;
        }
        .stat-card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .stat-card h3 {
            font-size: 1rem;
            color: #888;
            margin-bottom: 6px;
        }
        .stat-card strong {
            font-size: 1.4rem;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h1 class="logo">Admin Panel</h1>
        <nav>
            <ul>
                <li class="active"><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="menu_manage.php">Manajemen Menu</a></li>
                <li><a href="../index.php">Kembali ke Website</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <div class="search-bar">
                <input type="text" placeholder="Cari data...">
            </div>
            <div class="user-profile">
                <span><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
            </div>
        </header>

        <section class="dashboard-header">
            <h2>Selamat datang, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</h2>
            <p>Ini adalah halaman admin dashboard. Silakan kelola data pada panel navigasi di samping.</p>
        </section>

        <section class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Menu</h3>
                <strong>5</strong>
            </div>
            <div class="stat-card">
                <h3>Pesanan Hari Ini</h3>
                <strong>12</strong>
            </div>
            <div class="stat-card">
                <h3>User Aktif</h3>
                <strong>3</strong>
            </div>
        </section>
    </main>
</div>
</body>
</html>
