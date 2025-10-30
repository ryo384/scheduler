<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleData = $_POST ?? '';
    $userComments = $_POST['userComments'] ?? '';
    $eventId = $_GET['eventId'];
    $userId = $_SESSION['user']['id'];

    // エラーがあれば中断
    if(!$eventId || empty($scheduleData)) {
        $error = '選択肢を取得できませんでした。もう一度試してください';
        $_SESSION['errors'][] = $error;
        $previousPage = $_SESSION['previousPage'];
        header("Location: $previousPage");
        exit;
    }

    // 処理続行 user_responsesテーブルへ保存
    require_once 'dbConnect.php';
    $allHours = range(0, 23);
    foreach($scheduleData['schedule'] as $date => $hours) {
        $hourData = [];
        foreach($allHours as $h) {
            $colName = "h{$h}";
            $hourData[$colName] = $hours[$h] ?? null;
        }
        
        $hourColumns = implode(',' , array_keys($hourData));/* 'h0, h1, h2...' */
        $valuePlaceholders = implode(',', array_map(fn($c) => ":$c", array_keys($hourData)));/* ':h0,:h1,:h2...' */
        $updateAssignments = implode(',', array_map(fn($c) => "$c = :$c", array_keys($hourData)));/* 'h0 = :h0,h1 = :h1,h2 = :h2...' */

        $sql = "
            INSERT INTO user_responses (event_id, user_id, response_date, {$hourColumns})
            VALUES (:event_id, :user_id, :response_date, {$valuePlaceholders})
            ON DUPLICATE KEY UPDATE {$updateAssignments}
        ";

        $stmt = $pdo->prepare($sql);
        $params = array_merge(
            [
                'event_id' => $eventId,
                'user_id' => $userId,
                'response_date' => $date,
            ],
            $hourData
        );
        $stmt->execute($params);
    }
    // join_eventsテーブルにコメントを保存
    $saveComments = $pdo->prepare("
        INSERT INTO join_events (user_id, event_id, user_comments)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
            user_comments = VALUES(user_comments)
    ");
    $saveComments->execute([$userId, $eventId, $userComments]);


} else {
    $error = '不正なアクセスです。最初からやり直してください。';
    $_SESSION['errors'][] = $error;
    $previousPage = $_SESSION['previousPage'];
    header("Location: $previousPage");
    exit;
}

// 正常に処理終了
$message = '予定を入力しました';
$_SESSION['messages'][] = $message;

$returnPage = $_SESSION['previousPage'];
header("Location: $returnPage");





