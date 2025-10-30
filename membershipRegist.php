<?php
$title = '会員登録';
$header1 = '会員登録';
require_once 'app/functions.php';
require_once 'header.php';
require_once 'app/systemMessage.php';


?>

    <div class="mx-auto">
        <div class="bg-warning-subtle py-3">
            <form action="app/membershipRegistProcess.php" method="post" class="col-12 col-md-6 mx-auto">
                <div class="p-3">
                    <label for="userName" class="d-flex align-center">ユーザーネーム<span class="badge bg-danger mx-2 my-auto">必須</span></label>
                    <input type="text" id="userName" name="userName" class="w-100" required maxlength="10">
                </div>
                <div class="p-3">
                    <label for="globalName" class="d-flex align-center">ニックネーム<span class="badge bg-danger mx-2 my-auto">必須</span></label>
                    <input type="text" id="globalName" name="globalName" class="w-100" required maxlength="10">
                </div>
                <div class="p-3">
                    <label for="userPassword" class="d-flex align-center">パスワード<span class="badge bg-danger mx-2 my-auto">必須</span></label>
                    <input type="password" id="userPassword" name="userPassword" class="w-100" required minlength="8" maxlength="20" pattern="[A-Za-z0-9]+">
                </div>
                <div class="p-3">
                    <label for="userPasswordConfirm" class="d-flex align-center">パスワード（再入力）<span class="badge bg-danger mx-2 my-auto">必須</span></label>
                    <input type="password" id="userPasswordConfirm" name="userPasswordConfirm" class="w-100" required minlength="8" maxlength="20" pattern="[A-Za-z0-9]+">
                </div>
                <div class="py-3 d-flex justify-content-center">
                    <input type="submit" value="登録" class="py-1 px-3" name="membershipRegist">
                </div>
            </form>
        </div>
    </div>

<?php require_once 'footer.php'; ?>







