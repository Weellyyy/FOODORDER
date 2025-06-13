<?php
// user_register.php
session_start();
require_once 'config/db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $errors[] = 'Password dan konfirmasi tidak cocok';
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $hashed);

        if ($stmt->execute()) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Gagal mendaftar. Mungkin email sudah terdaftar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .auth-container { max-width: 400px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .auth-container h2 { text-align: center; margin-bottom: 20px; }
        .auth-container input[type="text"], .auth-container input[type="email"], .auth-container input[type="password"] {
            width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc;
        }
        .auth-container button { width: 100%; padding: 10px; background-color: #ff5722; border: none; color: white; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .auth-container .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="auth-container">
    <h2>Register Akun</h2>
    <?php if ($errors): ?><div class="error"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
        <button type="submit">Daftar</button>
    </form>
    <p style="text-align:center; margin-top:10px;">Sudah punya akun? <a href="user_login.php">Login di sini</a></p>
</div>
</body>
</html>
