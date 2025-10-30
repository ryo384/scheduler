<?php
$title = 'イベント';
$header1 = 'イベントページ';
$script = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'scripts/modal.js',
    'scripts/callTemplate.js',
    'scripts/choiceChange.js',
    'scripts/eventDelete.js',
    'scripts/mouseEvent.js'
];
require_once 'app/dbConnect.php';
require_once 'app/functions.php';

if(isset($_GET['token'])) {/* ページURLからトークンを取得 */
    $token = $_GET['token'];
    $pageInfo = $pdo -> prepare('SELECT * FROM events WHERE token = ?');/* トークンに一致する行（イベントテーブル）を取得 */
    $pageInfo -> execute([$token]);
    $eventTable = $pageInfo -> fetch(PDO::FETCH_ASSOC);/* キー配列に */
    if(!$eventTable) {
        $error = 'ページが存在しません';
        session_start();
        $_SESSION['errors'][] = $error;
        require_once 'app/systemMessage.php';
        exit;            
    }
} else {
    $error = 'ページが存在しません';
    session_start();
    $_SESSION['errors'][] = $error;
    require_once 'app/systemMessage.php';
    exit;
}

require_once 'header.php';
require_once 'app/eventProcess.php';
require_once 'app/systemMessage.php';

// テンプレート名を取得
$getTemplate = $pdo->prepare('SELECT template_name FROM template WHERE user_id=? GROUP BY template_name');
$getTemplate->execute([$userId]);
$templateName = $getTemplate->fetchAll(PDO::FETCH_COLUMN);


