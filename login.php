<?php
$title = 'ログイン';
$header1 = 'ログイン';
require_once 'header.php';
require_once 'app/systemMessage.php';

?>
        <div class="mx-auto">
            <div class="bg-warning-subtle py-3">
                <form action="app/loginProcess.php" method="post" class="col-12 col-md-6 mx-auto">
                    <div class="p-3">
                        <label for="userName">ユーザーネーム</label>
                        <input type="text" id="userName" name="userName" class="w-100">
                    </div>
                    <div class="p-3">
                        <label for="userPassword">パスワード</label>
                        <input type="password" id="userPassword" name="userPassword" class="w-100">
                    </div>
                    <div class="py-3 d-flex justify-content-center">
                        <input type="submit" value="ログイン" class="py-1 px-3">
                    </div>
                </form>
            </div>
        </div>

<?php require_once 'footer.php'; ?>