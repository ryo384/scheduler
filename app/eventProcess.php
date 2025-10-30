    <?php
    // 予定入力用の選択肢
    $choices = ['〇', '×', '△', '-'];

    // オーナーのコメントを取得
    $ownerId = $eventTable['owner_id'];
    $ownerComments = $eventTable['owner_comments'] ?? '';

    // 開始日と終了日のデータを取得し、dateAllに格納
    $startDate = new DateTime($eventTable['start_date']);
    $endDate = new DateTime($eventTable['end_date']);
    $endDate -> modify('+1 day');
    $dayInterval = new DateInterval('P1D');
    $dayPeriod = new DatePeriod($startDate, $dayInterval, $endDate);
    $dateAll = [];
    $week = ['日', '月', '火', '水', '木', '金', '土'];
    $weekDayAll = [];
    foreach($dayPeriod as $date) {
        $dateFormat = $date -> format('Y-m-d');
        $dateAll[] = $dateFormat;
        $weekDayEn = date('D', strtotime($dateFormat));
        $weekDayJa = $week[date('w', strtotime($dateFormat))];
        $weekDayAll[] = [$weekDayEn => $weekDayJa];
    }
    // 開始時間と終了時間を取得しtimeAllに格納
    $startTime = new DateTime('2000-01-01 '. $eventTable['start_time']. ':00:00');
    $endTime = new DateTime('2000-01-01 '. $eventTable['end_time']. ':00:00');
    if($startTime >= $endTime ) {
        $endTime ->modify('+1 day');
    }
    $endTime -> modify('+1 second');
    $timeInterval = new DateInterval('PT1H');
    $timePeriod = new DatePeriod($startTime, $timeInterval, $endTime);
    $timeAll = [];
    foreach($timePeriod as $hour) {
        $timeAll[] = ['hour' => $hour -> format('G')];
    }
    // 最終的に使用するdateResultに日付と曜日を入れてひな型を作る。
    $dateResult = [];
    foreach($dateAll as $i => $day) {
        $dateResult[] = ['responseDate' => $day];
        $dateResult[$i]['weekDay'] = $weekDayAll[$i];
    }
    // dateResultに入れるtimeDetailを作る。各インデックスに開始～終了の時刻配列を格納
    $timeDetail = [];
    for($i = 0; $i < count($dateResult); $i++) {
        $timeDetail[] = [$dateAll[$i] => $timeAll];
    }

    // user_responsesテーブルからデータを取得
    $eventId = $eventTable['id'];/* イベントIDを取得 */
    $responseMatchData = $pdo -> prepare('SELECT * FROM user_responses WHERE event_id = ?');
    $responseMatchData -> execute([$eventId]);
    $responseTable = $responseMatchData -> fetchAll(PDO::FETCH_ASSOC);

    for($i = 0; $i < count($timeDetail); $i++) {
        $dayKey = array_keys($timeDetail[$i])[0];/* 2025-01-01 */
        $dayMatch = [];        
        foreach($responseTable as $row) {
            if($row['response_date'] === $dayKey) {
                $dayMatch[] = $row;/* 日付にマッチするuser_responsesの行を取得(取得数＝ユーザー数) */
            }
        }
        foreach($timeDetail[$i] as $hour) {/* $hour[[20], [21], [22], [23], [0], [1]] */
            $j = 0;
            foreach($hour as $time) {/* $time = 20 */
                $hourKey = 'h'. $time['hour'];
                $res = [];
                foreach($dayMatch as $response) {
                    $res[] = $response[$hourKey];
                    $timeDetail[$i][$dayKey][$j]['userResponses'][] = $response[$hourKey];
                }
                $yesCount = count(array_filter($res, function($value) {
                    return $value === '〇';
                }));
                $timeDetail[$i][$dayKey][$j]['number'][] = $yesCount;
                $maybeCount = count(array_filter($res, function($value) {
                    return $value === '△';
                }));
                $timeDetail[$i][$dayKey][$j]['number'][] = $maybeCount;
                $noCount = count(array_filter($res, function($value) {
                    return $value === '×';
                }));
                $timeDetail[$i][$dayKey][$j]['number'][] = $noCount;
                $j++;
            }
            // ここまでで$timeDetailに '日付key'=>[0=>[hour=>値, user_responses=>[値,値], number=>[2,0]],1=>...]となっている。
        }
    }


    // user毎のデータを作る
    $userIds = [];
    foreach($responseTable as $response) {
        $userIds[] = $response['user_id'];
    }
    $joinUsersId = array_values(array_unique($userIds));/* 被りIDを消す */
    $usersResponseAll = [];/* ここの値が(ユーザー数分の)配列、その中に（日付入力分の）配列にならないといけない */
    $countUser = count($joinUsersId);
    for($i = 0; $i < $countUser; $i++) {
        $id = $joinUsersId[$i];
        $countData = count($responseTable) / $countUser;
        for($j = 0; $j < $countData; $j++) {
            $row = $responseTable[$j + $countData * $i];
            if($row['user_id'] === $id) {
                $usersResponseAll[$i][$j] = $row;
            }
        }
    }

    $usersResponsePerDay = []; /* ユーザー毎の日別に〇×を入れた配列 */
    $useHours = array_column($timeAll, 'hour');
    foreach($usersResponseAll as $i => $user) {
        foreach($user as $j => $day) {
            $filterDay = [];
            foreach($useHours as $hour) {
                $key = 'h'. $hour;
                $filterDay[$key] = $day[$key];
            }
            $usersResponsePerDay[$i][$j] = $filterDay;
        }
    }

    // ユーザーの名前を取得
    if(!empty($joinUsersId)) {
        $placeholders = rtrim(str_repeat('?,', count($joinUsersId)), ','); 
        $getUsersName = $pdo -> prepare("SELECT id, global_name FROM users WHERE id IN($placeholders)");
        $getUsersName -> execute($joinUsersId);
        $usersName = $getUsersName -> fetchAll(PDO::FETCH_ASSOC);
    }
    
    // join_eventsからコメントを取得
    $getJoinEvents = $pdo -> prepare('SELECT user_id, user_comments FROM join_events WHERE event_id = ?');
    $getJoinEvents -> execute([$eventId]);
    $getUsersComment = $getJoinEvents -> fetchAll(PDO::FETCH_ASSOC);
    $usersComment = $usersName ?? [];
    foreach($getUsersComment as $idAndComment) {
        foreach($usersComment as &$idAndName) {
            if($idAndComment['user_id'] === $idAndName['id']) {
                $idAndName['user_comments'] = $idAndComment['user_comments'];
                break;
            }
        }
        unset($idAndName);
    }
    
    //時間の表記を変換する関数
    function convertHours(array $hourKeys): string {
        // "h" を取り除き整数化
        $hours = array_map(fn($v) => (int)str_replace('h', '', $v), $hourKeys);
        $n = count($hours);
        if ($n === 0) return '';
        $result = [];
        $start = $hours[0];
        $prev = $hours[0];
        for ($i = 1; $i <= $n; $i++) {
            $current = $i < $n ? $hours[$i] : null;
            $isContinuous = $current !== null && (($prev + 1) % 24 === $current);
            if ($isContinuous) {
                $prev = $current;
            } else {
                // 範囲を追加
                if ($start == $prev) {
                    $result[] = (string)$start;
                } else {
                    $result[] = $start . '-' . $prev;
                }
                if ($current !== null) {
                    $start = $current;
                    $prev = $current;
                }
            }
        }
        return implode(',', $result);
    }

    //$dateResultのuser_responsesを作って入れる
    $usersResponse = [];
    $hoursCount = count($useHours);
    foreach($usersResponsePerDay as $i => $user) {
        $judge = '';
        foreach($user as $j => $day) {
            // 〇の数をカウント
            $yesCount = count(array_filter($day, function($value) {
                return $value === '〇';
            }));
            // 〇が時間の個数と同じなら〇判定で終了
            if($yesCount === $hoursCount) {
                $judge = '〇';
                $usersResponse[$i][$j] = $judge;
                continue;
            }

            // 違うなら×の数をカウント
            $noCount = count(array_filter($day, function($value) {
                return $value === '×';
            }));
            // ×が時間の個数と同じなら×判定で終了
            if($noCount === $hoursCount) {
                $judge = '×';
                $usersResponse[$i][$j] = $judge;
                continue;
            }

            // 全部〇、全部×でないなら〇が0かどうかチェック
            if($yesCount !== 0) {
                // 〇があるなら〇の時間を取得
                $filter = array_filter($day, function($value) {
                    return $value === '〇';
                });
                $getKey = array_keys($filter);
                $judge = convertHours($getKey);
                $usersResponse[$i][$j] = $judge;
                continue;
            }

            // △があるかチェック
            $maybeCount = count(array_filter($day, function($value) {
                return $value === '△';
            }));
            if($maybeCount !== 0) {
                $judge = '△';
                $usersResponse[$i][$j] = $judge;
                continue;
            }

            // -の場合
            $judge ='-';
            $usersResponse[$i][$j] = $judge;
        }
    }
    foreach($usersResponse as $userKey => $user) {
        foreach($user as $dayKey => $result) {
            $dateResult[$dayKey]['userResponses'][] = $result;
        }
    }


    // dateResultに入れるnumberとtimeCountを作る
    $maxNumber = [];
    foreach($timeDetail as $dayData) {
        foreach($dayData as $date => $hours) {
            $numbers = [];
            foreach($hours as $hourData) {
                $numbers[] = $hourData['number'][0] + $hourData['number'][1];
            }
            $maxNumber[] = max($numbers);
        }
    }

    $timeCount = [];
    foreach($timeDetail as $i => $dayData) {
        foreach($dayData as $date => $hours) {
            $hourCounts = [];
            foreach($hours as $hourData) {
                if($hourData['number'][0] + $hourData['number'][1] === $maxNumber[$i] && $maxNumber[$i] !== 0) {
                    $hourCounts[] = $hourData['hour'];
                }
            }
            $timeCount[] = convertHours($hourCounts);
        }
    }

    // $dateResultに$timeDetail,$maxNumber,$timeCountのデータを入れていく
    foreach($dateResult as $i => $dayData) {
        $getKey = array_keys($timeDetail[$i])[0];
        $dateResult[$i]['number'] = $maxNumber[$i];
        $dateResult[$i]['timeCount'] = $timeCount[$i];
        $dateResult[$i]['timeDetail'] = $timeDetail[$i][$getKey];
    }
    
    // echo ('<pre>');
    // var_dump($dateResult);
    // echo ('</pre>');



