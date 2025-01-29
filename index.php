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
            max-width: 800px; 
            margin: 20px auto; 
            padding: 20px; 
            font-family: Arial, sans-serif; 
            background-color: #f2f2f2; 
        }
        .container { 
            border: 1px solid #ddd; 
            padding: 20px; 
            border-radius: 8px; 
            background-color: #fff; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
        }
        input[type="text"] { 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        button { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            cursor: pointer; 
            border-radius: 4px; 
        }
        button:hover { 
            background: #0056b3; 
        }
        #result { 
            margin-top: 20px; 
            white-space: pre-wrap; 
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
    </style>
</head>
<body>
    <div class="container">
        <h1>考试看板配置查询</h1>
        <div class="form-group">
            <label for="configId">输入配置ID：</label>
            <input type="text" id="configId" placeholder="例如：room301">
            <button onclick="loadConfig()">获取配置</button>
        </div>
        
        <div id="result"></div>
        
        <hr>
        <p>管理员请前往 <a href="/admin/login.php">管理后台</a></p>
    </div>

    <script>
    function loadConfig() {
        const configId = document.getElementById('configId').value.trim();
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = '加载中...';
        
        if(!configId) {
            resultDiv.innerHTML = '<span class="error">请输入配置ID</span>';
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
            })
            .catch(error => {
                resultDiv.innerHTML = `<span class="error">错误：${error.error || '获取配置失败'}</span>`;
            });
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