<?php
require_once '../includes/auth.php';
checkLogin();

$id = $_GET['id'] ?? '';

if (!empty($id)) {
    $filePath = "../configs/{$id}.json";
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

header('Location: index.php');
exit;
?>
