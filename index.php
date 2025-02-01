<?php
// exam_config/index.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>考试看板配置查询</title>
    <style>
        body { 
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .container { 
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #333;
        }
        input[type="text"] { 
            width: 100%; 
            padding: 10px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            margin-bottom: 10px;
        }
        button { 
            background: #6200ea; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            cursor: pointer; 
            border-radius: 4px; 
            transition: background-color 0.3s ease;
        }
        button:hover { 
            background: #3700b3; 
        }
        #result { 
            margin-top: 20px; 
            white-space: pre-wrap; 
            color: #333;
        }
        .error { 
            color: red; 
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            overflow: auto;
        }
        .string { color: green; }
        .number { color: darkorange; }
        .boolean { color: blue; }
        .null { color: magenta; }
        .key { color: red; }
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #333;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body>
    <button class="theme-toggle" onclick="toggleTheme()">切换主题</button>
    <div class="container">
        <h1>考试看板配置查询</h1>
        <div class="form-group">
            <label for="configId">输入配置ID：</label>
            <input type="text" id="configId" placeholder="例如：room301">
            <button onclick="loadConfig()">获取配置</button>
            <button id="enterButton" style="display:none;" onclick="enterSchedule()">进入</button>
        </div>
        
        <div id="result"></div>
        
        <hr>
        <p>管理员请前往 <a href="/admin/login.php">管理后台</a></p>
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
        window.location.href = `/ExamCloudSchedule/index.html?configId=${encodeURIComponent(configId)}`;
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

    function toggleTheme() {
        const body = document.body;
        const container = document.querySelector('.container');
        const themeToggle = document.querySelector('.theme-toggle');
        if (body.classList.contains('dark-theme')) {
            body.classList.remove('dark-theme');
            container.classList.remove('dark-theme');
            themeToggle.textContent = '切换主题';
        } else {
            body.classList.add('dark-theme');
            container.classList.add('dark-theme');
            themeToggle.textContent = '切换主题';
        }
    }
    </script>
    <style>
        .dark-theme {
            background: #333 !important;
            color: #e0e0e0 !important;
        }
        .dark-theme .container {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        .dark-theme .theme-toggle {
            background: #e0e0e0 !important;
            color: #333 !important;
        }
    </style>
</body>
</html>