<?php
session_start();
require_once 'app/functions.php';

// 今いるページのURIを保存（遷移元の確認用）
$_SESSION['previousPage'] = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['user'])) {
    $login = true;
} else {
    $login = false;
}
// ユーザーデータを取得
$userData = $_SESSION['user'] ?? '';
$userId = $userData['id'] ?? '';
$userName = $userData['userName'] ?? '';
$globalName = $userData['globalName'] ?? '';

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ? 'スケジュール調整'. ' | '. $title : 'スケジュール調整') ?></title>
    <!-- ブートストラップ -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/event.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="flex-shrink-0">
        <div class="d-flex justify-content-end py-2 bg-primary-subtle">
            <nav class="d-flex align-items-center">
                <ul class="m-0 d-flex list-unstyled">
                    <li class="mx-3">
                        <a href="index.php">トップページ</a>
                    </li>
                    <?php if($login): ?> 
                        <li class="mx-3">
                            <?= h($globalName).'様' ?? '' ?>
                        </li>
                        <li class="mx-3">
                            <a href="app/logoutProcess.php">ログアウト</a>
                        </li>
                        <?php else: ?>
                        <li class="mx-3">
                            <a href="membershipRegist.php">会員登録</a>
                        </li>                        
                        <li class="mx-3">
                            <a href="login.php">ログイン</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <main class="mb-3 flex-grow-1">
            <div class="my-3">
                <h1><?= h($header1) ?></h1>
            </div>
