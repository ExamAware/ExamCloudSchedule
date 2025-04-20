<?php
require_once '../includes/auth.php';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (verifyUser($username, $password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = '用户名或密码错误';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <link rel="stylesheet" href="/assets/css/md3.css">
    <style>
        body {
            background: var(--md-surface);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .login-container {
            background: var(--md-surface);
            padding: 32px;
            border-radius: 28px;
            box-shadow: var(--md-elevation-1);
            width: 320px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .login-container .md3-text-field {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 0;
        }

        .login-container .md3-button {
            margin-top: 8px;
        }

        .login-container h2 {
            color: var(--md-on-surface);
            margin: 0 0 24px;
            text-align: center;
        }

        .login-container input {
            width: 100%;
            margin-bottom: 16px;
        }

        .error {
            color: var(--md-error);
            text-align: center;
            margin-bottom: 16px;
        }

        .back-button {
            position: absolute;
            top: 16px;
            left: 16px;
            text-decoration: none;
            padding: 8px 16px;
            background: var(--md-primary);
            color: var(--md-on-primary);
            border-radius: 4px;
            box-shadow: var(--md-elevation-1);
            font-size: 14px;
        }

        .back-button:hover {
            background: var(--md-primary-hover);
        }
    </style>
</head>
<body>
    <a href="../" class="back-button md3-button">返回</a>
    <div class="login-container md3-card">
        <h2>登录</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <label class="md3-label">用户名:</label>
            <input type="text" name="username" class="md3-text-field" required>
            
            <label class="md3-label">密码:</label>
            <input type="password" name="password" class="md3-text-field" required>
            
            <button type="submit" class="md3-button">登录</button>
        </form>
    </div>
</body>
</html>