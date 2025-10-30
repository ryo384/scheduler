<?php
session_start();

function validation() {
    $userName = trim($_POST['userName'] ?? '');
    $globalName = trim($_POST['globalName'] ?? '');
    $password = trim($_POST['userPassword'] ?? '');
    $passwordConfirm = trim($_POST['userPasswordConfirm'] ?? '');
    
    $inputValues = [
        'userName' => $userName, 
        'globalName' => $globalName, 
        'password' => $password, 
        'passwordConfirm' => $passwordConfirm
    ];
    
    $errorNames = [
        'userName' => 'ユーザーネーム',
        'globalName' => 'ニックネーム',
        'password' => 'パスワード',
        'passwordConfirm' => 'パスワード（確認用）'
    ];
    
    $errors = [];
    foreach($inputValues as $key => $value) {
        if ($value === '') {
            $errors[] = $errorNames[$key] . 'が未入力です';
        }
    }

    if(!preg_match('/^[A-Za-z0-9]{8,20}$/', $password)) {
        $errors[] = "パスワードは英数字のみで8文字以上20文字以内にしてください";
    }
    if($password !== $passwordConfirm) {
        $errors[] = 'パスワードが一致しません';
    }
    return [$inputValues, $errors];
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 会員登録
    if(isset($_POST['membershipRegist'])) {
        [$inputValues, $errors] = validation();

        // エラーがあれば入力ページに戻る
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../membershipRegist.php');
            exit;
        }

        // DBに保存
        require_once 'dbConnect.php';
        $regist = $pdo -> prepare("INSERT INTO users (user_name, global_name, user_password) VALUES (?, ?, ?)");
        $regist->execute([
            $inputValues['userName'],
            $inputValues['globalName'],
            // $inputValues['password']
            password_hash($inputValues['password'], PASSWORD_DEFAULT)
        ]);

        $message = '会員登録が完了しました。ログインしてください';
        $_SESSION['messages'][] = $message;
        header('Location: ../login.php');
        exit;



    } else if(isset($_POST['userDataEdit'])) {
    // 登録情報の変更
        if(empty($_SESSION['user']['id'])) {
            $error = 'ログインしてください';
            $_SESSION['errors'][] = $error;
            header('Location: ../login.php');
            exit;
        }
        
        $userId = $_SESSION['user']['id'];

        // [$inputValues, $errors] = validation();
        // パスワード再入力が違う場合と形式が違う場合のみエラーでいいのでは？
        $userName = trim($_POST['userName'] ?? '');
        $globalName = trim($_POST['globalName'] ?? '');
        $password = trim($_POST['userPassword'] ?? '');
        $passwordConfirm = trim($_POST['userPasswordConfirm'] ?? '');
        $inputValues = [
            'userName' => $userName, 
            'globalName' => $globalName, 
            'password' => $password, 
            'passwordConfirm' => $passwordConfirm
        ];
        if($userName === '') $errors[] = 'ユーザーネームを入力してください';
        if($globalName === '') $errors[] = 'ニックネームを入力してください';
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../userDataEdit.php');
            exit;
        }

        require_once 'dbConnect.php';

        // パスワード変更がある場合のみハッシュ生成
        if ($inputValues['password'] !== '') {
            if(!preg_match('/^[A-Za-z0-9]{8,20}$/', $password)) {
                $errors[] = "パスワードは英数字のみで8文字以上20文字以内にしてください";
            }
            if($password !== $passwordConfirm) {
                $errors[] = 'パスワードが一致しません';
            }
            // エラーがあれば入力ページに戻る
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: ../userDataEdit.php');
                exit;
            }

            $hashedPassword = password_hash($inputValues['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET user_name = ?, global_name = ?, user_password = ? WHERE id = ?";
            $params = [
                $inputValues['userName'],
                $inputValues['globalName'],
                $hashedPassword,
                $userId
            ];
        } else {
            $sql = "UPDATE users SET user_name = ?, global_name = ? WHERE id = ?";
            $params = [
                $inputValues['userName'],
                $inputValues['globalName'],
                $userId
            ];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['user'] = [
            'id' => $userId,
            'user_name' => $inputValues['userName'],
            'global_name' => $inputValues['globalName']
        ];
        $message = 'ユーザー情報を更新しました';
        $_SESSION['messages'][] = $message;
        header('Location: ../index.php');
        exit;


    }

}