?>


            <section>
                <div class="d-flex justify-content-between">
                    <h2><?= h($eventTable['title']) ?></h2>
                    <?php if($ownerId === $userId): ?>
                    <button type="button" class="mb-2" id="eventDelete" data-token="<?= $token ?>">イベントを削除</button>
                    <?php endif; ?>
                </div>
                <p><?= h($ownerComments) ?></p>
                <button id="dialogOpen" >予定を入力する</button>
                <!-- テーブル -->
                <div class="scrollEvent table-responsive p-2 mt-3 bg-warning-subtle">
                    <table class="cssTable1 accordion mt-4 table table-striped">
                        <thead>
                            <!-- 項目名 -->
                            <tr>
                                <th class="cellHead-w-custom-ev">日付</th>
                                <th class="cell-w-custom-ev">時間</th>
                                <th class="cell-w-custom-ev">人数</th>
                                <?php
                                    if(!empty($usersName)) {
                                        foreach($usersName as $userName) {
                                            echo '<th class="cell-w-custom-ev">'. $userName['global_name']. '</th>';
                                        }
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody class="accordion-item">
                            <!-- 日付行 -->
                            <?php for($i = 0; $i < count($dateResult); $i++) { ?>
                                <tr class="accordion-header" id="heading<?=$i?>">
                                    <!-- 日付 -->
                                    <th class="accordion-button <?= h(array_key_first($dateResult[$i]['weekDay'])) ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?=$i?>" aria-expanded="true" aria-controls="collapse<?= $i ?>">
                                        <?= h($dateResult[$i]['responseDate']). '('. h(reset($dateResult[$i]['weekDay'])). ')' ?>
                                    </th>
                                    <td><?= h($dateResult[$i]['timeCount']) ?></td><!-- 時間 -->
                                    <td><?= h($dateResult[$i]['number']) ?></td><!-- 人数 -->
                                    <?php if(isset($dateResult[$i]['userResponses'])) { ?><!-- このif~else処理は本来要らない まぁ有っても良い -->
                                        <?php foreach($dateResult[$i]['userResponses'] as $row) { ?>
                                            <td><?= h($row) ?></td>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php foreach($joinUsersId as $id) { ?>
                                            <td>-</td>
                                        <?php } ?>
                                    <?php } ?>
                                    </tr>
                                    <!-- 各時間詳細 -->
                                    <tr id="collapse<?=$i?>" class="accordion-collapse collapse" aria-labelledby="heading<?=$i?>" data-bs-parent="#myAccordion">
                                    <td colspan="<?=(count($joinUsersId) + 3)?>" class="p-0">
                                    <table class="cssTable1 table table-striped">
                                    <tbody>
                                    <?php foreach($dateResult[$i]['timeDetail'] as $row) { ?>
                                        <tr>
                                        <th class="cellHead-w-custom-ev"></th><!-- 空セル -->
                                        <td><?= h($row['hour']) ?></td><!-- 時間 -->
                                        <td><?= h($row['number'][0]) + h($row['number'][1]) ?></td><!-- 人数 -->
                                        <?php if(isset($row['userResponses'])) { ?>
                                            <?php foreach($row['userResponses'] as $res) { ?>
                                                <td><?= h($res) ?></td>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php foreach($joinUsersId as $id) { ?>
                                                <td>-</td>
                                            <?php } ?>
                                        <?php } ?>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                    </table>
                                    </td>
                                    </tr>
                            <?php } ?>
                            <tr>
                                <th>コメント</th>
                                <td></td>
                                <td></td>
                                <!-- ユーザーコメント -->
                                    <?php foreach($usersComment as $comments): ?>
                                        <td><?= h($comments['user_comments'] ?? '') ?></td>
                                    <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- モーダル -->
            <div class="container-fluid row">
                <dialog class="col-11 col-md-9 h-100 p-0">
                    <h2 class="p-0 m-1 text-center">予定入力</h2>
                    <div class="d-flex justify-content-between align-items-center p-2 fixed-head">
                        <form class="d-flex align-items-end" id="templateForm" method="POST">
                            <label class="d-flex flex-column fs-6">
                                テンプレート呼び出し
                                <select name="templateName" class="pb-1 my-1">
                                    <?php foreach($templateName as $template): ?>
                                    <option value="<?= h($template ?? '') ?>"><?= h($template ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <button type="submit" id="templateCallBtn" class="h-50 mb-1 ms-2">適用</button>
                        </form>
                        <button id="dialogClose" class="py-2 px-3 m-3">キャンセル</button>
                    </div>
                    <form action="app/inputSchedule.php?eventId=<?= h($eventTable['id']) ?>" method="post"> 
                        <div class="scrollEvent table-responsive">
                            <table class="table table-striped table-fixed">
                                <thead>
                                    <tr>
                                        <th class="cellHead-w-custom fixed-col"></th><!-- 空セル -->
                                        <?php foreach($timeAll as $i => $time) { ?>
                                            <th class="hourHead hourChangeAll cell-w-custom-ev fixed-row" data-value="optionNum1" id="hour<?= h($time['hour']) ?>"><?= h($time['hour']) ?>時</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($dateAll as $i => $day) { ?>
                                    <tr>
                                        <th class="fixed-col dayChangeAll" id="<?= h(array_key_first($dateResult[$i]['weekDay'])). $i ?>" data-value="optionNum1"><?= h($day) ?><span class="dayHead <?= h(array_key_first($dateResult[$i]['weekDay'])) ?>">(<?= h(reset($dateResult[$i]['weekDay'])) ?>)</span></th>
                                        <?php foreach($timeAll as $time) { ?>
                                            <td class="p-2">
                                            <select name="schedule[<?= h($day) ?>][<?= h($time['hour']) ?>]" class="w-100 p-1 templateTarget <?= h(array_key_first($dateResult[$i]['weekDay'])). $i. ' hour'. h($time['hour']) ?>">
                                            <?php foreach($choices as $choice) { ?>
                                                <option><?= h($choice) ?></option>
                                            <?php } ?>
                                            </select>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="py-3 d-flex justify-content-center align-items-center">
                            <label for="userComments" class="me-1">コメント入力</label>
                            <input type="text" id="userComments" name="userComments" class="w-75">
                        </div>
                        <div class="d-flex justify-content-center my-4">
                            <input type="submit" value="入力内容を確定" class="py-2 px-4 fs-4">
                        </div>
                    </form>
                </dialog>
            </div><!-- ～～～モーダル -->

<?php require_once 'footer.php'; ?>




