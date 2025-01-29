<?php
session_start();

$users_file = __DIR__.'/users.json';

function checkLogin() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function verifyUser($username, $password) {
    global $users_file;
    $users = json_decode(file_get_contents($users_file), true);
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === md5($password)) {
            return true;
        }
    }
    return false;
}
?>