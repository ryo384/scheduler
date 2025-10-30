<?php
$title = '登録情報の変更';
$header1 = '登録情報の変更';
require_once 'app/functions.php';
require_once 'header.php';
require_once 'app/systemMessage.php';


if(isset($_SESSION['user'])) {
    $login = true;
    // ユーザーデータを取得
    $userData = $_SESSION['user'];
    $userId = $userData['id'];
    $userName = $userData['userName'];
    $globalName = $userData['globalName'];
} else {
    $error = 'ログインしてください';
    $_SESSION['errors'][] = $error;
    header('Location: login.php');
    exit;
}

?>

    <div class="container mx-auto">

        <div class="w-75 mx-auto bg-warning-subtle py-3">
            <form action="app/membershipRegistProcess.php" method="post">
                <div class="p-3">
                    <label for="userName" class="d-flex align-center">ユーザーネーム</label>
                    <input type="text" id="userName" name="userName" class="w-100" value="<?= h($userName ?? '') ?>">
                </div>
                <div class="p-3">
                    <label for="globalName" class="d-flex align-center">ニックネーム</label>
                    <input type="text" id="globalName" name="globalName" class="w-100" value="<?= h($globalName ?? '') ?>">
                </div>
                <div class="p-3">
                    <label for="userPassword" class="d-flex align-center">新しいパスワード</label>
                    <input type="password" id="userPassword" name="userPassword" class="w-100">
                </div>
                <div class="p-3">
                    <label for="userPasswordConfirm" class="d-flex align-center">新しいパスワード（再入力）</label>
                    <input type="password" id="userPasswordConfirm" name="userPasswordConfirm" class="w-100">
                </div>
                <div class="py-3 d-flex justify-content-center">
                    <input type="submit" value="登録" class="py-1 px-3" name="userDataEdit">
                </div>
            </form>
        </div>
    </div>


<?php require_once 'footer.php' ?>




