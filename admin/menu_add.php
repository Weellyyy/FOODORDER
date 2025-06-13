<?php
// admin/menu_add.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once('../config/db_connect.php');

// Ambil data kategori untuk dropdown
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category_id = intval($_POST['category_id']);
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO foods (name, price, image_url, category_id, is_popular) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sdsii', $name, $price, $image_url, $category_id, $is_popular);
    $stmt->execute();

    header('Location: menu_manage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        form {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        form input, form select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <h2>Tambah Menu Baru</h2>
        <input type="text" name="name" placeholder="Nama Makanan" required>
        <input type="number" step="0.01" name="price" placeholder="Harga" required>
        <input type="text" name="image_url" placeholder="URL Gambar" required>
        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories_result)) : ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <label><input type="checkbox" name="is_popular"> Tandai sebagai populer</label><br><br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>
