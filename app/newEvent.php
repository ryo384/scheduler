<!-- 新しくイベントページを作成する時にトークンを作成し、ユーザー入力データと共にDBへ保存 リダイレクトで個別ページへ-->
<?php
session_start();
require_once 'dbConnect.php';
require_once '../app/functions.php';



if(isset($_SESSION['user'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $startDate = trim($_POST['startDate'] ?? '');
        $endDate = trim($_POST['endDate'] ?? '');
        $startTime = trim($_POST['startTime'] ?? '');
        $endTime = trim($_POST['endTime'] ?? '');
        $ownerComments = trim($_POST['ownerComments'] ?? '');

        // 入力エラーチェック
        $errors = [];
        if($title === '') {
            $errors[] = 'タイトルは必須です';
        }
        if($startDate === '') {
            $errors[] = '開始日は必須です';
        }
        if($endDate === '') {
            $errors[] = '終了日は必須です';
        }
        if($startTime === '') {
            $errors[] = '開始時間は必須です';
        }
        if($endTime === '') {
            $errors[] = '終了時間は必須です';
        }

        if($errors) {
            foreach($errors as $error) {
                echo '<p style="color:red">'. h($error). '</p>';
            }
            // エラーが出たらホーム画面へ戻るようにする
            echo '<p><a href="../index.php">戻る</a></p>';
            exit;
        }

        // トークン生成
        $token = generateToken();
        // 作成日生成
        date_default_timezone_set('Asia/Tokyo');
        $createDate = date('Y-m-d');
        // オーナーユーザーの確認
        $ownerId = $_SESSION['user']['id'];
        // eventのidはnullで渡す
        $eventId = null;

        // DB保存
        $stmt = $pdo -> prepare("INSERT INTO events (id, token, title, owner_id, start_date, end_date, start_time, end_time, create_date, owner_comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt -> execute([$eventId, $token, $title, $ownerId, $startDate, $endDate, $startTime, $endTime, $createDate, $ownerComments]);

        // join_eventsに保存
        $getEventId = $pdo->lastInsertId();
        $joinEvent = $pdo -> prepare("INSERT INTO join_events (user_id, event_id) VALUES(?, ?)");
        $joinEvent -> execute([$ownerId, $getEventId]);


        // イベントページへリダイレクト
        header("Location: ../event.php?token=" . urlencode($token));
        exit;
    } else {
        // 不正アクセスはフォームへ戻す
        header("Location: ../index.php");
        exit;
    }
} else {
    echo '新規作成するにはログインしてからもう一度試してください';
    echo '<p><a href="../index.php">戻る</a></p>';
    exit;

}
