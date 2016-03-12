<?php

require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];
$dbh = connectDb();

$sql = "select * from tasks where id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":id", $id);
$stmt->execute();

// 結果の取得
$post = $stmt->fetch(PDO::FETCH_ASSOC);

// タスクの編集
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 受け取ったデータ
    $title = $_POST['title'];
    $content = $_POST['content'];
    $due_date = $_POST['due_date'];

    // エラーチェック用の配列
    $errors = array();

    // バリデーション
    if ($title == '') {
        $errors['title'] = 'タスク名を入力してください';
    }

    if ($title == $post['title'] && $content == $post['content'] && $due_date == $post['due_date']) {
        $errors['title'] = 'タスク名,メモともに変更点がありません';
    }

    if (!$due_date == '') {
        if (checkDateFormat($due_date)) {
            $date = date_create($_POST['due_date']);
            $due_date = date_format($date, 'Y-m-d');
        } else {
            $errors['due_date'] = '日付の形式が正しくありません';
        }
    } else {
        $due_date = null;
    }

    // エラーが１つもなければレコードを更新
    if (empty($errors)) {
        $dbh = connectDb();

        $sql = "update tasks set title = :title, content = :content, due_date = :due_date, updated_at = now() ";
        $sql.= "where id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":due_date", $due_date);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>編集画面</title>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" >
</head>
<body>
    <h2>タスクの編集</h2>
    <p>
        <form action="" method="POST">
            <input type="text" name="title" value="<?php echo h($post['title']) ?>">
            <textarea  name='content' value="<?php echo h($post['content']) ?>" cols="50" rows="1"></textarea>
            <input type="text" name='due_date' id="datepicker" value="<?php echo h($post['due_date']) ?>">
            <input type="submit" value="編集">
            <span style="color:red"><?php echo h($errors['title']) ?></span>
        </form>
        <a href="revival.php?id=<?php echo h($task['id']) ?>" class="task-check-box">
            [削除]
        </a>
    </p>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script src="main.js"></script>
</body>
</html>