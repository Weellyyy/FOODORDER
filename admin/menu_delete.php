<?php
// admin/menu_delete.php
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

$stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

header('Location: menu_manage.php');
exit;
?>
