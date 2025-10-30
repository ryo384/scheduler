<?php
session_start();

$token = $_GET['token'] ?? null;

if (empty($token)) {
    $error = 'イベントが見つかりませんでした';
    $_SESSION['errors'][] = $error;
    header('Location: ../index.php');
    exit;
}

require_once 'dbConnect.php';

$deleteEvent = $pdo->prepare('DELETE FROM events WHERE token=?');
$deleteEvent->execute([$token]);


// 削除されたかの確認　削除するのは1件なので、されていれば1、いなければ0が返ってくる
$deletedCount = $deleteEvent->rowCount();
if($deletedCount > 0) {
    // 成功
    $message = 'イベントを削除しました';
    $_SESSION['messages'][] = $message;
    header('Location: ../index.php');
    exit;
} else {
    // 失敗
    $error = 'イベントの削除に失敗しました';
    $_SESSION['errors'][] = $error;
    header('Location: ../index.php');
    exit;
}




