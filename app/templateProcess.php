<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = $_POST ?? '';
    $userId = $_SESSION['user']['id'];
    $templateName = $inputData['templateName'];
    $templateData = $inputData['template'];

    // エラー処理
    if(empty($templateData) || empty($templateName)) {
        $error = '情報を取得できませんでした。もう一度試してください';
        $_SESSION['errors'][] = $error;
        $previousPage = $_SESSION['previousPage'];
        header("Location: $previousPage");
        exit;
    }

    // 処理続行 DBへ保存
    require_once 'dbConnect.php';
    $allHours = range(0, 23);
    foreach($templateData as $day => $hours) {
        $hourData = [];
        foreach($allHours as $h) {
            $colName = "h{$h}";
            $hourData[$colName] = $hours[$h] ?? null;
        }
        
        $hourColumns = implode(',' , array_keys($hourData));/* 'h0, h1, h2...' */
        $valuePlaceholders = implode(',', array_map(fn($c) => ":$c", array_keys($hourData)));/* ':h0,:h1,:h2...' */
        $updateAssignments = implode(',', array_map(fn($c) => "$c = :$c", array_keys($hourData)));/* 'h0 = :h0,h1 = :h1,h2 = :h2...' */

        $sql = "
            INSERT INTO template (user_id, template_name, week_days, {$hourColumns})
            VALUES (:user_id, :template_name, :week_days, {$valuePlaceholders})
            ON DUPLICATE KEY UPDATE {$updateAssignments}
        ";
        $stmt = $pdo->prepare($sql);
        $params = array_merge(
            [
                'user_id' => $userId,
                'template_name' => $templateName,
                'week_days' => $day,
            ],
            $hourData
        );
        $stmt->execute($params);
    }
    $message = 'テンプレートを保存しました';
    $_SESSION['messages'][] = $message;
    header("Location: ../index.php");
    exit;
} else {
    $error = '不正なアクセスです。最初からやり直してください。';
    $_SESSION['errors'][] = $error;
    $previousPage = $_SESSION['previousPage'];
    header("Location: $previousPage");
    exit;
}
