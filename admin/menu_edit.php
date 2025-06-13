<?php
// admin/menu_edit.php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

require_once('../config/db_connect.php');

if (!isset($_GET['id'])) {
    header('Location: menu_manage.php');
    exit;
}

$id = intval($_GET['id']);
$menu_query = $conn->prepare("SELECT * FROM foods WHERE id = ?");
$menu_query->bind_param('i', $id);
$menu_query->execute();
$menu_result = $menu_query->get_result();

if ($menu_result->num_rows === 0) {
    echo "<p>Data tidak ditemukan.</p>";
    exit;
}

$menu = $menu_result->fetch_assoc();
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category_id = intval($_POST['category_id']);
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE foods SET name=?, price=?, image_url=?, category_id=?, is_popular=? WHERE id=?");
    $stmt->bind_param('sdsiii', $name, $price, $image_url, $category_id, $is_popular, $id);
    $stmt->execute();

    header('Location: menu_manage.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu</title>
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
            background: #007bff;
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
    <form method="POST">
        <h2>Edit Menu</h2>
        <input type="text" name="name" value="<?= htmlspecialchars($menu['name']) ?>" required>
        <input type="number" step="0.01" name="price" value="<?= $menu['price'] ?>" required>
        <input type="text" name="image_url" value="<?= htmlspecialchars($menu['image_url']) ?>" required>

        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            <?php while ($cat = mysqli_fetch_assoc($categories_result)) : ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $menu['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label><input type="checkbox" name="is_popular" <?= $menu['is_popular'] ? 'checked' : '' ?>> Tandai sebagai populer</label><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>