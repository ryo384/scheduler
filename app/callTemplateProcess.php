<?php
// テンプレート呼び出し処理　フォームからテンプレ名を取得してDBから情報取得→jsにデータを返す
session_start();
header('Content-Type: application/json');

try {
    // --- 入力チェック ---
    if (!isset($_POST['templateName']) || empty($_POST['templateName'])) {
        $_SESSION['errors'][] = 'テンプレート名が指定されていません。';
        echo json_encode(['error' => 'テンプレート名が指定されていません。']);
        exit;
    }

    $templateName = $_POST['templateName'];
    $userId = $_SESSION['user']['id'] ?? null;

    if (!$userId) {
        $_SESSION['errors'][] = 'ユーザー情報が確認できません。';
        echo json_encode(['error' => 'ユーザー情報が確認できません。']);
        exit;
    }

    require_once 'dbConnect.php';

    // --- データ取得 ---
    $stmt = $pdo->prepare('SELECT * FROM template WHERE user_id = ? AND template_name = ?');
    $stmt->execute([$userId, $templateName]);
    $templateData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$templateData) {
        $_SESSION['errors'][] = '該当テンプレートが見つかりません。';
        echo json_encode(['error' => '該当テンプレートが見つかりません。']);
        exit;
    }

    // --- 正常データ構築 ---
    $useTemplateData = [];
    foreach ($templateData as $row) {
        $useTemplateData[$row['week_days']] = $row;
    }

    echo json_encode($useTemplateData);
    exit;

} catch (PDOException $e) {
    $_SESSION['errors'][] = 'エラーが発生しました';
    echo json_encode(['error' => 'エラーが発生しました。']);
    exit;
} catch (Exception $e) {
    $_SESSION['errors'][] = '予期せぬエラーが発生しました';
    echo json_encode(['error' => '予期せぬエラーが発生しました。']);
    exit;
}






