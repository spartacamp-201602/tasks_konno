<?php

// 設定ファイルと関数ファイルを読み込む
require_once('config.php');
require_once('functions.php');

// DBに接続
$dbh = connectDb();

// タスクを取得
$sql = "select * from tasks order by due_date asc";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 完了済みタスク数を取得する
$sql = "select count(*) from tasks where status = 'done'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$doneCountArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
$doneCount = $doneCountArray[0]['count(*)'];

// 新規タスクの追加
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // フォームに入力されたデータの受け取り
    $title = $_POST['title'];
    $content = $_POST['content'];
    $due_date = $_POST['due_date'];

    // エラーチェック用の配列
    $errors = array();

    // バリデーション
    if ($title == '') {
        $errors['title'] = 'タスク名を入力して下さい';
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

    // if (empty($errors)) でも良い
    if (count($errors) == 0) {
        $dbh = connectDb();

        $sql = "insert into tasks ";
        $sql.= "(title, content, due_date,created_at, updated_at) values ";
        $sql.= "(:title, :content, :due_date, now(), now());";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam('content', $content);
        // 日付型のフォーマットを 2000/01/01 ==> 2000-01-01 へ
        $stmt->bindParam('due_date', $due_date);
        $stmt->execute();

        // index.php
        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タスク管理</title>
    <link rel="stylesheet" href="css/foundation/foundation.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/cupertino/jquery-ui.css" >
</head>
<body>
    <div class="row">
        <div class="columns large-2 left-side-nav">

            <div class="left-side-nav-top">
                <div class="todo-serch-btn-wrapper">
                    <a class="todo-search-btn" href="#"><i class="fa fa-search"></i></a>
                </div>
                <div class="todo-search-wrapper">
                    <form class="todo-search-form">
                        <input class="todo-search-input" type="text" name="todo_search" placeholder="Todoを検索する...">
                    </form>
                </div>
            </div>

            <div class="left-side-nav-bottom">
                <ul class="tabs vertical" id="example-vert-tabs" data-tabs>
                    <li class="tabs-title is-active"><a href="#panel1v" aria-selected="true">進行中タスク</a></li>
                    <li class="tabs-title"><a href="#panel2v">完了済みタスク</a></li>
                </ul>
            </div>
        </div>

        <div class="columns large-10 right-main-sec">

            <div class="right-toolbar">
                <span>ToDoリスト</span>
            </div>

            <div class="right-main-tasks">
                <form class="todo-add-form" action="" method="post">
                    <div class="todo-add-sec">
                        <div class="todo-add-box todo-add-box-input">
                            <input class="todo-add-input" type="text" name="title" placeholder="ToDoを追加…">
                            <div class="todo-add-date">
                                <input class="todo-add-date-input" type="text" name='due_date' id="datepicker" placeholder="期日を設定…">
                            </div>
                        </div>
                        <div class="todo-add-box todo-add-box-submit">
                            <input class="todo-add-submit" type="submit" value="追加">
                        </div>
                    </div>
                    <!--textarea  name='content' placeholder="メモを追加…" cols="50" rows="1"></textarea>-->
                </form>
                <?php foreach ((array)$errors as $error) : ?>
                    <ul class="todo-add-alert-ul">
                        <div class="callout alert">
                            <li>
                                <?php echo $error ?>
                            </li>
                        </div>
                    </ul>
                <?php endforeach ?>
                <div class="tabs-content vertical" data-tabs-content="example-vert-tabs">
                    <div class="tabs-panel is-active" id="panel1v">
                        <ul class="tasks">
                            <?php foreach ($tasks as $task) : ?>
                                <?php if ($task['status'] == 'notyet') : ?>
                                    <li class="tasks-list">
                                        <div class="task-item">
                                            <div class="task-item-checkbox">
                                                <a href="done.php?id=<?php echo h($task['id']) ?>" class="task-check-box">
                                                </a>
                                            </div>
                                            <div class="task-item-content">
                                                <a href="delete.php?id=<?php echo h($task['id']) ?>"></a>
                                                <a href="edit.php?id=<?php echo h($task['id']) ?>">
                                                    <?php echo h($task['title']); ?>
                                                    <?php if (!$task['content'] == '') : ?>
                                                        <?php echo ':'.h($task['content']); ?>
                                                    <?php endif ?>
                                                </a>
                                            </div>
                                            <div class="task-item-date">
                                                <?php if (!$task['due_date'] == '') : ?>
                                                    <?php if (calcLimitTime($task['due_date'])): ?>
                                                        <span class="badge task-item-badge">
                                                            <?php echo h(calcLimitTime($task['due_date'])); ?>
                                                        </span>
                                                    <?php endif ?>
                                                    <?php echo h($task['due_date']); ?>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="tabs-panel" id="panel2v">
                        <ul class="tasks">
                            <?php foreach ($tasks as $task) : ?>
                                <?php if ($task['status'] == 'done') : ?>
                                    <li class="tasks-list">
                                        <div class="task-item">
                                            <div class="task-item-checkbox">
                                                <a href="revival.php?id=<?php echo h($task['id']) ?>" class="task-check-box">
                                                    <i class="do-check fa fa-check"></i>
                                                </a>
                                            </div>
                                            <div class="task-item-content">
                                                <a href="edit.php?id=<?php echo h($task['id']) ?>">
                                                    <?php echo h($task['title']); ?>
                                                    <?php if (!$task['content'] == '') : ?>
                                                        <?php echo ':'.h($task['content']); ?>
                                                    <?php endif ?>
                                                </a>
                                            </div>
                                            <div class="task-item-date">
                                                <?php if (!$task['due_date'] == '') : ?>
                                                    <?php echo h($task['due_date']); ?>
                                                    <!-- <?php echo ', '.h(calcLimitTime($task['due_date'])); ?> -->
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif ?>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="tabs-panel" id="panel3v">

                    </div>
                    <div class="tabs-panel" id="panel4v">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>
                    <div class="tabs-panel" id="panel5v">
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    </div>
                    <div class="tabs-panel" id="panel6v">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script src="js/foundation.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>