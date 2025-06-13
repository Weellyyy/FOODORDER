<?php
// admin/menu_manage.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once('../config/db_connect.php');

$query = "SELECT f.*, c.name AS category_name FROM foods f LEFT JOIN categories c ON f.category_id = c.id ORDER BY f.id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Menu</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .main-content {
            padding: 20px;
        }
        .menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .menu-header h2 {
            font-size: 1.6rem;
            font-weight: 600;
        }
        .menu-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .menu-table th, .menu-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
        }
        .menu-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #333;
        }
        .menu-table td img {
            border-radius: 6px;
        }
        .menu-actions {
            display: flex;
            gap: 8px;
        }
        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-edit {
            background-color: #007bff;
            color: #fff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-delete:hover {
            background-color: #b52a37;
        }
        .btn-icon i {
            font-style: normal;
        }
        .add-btn {
            background: #28a745;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: background 0.2s;
        }
        .add-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <h1 class="logo">Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li class="active"><a href="menu_manage.php">Manajemen Menu</a></li>
                <li><a href="admin_logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <div class="menu-header">
            <h2>Manajemen Menu</h2>
            <a href="menu_add.php" class="add-btn">+ Tambah Menu</a>
        </div>

        <table class="menu-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Populer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($row['image_url']) ?>" width="50" alt=""></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td><?= $row['is_popular'] ? 'Ya' : 'Tidak' ?></td>
                    <td class="menu-actions">
                        <a href="menu_edit.php?id=<?= $row['id'] ?>" class="btn-icon btn-edit"><i>✏️</i> Edit</a>
                        <a href="menu_delete.php?id=<?= $row['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Yakin ingin menghapus menu ini?')"><i>🗑️</i> Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>