<?php
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    die(json_encode(['error' => '缺少ID参数']));
}

$id = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['id']);
$file = "configs/{$id}.json";

if (!file_exists($file)) {
    http_response_code(404);
    die(json_encode(['error' => '配置不存在']));
}

echo file_get_contents($file);
?>