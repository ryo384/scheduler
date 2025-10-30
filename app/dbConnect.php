<?php
require_once 'loadEnv.php';

// .envファイルの絶対パス（public_htmlより上の階層）
loadEnv($_SERVER['DOCUMENT_ROOT'] . '/../env/scheduler/.env');


$dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4';
$user = getenv('DB_USER');
$password = getenv('DB_PASS');

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}
