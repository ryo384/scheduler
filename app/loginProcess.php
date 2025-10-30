<?php
session_start();
require_once 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    unset($_SESSION['user']);
    $sql = $pdo -> prepare('SELECT * FROM users WHERE user_name = ?');
    $sql -> execute([$_REQUEST['userName']]);
    $user = $sql->fetchAll(PDO::FETCH_ASSOC);
    $password = $_POST['userPassword'] ?? '';

    if ($user && password_verify($password, $user[0]['user_password'])) {
        // 認証成功
        foreach($user as $row) {
            $_SESSION['user'] = [
                'id' => $row['id'],
                'userName' => $row['user_name'],
                'globalName' => $row['global_name'],
            ];
        }
        // セッションに保存されたか確認
        if(isset($_SESSION['user'])) {
            header("Location: ../index.php");
            exit;
        } else {
            $error = 'ログインエラー：ページを戻ってやり直してください';
            $_SESSION['errors'][] = $error;
            header("Location: ../index.php");
            exit;
        }
      
    } else {
        // 認証失敗
        $error = 'ユーザー名またはパスワードが正しくありません。';
        $_SESSION['errors'][] = $error;
        header("Location: ../index.php");
        exit;
    }
    
    
} else {
    // 不正アクセスはフォームへ戻す
    header("Location: ../index.php");
    exit;
}

