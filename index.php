<?php
// exam_config/index.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>考试看板配置查询</title>
    <link rel="stylesheet" href="/assets/css/md3.css">
    <style>
        body { 
            font-family: Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--md-surface);
            color: var(--md-on-surface);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container { 
            background: var(--md-surface);
            padding: 32px;
            border-radius: 28px;
            box-shadow: var(--md-elevation-1);
            max-width: 600px;
            width: 100%;
        }

        h1 {
            color: var(--md-on-surface);
            font-size: 24px;
            margin-bottom: 24px;
        }

        #result { 
            margin-top: 24px;
            padding: 16px;
            background: var(--md-surface-variant);
            border-radius: 16px;
            max-height: 400px;
            overflow: auto;
        }

        #result pre {
            margin: 0;
        }

        /* 定制滚动条样式 */
        #result::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        #result::-webkit-scrollbar-track {
            background: var(--md-surface-variant);
            border-radius: 4px;
        }

        #result::-webkit-scrollbar-thumb {
            background: var(--md-outline);
            border-radius: 4px;
        }

        #result::-webkit-scrollbar-thumb:hover {
            background: var(--md-primary);
        }

        .error { 
            color: var(--md-error);
        }

        pre {
            background: var(--md-surface-variant);
            padding: 10px;
            border-radius: 4px;
            overflow: auto;
        }
        .string { color: green; }
        .number { color: darkorange; }
        .boolean { color: blue; }
        .null { color: magenta; }
        .key { color: red; }
    </style>
</head>
<body>
    <div class="container md3-card">
        <h1>考试看板配置查询</h1>
        <div class="form-group">
            <label class="md3-label" for="configId">输入配置ID：</label>
            <input type="text" id="configId" class="md3-text-field" placeholder="例如：room301">
            <button class="md3-button" onclick="loadConfig()">获取配置</button>
            <button id="enterButton" class="md3-button" style="display:none;" onclick="enterSchedule()">进入</button>
        </div>
        
        <div id="result"></div>
        
        <hr>
        <p>管理员请前往 <a href="/admin/login.php" class="md3-button">管理后台</a></p>
    </div>

    <script>
    function loadConfig() {
        const configId = document.getElementById('configId').value.trim();
        const resultDiv = document.getElementById('result');
        const enterButton = document.getElementById('enterButton');
        resultDiv.innerHTML = '加载中...';
        
        if(!configId) {
            resultDiv.innerHTML = '<span class="error">请输入配置ID</span>';
            enterButton.style.display = 'none';
            return;
        }

        fetch(`/get_config.php?id=${encodeURIComponent(configId)}`)
            .then(response => {
                if(!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                resultDiv.innerHTML = `<pre>${syntaxHighlight(JSON.stringify(data, null, 4))}</pre>`;
                enterButton.style.display = 'inline-block';
            })
            .catch(error => {
                resultDiv.innerHTML = `<span class="error">错误：${error.error || '获取配置失败'}</span>`;
                enterButton.style.display = 'none';
            });
    }

    function enterSchedule() {
        const configId = document.getElementById('configId').value.trim();
        window.location.href = `/present/index.html?configId=${encodeURIComponent(configId)}`;
    }

    // JSON高亮显示
    function syntaxHighlight(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, 
            function (match) {
                let cls = 'number';
                if (/^"/.test(match)) {
                    cls = /:$/.test(match) ? 'key' : 'string';
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
    }
    </script>
</body>
</html>