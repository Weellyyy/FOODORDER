<?php
// user_auth.php (Login dan Logout)
session_start();
require_once 'config/db_connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: user_auth.php');
    exit;
}

// Handle login
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            header('Location: index.php');
            exit;
        }
    }

    $errors[] = 'Username atau password salah';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .auth-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .auth-container input[type="text"], .auth-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .auth-container button {
            width: 100%;
            padding: 10px;
            background-color: #ff5722;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
        .auth-container .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .auth-container .logout-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #555;
        }
    </style>
</head>
<body>
<div class="auth-container">
    <h2>User Login</h2>
    <?php if ($errors): ?><div class="error"><?php echo implode('<br>', $errors); ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username atau Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p style="text-align:center; margin-top:10px;">Belum punya akun? <a href="user_register.php">Daftar di sini</a></p>
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
        <p style="text-align:center; margin-top:20px;">Login sebagai <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
        <p style="text-align:center;"><a href="?logout" class="logout-btn">Logout</a></p>
    <?php endif; ?>
</div>
</body>
</html>
