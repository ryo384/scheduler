<?php
session_start();
if(isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    if(!isset($_SESSION['user'])) {
        $message = 'ログアウトしました';
    } else {
        $message = 'ログアウトに失敗しました。もう一度やり直してください';
    }
} else {
    $message = 'ログアウト済みです';
}
$_SESSION['messages'][] = $message;
header('Location: ../index.php');
exit;
?>








