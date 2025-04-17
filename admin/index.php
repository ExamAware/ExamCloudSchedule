<?php
require_once '../includes/auth.php';
checkLogin();
$role = getUserRole($_SESSION['username']);

// 获取所有配置文件列表
$configs = [];
foreach (scandir('../configs') as $file) {
    if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'json') {
        $id = pathinfo($file, PATHINFO_FILENAME);
        $configs[$id] = [
            'id' => $id,
            'mtime' => date("Y-m-d H:i:s", filemtime("../configs/$file")),
            'size' => filesize("../configs/$file")
        ];
    }
}

// 按创建时间排序
uksort($configs, function($a, $b) {
    return filemtime("../configs/$b.json") - filemtime("../configs/$a.json");
});
?>
<!DOCTYPE html>
<html>
<head>
    <title>配置管理后台</title>
    <link rel="stylesheet" href="/assets/css/md3.css">
    <style>
        body { 
            font-family: Roboto, sans-serif;
            background: var(--md-surface);
            margin: 0; 
            padding: 24px; 
        }

        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
            padding: 0 24px;
        }

        .content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .card {
            background: var(--md-surface);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--md-elevation-1);
            transition: box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .card:hover {
            box-shadow: var(--md-elevation-2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-title {
            font-size: 20px;
            font-weight: 500;
            color: var(--md-on-surface);
            margin: 0;
        }

        .card-meta {
            color: var(--md-on-surface-variant);
            font-size: 14px;
        }

        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--md-outline);
        }

        .card-actions a {
            text-decoration: none; /* 添加这一行 */
        }

        .add-card {
            border: 2px dashed var(--md-outline);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 200px;
            text-decoration: none; /* 添加这一行 */
        }

        .add-card:hover {
            border-color: var(--md-primary);
            background: var(--md-primary-container);
        }

        .add-icon {
            font-size: 48px;
            color: var(--md-primary);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>考试配置管理 <small>当前用户：<?= $_SESSION['username'] ?></small></h1>
        <div>
            <?php if ($role === 'admin'): ?>
                <a href="users.php" class="md3-button">用户管理</a>
            <?php endif; ?>
            <a href="login.php?action=logout" class="md3-button">退出登录</a>
        </div>
    </div>

    <div class="content">
        <div class="grid">
            <a href="edit.php" class="card add-card">
                <div class="add-icon">+</div>
                <span class="card-title">新建配置</span>
            </a>
            <?php if(!empty($configs)): ?>
                <?php foreach($configs as $config): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= htmlspecialchars($config['id']) ?></h3>
                    </div>
                    <div class="card-meta">
                        最后修改：<?= $config['mtime'] ?><br>
                        文件大小：<?= round($config['size']/1024, 2) ?> KB
                    </div>
                    <div class="card-actions">
                        <a href="edit.php?id=<?= urlencode($config['id']) ?>" class="md3-button">编辑</a>
                        <a href="../get_config.php?id=<?= urlencode($config['id']) ?>" class="md3-button" target="_blank">查看</a>
                        <button class="md3-button" style="background:var(--md-error)" onclick="confirmDelete('<?= $config['id'] ?>')">删除</button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function confirmDelete(id) {
        if(confirm(`确定要删除 ${id} 的配置吗？`)) {
            window.location = `delete.php?id=${encodeURIComponent(id)}`;
        }
    }
    </script>
</body>
</html>