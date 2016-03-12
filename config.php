<?php

// DB接続の情報
define('DSN', 'mysql:host=localhost;dbname=task_app;charset=utf8');
define('DB_USER', 'testuser');
define('DB_PASSWORD', '9999');

// エラー表示の設定(Noticeが表示されなくなる)
error_reporting(E_ALL & ~E_NOTICE);

// タイムゾーンを設定
date_default_timezone_set("Asia/Tokyo");