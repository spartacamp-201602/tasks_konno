<?php
// データベース接続
function connectDb() {
    try {
        return new PDO(DSN, DB_USER, DB_PASSWORD);
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

// エスケープ処理
function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}


// 指定した日付フォーマットに当てはまるかを判定
function checkDateFormat($date) {
    if ($date == '') {
        return False;
    } else {
        return $date === date("Y/m/d", strtotime($date));
    }
}

// 残り時間を計算する
function calcLimitTime($due_date) {
    $due_date = new DateTIme($due_date);
    $now_date = new DateTime('now');
    $diff = $due_date->diff($now_date);

    if ($due_date < $now_date) {
        return false;   // 期日が過去である場合
    } else if ($interval->y) {
        return false;   // 1年以上先である場合
    } else if ($interval->m) {
        return false;   // 1ヶ月以上先である場合
    } else if ($interval->d) {
        return $interval->d + 1;  // 数日後である場合
    } else {
        return 1;
    }
}


