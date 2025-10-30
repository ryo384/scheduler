<?php
$title = 'テンプレート編集';
$header1 = 'テンプレート編集';
$script[] = 'scripts/choiceChange.js';
require_once 'app/dbConnect.php';
require_once 'app/functions.php';
require_once 'header.php';
require_once 'app/systemMessage.php';

if(!isset($_SESSION['user'])) {
    $error = 'ログインしてください';
    $_SESSION['errors'][] = $error;
    header('Location: login.php');
    exit;
}

// テンプレート’変更’の場合　データを取得
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editTemplate = $_POST ?? '';
    $userId = $_SESSION['user']['id'] ?? '';
    if($editTemplate && $userId) {
        $getTemplateData = $pdo->prepare('SELECT * FROM template WHERE user_id=? AND template_name=?');
        $getTemplateData->execute([$userId, $editTemplate['templateEdit']]);
        $templateData = $getTemplateData->fetchAll(PDO::FETCH_ASSOC);
        $templateName = $templateData[0]['template_name'];
        $useTemplateData = [];
        foreach($templateData as $row) {
            $useTemplateData[$row['week_days']] = $row;
        }
    }
}

$allHours = range(0, 23);
$weekdays = [
    'Sun' => '日',
    'Mon' => '月',
    'Tue' => '火',
    'Wed' => '水',
    'Thu' => '木',
    'Fri' => '金',
    'Sat' => '土',
];
$choices = ['〇', '×', '△', '-'];

?>

        <div class="col-12 col-md-10 col-lg-8 h-100 mx-auto pb-5">
            <p>※曜日、時間のボタンを押すことでその列/行を一括で変更できます。</p>
            <form action="app/templateProcess.php" method="post"> 
                <label class="pb-4 w-100">
                    テンプレート名
                    <input type="text" name="templateName" class="w-100 my-2" value="<?= $templateName ?? '' ?>">
                </label>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th><!-- 空セル -->
                            <?php foreach($weekdays as $key => $day) { ?>
                                <th><button type="button" data-value="optionNum1" id="<?= h($key) ?>" class="dayChangeAll"><?= h($day) ?></button></th>
                                <?php } ?>
                            </tr>
                    </thead>
                    <tbody>
                    <?php foreach($allHours as $hour) { ?>
                        <tr>
                            <th><button type="button" data-value="optionNum1" id="hour<?= h($hour) ?>" class="hourChangeAll"><?= h($hour) ?>時</button></th>
                            <?php foreach($weekdays as $key => $day) { ?>
                                <td class="p-1">
                                <select name="template[<?= h($key) ?>][<?= h($hour) ?>]" class="<?= h($key) ?> hour<?= h($hour) ?> w-100 p-1">
                                <?php foreach($choices as $choice) { ?>
                                    <option <?= (!empty($useTemplateData) && $choice === $useTemplateData[$key]['h' . $hour]) ? 'selected' : '' ?>>
                                        <?= h($choice) ?>
                                    </option>
                                <?php } ?>
                                </select>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <input type="submit" value="入力内容を確定" class="py-2 px-4 fs-4">
                </div>
            </form>
        </div>


<?php require_once 'footer.php' ?>







