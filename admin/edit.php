<?php
require_once '../includes/auth.php';
checkLogin();

$id = $_GET['id'] ?? '';
$config = ['examName' => '', 'message' => '', 'examInfos' => []];

// 保存逻辑
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['id']);
    
    $newConfig = [
        'examName' => $_POST['examName'],
        'message' => $_POST['message'],
        'room' => $_POST['room'],
        'examInfos' => []
    ];
    
    foreach ($_POST['subject'] as $index => $subject) {
        $newConfig['examInfos'][] = [
            'name' => $subject,
            'start' => $_POST['start'][$index],
            'end' => $_POST['end'][$index]
        ];
    }
    
    file_put_contents("../configs/{$id}.json", json_encode($newConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header('Location: index.php');
    exit;
}

// 加载现有配置
if (!empty($id) && file_exists("../configs/{$id}.json")) {
    $config = json_decode(file_get_contents("../configs/{$id}.json"), true);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>编辑配置</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
        }
        .container h3 {
            margin-top: 0;
        }
        .container div {
            margin-bottom: 15px;
        }
        .container label {
            display: block;
            margin-bottom: 5px;
        }
        .container input[type="text"],
        .container input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .container button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #45a049;
        }
        .subject {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .subject button {
            background-color: #dc3545;
            margin-top: 10px;
        }
        .subject button:hover {
            background-color: #c82333;
        }
    </style>
    <script>
    function addSubject() {
        const container = document.getElementById('subjects');
        const index = Date.now();
        const html = `
        <div class="subject">
            <div>
                <label>科目名称:</label>
                <input type="text" name="subject[]" required>
            </div>
            <div>
                <label>开始时间:</label>
                <input type="datetime-local" name="start[]" required>
            </div>
            <div>
                <label>结束时间:</label>
                <input type="datetime-local" name="end[]" required>
            </div>
            <button type="button" onclick="this.parentElement.remove()">删除</button>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
    </script>
</head>
<body>
    <div class="container">
        <form method="post">
            <div>
                <label>配置ID:</label>
                <input type="text" name="id" value="<?= htmlspecialchars($id) ?>" required>
            </div>
            <div>
                <label>考试名称:</label>
                <input type="text" name="examName" value="<?= htmlspecialchars($config['examName']) ?>" required>
            </div>
            <div>
                <label>考试提示语:</label>
                <input type="text" name="message" value="<?= htmlspecialchars($config['message']) ?>">
            </div>
            <div>
                <label>考场号:</label>
                <input type="text" name="room" value="<?= htmlspecialchars($config['room'] ?? '') ?>" required>
            </div>
            
            <h3>考试科目安排</h3>
            <div id="subjects">
                <?php foreach ($config['examInfos'] as $subject): ?>
                <div class="subject">
                    <div>
                        <label>科目名称:</label>
                        <input type="text" name="subject[]" value="<?= htmlspecialchars($subject['name']) ?>" required>
                    </div>
                    <div>
                        <label>开始时间:</label>
                        <input type="datetime-local" name="start[]" value="<?= str_replace(' ', 'T', $subject['start']) ?>" required>
                    </div>
                    <div>
                        <label>结束时间:</label>
                        <input type="datetime-local" name="end[]" value="<?= str_replace(' ', 'T', $subject['end']) ?>" required>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()">删除</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addSubject()">添加科目</button>
            <hr>
            <button type="submit">保存配置</button>
        </form>
    </div>
</body>
</html>