<?php
$title = 'ホーム';
$header1 = 'スケジュール調整';
$script[] = 'scripts/modal.js';
require_once 'app/dbConnect.php';
require_once 'header.php';
require_once 'app/systemMessage.php';

if(isset($_SESSION['user'])) {
    // 参加イベントを取得
    $getJoinEvent = $pdo->prepare('SELECT event_id FROM join_events WHERE user_id=?');
    $getJoinEvent->execute([$userId]);
    $joinEvents = $getJoinEvent->fetchAll(PDO::FETCH_COLUMN);
    // 参加イベントのデータを取得
    if($joinEvents) {
        $placeholders = implode(',', array_fill(0, count($joinEvents), '?'));
        $sql = "SELECT token, title, owner_id, create_date 
                FROM events 
                WHERE id IN ($placeholders)";
        $getJoinEventsData = $pdo->prepare($sql);
        $getJoinEventsData->execute($joinEvents);
        $joinEventsData = $getJoinEventsData->fetchAll(PDO::FETCH_ASSOC);
    }
    // テンプレート名を取得
    $getTemplate = $pdo->prepare('SELECT template_name FROM template WHERE user_id=? GROUP BY template_name');
    $getTemplate->execute([$userId]);
    $templateName = $getTemplate->fetchAll(PDO::FETCH_COLUMN);
}
?>

            <section class="mt-4 py-3 px-2 bg-warning-subtle">
                <h2>イベント</h2>
                <div class="mt-3 ms-2 fs-6"><button id="dialogOpen" type="btn" class="btn btn-secondary">新規イベント作成＋</button></div>
                <h3 class="mt-3">参加イベント一覧</h3>
                <div class="p-2">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>イベント名</th>
                                <th>作成日</th>
                                <th>オーナー</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($login && !empty($joinEventsData)): ?>
                            <?php foreach($joinEventsData as $data): ?>
                            <tr>
                                <th><a href="event.php?token=<?= h($data['token']) ?>"><?= h($data['title']) ?></a></th>
                                <td><?= h($data['create_date']) ?></td>
                                <td><?= $data['owner_id'] === $userId ? '自分': '×' ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- モーダル -->
            <div class="container-fluid">
                <dialog class="col-12 col-md-9 h-custom-sm position-relative">
                    <div class="d-flex">
                        <h2>新規イベント作成</h2>
                        <button id="dialogClose" class="py-1 px-3 ms-auto">キャンセル</button>
                    </div>
                    <form action="app/newEvent.php" method="post"> 
                        <div class="p-3">
                            <label for="title">イベント名</label>
                            <input id="title" name="title" class="col-12 col-sm-9 py-1 px-2" type="text">
                        </div>
                        <div class="p-3">
                            <label for="startDate">開始日</label>
                            <input id="startDate" name="startDate" class="col-9 col-sm-6 py-1 px-2" type="date">
                        </div>
                        <div class="p-3">
                            <label for="endDate">終了日</label>
                            <input id="endDate" name="endDate" class="col-9 col-sm-6 py-1 px-2" type="date">
                        </div>
                        <div class="p-3">
                            <label for="startTime">開始時間</label>
                            <select name="startTime" id="startTime" class="py-1 px-2">
                                <?php
                                    for($i = 0; $i <= 23; $i++) {
                                        echo '<option value="'. $i. '">'. $i. '時</option> ';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="p-3">
                            <label for="endTime">終了時間</label>
                            <select name="endTime" id="endTime" class="py-1 px-2">
                                <?php
                                    for($i = 0; $i <= 23; $i++) {
                                        echo '<option value="'. $i. '">'. $i. '時</option> ';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="p-3 d-flex flex-column">
                            <label for="ownerComments">コメント</label>
                            <textarea name="ownerComments" id="ownerComments"></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <input type="submit" value="作成する" class="py-2 px-4 fs-4">
                        </div>
                    </form>
                </dialog>
            </div><!-- ～～～モーダル -->
            <section class="mt-4 py-3 px-2 bg-warning-subtle">
                <h2>登録情報</h2>
                <ul class="mx-2 p-0 list-unstyled">
                    <li>
                        <dl class="row m-0">
                            <dt class="col-4 ps-0">ユーザー名</dt>
                            <dd class="col-8 ps-0"><?= $login === true ? h($userName) : '' ?></dd>
                            <dt class="col-4 ps-0">ニックネーム</dt>
                            <dd class="col-8 ps-0"><?= $login === true ? h($globalName) : '' ?></dd>
                        </dl>
                        <a href="userDataEdit.php" class="btn btn-secondary">ユーザー情報の変更</a>
                    </li>
                    <li class="mt-4">
                        <div class="fs-5">テンプレートの作成・変更</div>
                        <a href="template.php" class="btn btn-secondary">テンプレート新規作成＋</a>
                        <?php if($login && !empty($templateName)): ?>
                        <form action="template.php" method="POST" class="mt-2">
                            <select name="templateEdit" id="" class="w-50">
                                <?php foreach($templateName as $name): ?>
                                    <option value="<?= h($name) ?>"><?= h($name) ?></option>
                                <?php endforeach; ?>
                                <input type="submit" value="変更">
                            </select>
                        </form>
                        <?php endif; ?>
                    </li>
                </ul>
            </section>

<?php require_once 'footer.php'; ?>
