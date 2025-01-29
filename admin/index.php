<?php
require_once '../includes/auth.php';
checkLogin();

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
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f2f2f2; 
            margin: 0; 
            padding: 20px; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            margin: 0; 
        }
        .add-btn-container {
            margin-bottom: 20px;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            background-color: #fff; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            border-radius: 8px; 
            overflow: hidden; 
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #f5f5f5; 
        }
        .actions a { 
            margin-right: 10px; 
            color: #007bff; 
            text-decoration: none; 
        }
        .actions a:hover { 
            text-decoration: underline; 
        }
        .logout { 
            color: #dc3545; 
        }
        .add-btn { 
            background: #28a745; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 4px; 
            text-decoration: none; 
        }
        .add-btn:hover { 
            background: #218838; 
        }
        .no-configs { 
            text-align: center; 
            padding: 20px; 
            color: #888; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>考试配置管理 <small>当前用户：<?= $_SESSION['username'] ?></small></h1>
        <a href="login.php?action=logout" class="logout">退出登录</a>
    </div>

    <div class="add-btn-container">
        <a href="edit.php" class="add-btn">新建配置</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>配置ID</th>
                <th>最后修改时间</th>
                <th>文件大小</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($configs)): ?>
                <tr>
                    <td colspan="4" class="no-configs">暂无配置文件</td>
                </tr>
            <?php else: ?>
                <?php foreach($configs as $config): ?>
                <tr>
                    <td><?= htmlspecialchars($config['id']) ?></td>
                    <td><?= $config['mtime'] ?></td>
                    <td><?= round($config['size']/1024, 2) ?> KB</td>
                    <td class="actions">
                        <a href="edit.php?id=<?= urlencode($config['id']) ?>">编辑</a>
                        <a href="#" onclick="confirmDelete('<?= $config['id'] ?>')">删除</a>
                        <a href="../get_config.php?id=<?= urlencode($config['id']) ?>" target="_blank">预览</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    function confirmDelete(id) {
        if(confirm(`确定要删除 ${id} 的配置吗？`)) {
            window.location = `delete.php?id=${encodeURIComponent(id)}`;
        }
    }
    </script>
</body>
</html>