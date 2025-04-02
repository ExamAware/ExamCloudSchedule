<?php
require_once '../includes/auth.php';
checkLogin();

$id = $_GET['id'] ?? '';
$config = ['examName' => '', 'message' => '', 'room' => '', 'examInfos' => []];

// Debug: 输出POST数据
error_log('POST data: ' . print_r($_POST, true));

// 保存逻辑
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['id']);
    
    $newConfig = [
        'examName' => $_POST['examName'] ?? '',
        'message' => $_POST['message'] ?? '',
        'room' => $_POST['room'] ?? '',
        'examInfos' => []
    ];
    
    // 验证并处理科目数据
    if (isset($_POST['subject']) && is_array($_POST['subject'])) {
        foreach ($_POST['subject'] as $index => $subject) {
            if (!empty($subject) && isset($_POST['start'][$index]) && isset($_POST['end'][$index])) {
                // 格式化时间为标准格式
                $startTime = date('Y-m-d\TH:i:s', strtotime($_POST['start'][$index]));
                $endTime = date('Y-m-d\TH:i:s', strtotime($_POST['end'][$index]));
                
                $newConfig['examInfos'][] = [
                    'name' => $subject,
                    'start' => $startTime,
                    'end' => $endTime
                ];
            }
        }
    }
    
    // Debug: 输出配置数据
    error_log('Config to save: ' . print_r($newConfig, true));
    
    // 保存配置
    $jsonData = json_encode($newConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($jsonData === false) {
        error_log('JSON encode error: ' . json_last_error_msg());
    } else {
        $saveResult = file_put_contents("../configs/{$id}.json", $jsonData);
        if ($saveResult === false) {
            error_log('File write failed for: ../configs/' . $id . '.json');
        } else {
            header('Location: index.php');
            exit;
        }
    }
}

// 加载现有配置
if (!empty($id) && file_exists("../configs/{$id}.json")) {
    $config = json_decode(file_get_contents("../configs/{$id}.json"), true);
    if ($config === null) {
        error_log('JSON decode error: ' . json_last_error_msg());
        $config = ['examName' => '', 'message' => '', 'room' => '', 'examInfos' => []];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>编辑配置</title>
    <link rel="stylesheet" href="/assets/css/md3.css">
    <style>
        body {
            background: var(--md-surface);
            margin: 0;
            padding: 24px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-wrapper {
            display: flex;
            gap: 24px;
        }

        .form-basic {
            width: 380px;
            flex-shrink: 0;
            height: fit-content;
            background: var(--md-surface);
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--md-elevation-1);
        }

        .form-basic .md3-text-field {
            width: 100%;
            box-sizing: border-box;
        }

        .form-basic .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-basic .md3-label {
            margin-bottom: 8px;
        }

        .form-subjects {
            flex-grow: 1;
            background: var(--md-surface);
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--md-elevation-1);
            min-width: 0; /* 防止flex子项溢出 */
        }

        .subject {
            background: var(--md-surface-variant);
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        h3 {
            margin-top: 0;
            color: var(--md-on-surface);
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="post" id="examForm" class="form-wrapper">
            <div class="form-basic">
                <h3>基本信息</h3>
                <div class="form-group">
                    <label class="md3-label">配置ID:</label>
                    <input type="text" name="id" class="md3-text-field" value="<?= htmlspecialchars($id) ?>" required>
                </div>
                <div class="form-group">
                    <label class="md3-label">考试名称:</label>
                    <input type="text" name="examName" class="md3-text-field" value="<?= htmlspecialchars($config['examName']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="md3-label">考试提示语:</label>
                    <input type="text" name="message" class="md3-text-field" value="<?= htmlspecialchars($config['message']) ?>">
                </div>
                <div class="form-group">
                    <label class="md3-label">考场号:</label>
                    <input type="text" name="room" class="md3-text-field" value="<?= htmlspecialchars($config['room'] ?? '') ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="md3-button">保存配置</button>
                </div>
            </div>

            <div class="form-subjects">
                <h3>考试科目安排</h3>
                <div id="subjects">
                    <?php if (!empty($config['examInfos'])): ?>
                        <?php foreach ($config['examInfos'] as $subject): ?>
                        <div class="subject">
                            <div class="form-group">
                                <label class="md3-label">科目名称:</label>
                                <input type="text" name="subject[]" class="md3-text-field" value="<?= htmlspecialchars($subject['name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="md3-label">开始时间:</label>
                                <input type="datetime-local" name="start[]" class="md3-text-field" value="<?= htmlspecialchars($subject['start']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="md3-label">结束时间:</label>
                                <input type="datetime-local" name="end[]" class="md3-text-field" value="<?= htmlspecialchars($subject['end']) ?>" required>
                            </div>
                            <button type="button" class="md3-button delete-btn" onclick="this.parentElement.remove()">删除</button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="form-actions">
                    <button type="button" class="md3-button" onclick="addSubject()">添加科目</button>
                </div>
            </div>
        </form>
    </div>
    
    <script>
    function addSubject() {
        const container = document.getElementById('subjects');
        const html = `
        <div class="subject">
            <div class="form-group">
                <label class="md3-label">科目名称:</label>
                <input type="text" name="subject[]" class="md3-text-field" required>
            </div>
            <div class="form-group">
                <label class="md3-label">开始时间:</label>
                <input type="datetime-local" name="start[]" class="md3-text-field" required>
            </div>
            <div class="form-group">
                <label class="md3-label">结束时间:</label>
                <input type="datetime-local" name="end[]" class="md3-text-field" required>
            </div>
            <button type="button" class="md3-button delete-btn" onclick="this.parentElement.remove()">删除</button>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
    }
    </script>
</body>
</html>