<?php
require_once '../includes/auth.php';
checkLogin();

if ($_SESSION['username'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$users_file = __DIR__ . '/../includes/users.json';
$users = json_decode(file_get_contents($users_file), true) ?: [];

// 添加用户
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $new_user = trim($_POST['username']);
    $new_pass = $_POST['password'];
    $new_role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    if ($new_user && $new_pass) {
        foreach ($users as $u) {
            if ($u['username'] === $new_user) {
                $error = '用户名已存在';
                break;
            }
        }
        if (!isset($error)) {
            $users[] = [
                'username' => $new_user,
                'password' => md5($new_pass),
                'role' => $new_role
            ];
            file_put_contents($users_file, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            header('Location: users.php');
            exit;
        }
    } else {
        $error = '用户名和密码不能为空';
    }
}

// 删除用户
if (isset($_GET['del']) && $_GET['del'] !== 'admin') {
    $users = array_filter($users, function($u) {
        return $u['username'] !== $_GET['del'];
    });
    file_put_contents($users_file, json_encode(array_values($users), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: users.php');
    exit;
}

// 修改密码
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_pass'])) {
    $target = $_POST['target_user'];
    $new_pass = $_POST['new_password'];
    foreach ($users as &$u) {
        if ($u['username'] === $target && $target !== '') {
            $u['password'] = md5($new_pass);
            break;
        }
    }
    unset($u);
    file_put_contents($users_file, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $msg = '密码已修改';
    header('Location: users.php');
    exit;
}

// 修改角色
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $target = $_POST['target_user'];
    $new_role = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
    foreach ($users as &$u) {
        if ($u['username'] === $target && $target !== 'admin') {
            $u['role'] = $new_role;
            break;
        }
    }
    unset($u);
    file_put_contents($users_file, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>用户管理</title>
    <link rel="stylesheet" href="/assets/css/md3.css">
    <style>
        body { background: var(--md-surface); margin: 0; padding: 24px; }
        .container { max-width: 700px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 12px; border-bottom: 1px solid var(--md-outline); }
        th { background: var(--md-surface-variant); }
        .md3-button { margin-right: 8px; }
        .error { color: var(--md-error); margin-bottom: 12px; }
        .msg { color: var(--md-primary); margin-bottom: 12px; }
        form.inline { display:inline; }
        select { padding: 4px 8px; }
    </style>
</head>
<body>
<div class="container md3-card">
    <h2>用户管理</h2>
    <?php if (isset($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <?php if (isset($msg)): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <form method="post" style="margin-bottom:16px;">
        <input type="text" name="username" class="md3-text-field" placeholder="新用户名" required>
        <input type="password" name="password" class="md3-text-field" placeholder="密码" required>
        <select name="role" class="md3-text-field" style="width:auto;">
            <option value="user">普通用户</option>
            <option value="admin">管理员</option>
        </select>
        <button type="submit" name="add_user" class="md3-button">添加用户</button>
    </form>
    <table>
        <tr><th>用户名</th><th>类型</th><th>操作</th></tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td>
                    <?php if ($u['username'] === 'admin'): ?>
                        管理员
                    <?php else: ?>
                        <form method="post" class="inline" style="margin:0;">
                            <input type="hidden" name="target_user" value="<?= htmlspecialchars($u['username']) ?>">
                            <select name="new_role" onchange="this.form.submit()">
                                <option value="user" <?= (isset($u['role']) && $u['role'] === 'user') ? 'selected' : '' ?>>普通用户</option>
                                <option value="admin" <?= (isset($u['role']) && $u['role'] === 'admin') ? 'selected' : '' ?>>管理员</option>
                            </select>
                            <input type="hidden" name="change_role" value="1">
                        </form>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" class="inline" style="margin:0;">
                        <input type="hidden" name="target_user" value="<?= htmlspecialchars($u['username']) ?>">
                        <input type="password" name="new_password" placeholder="新密码" class="md3-text-field" style="width:100px;" required>
                        <button type="submit" name="change_pass" class="md3-button">改密</button>
                    </form>
                    <?php if ($u['username'] !== 'admin'): ?>
                        <a href="?del=<?= urlencode($u['username']) ?>" class="md3-button" style="background:var(--md-error)" onclick="return confirm('确定删除该用户？')">删除</a>
                    <?php else: ?>
                        <span style="color:#888;">保护</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <div style="margin-top:24px;">
        <a href="index.php" class="md3-button">返回管理首页</a>
    </div>
</div>
</body>
</html>
