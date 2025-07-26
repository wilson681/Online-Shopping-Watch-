<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

$db = new PDO('mysql:host=localhost;dbname=Qtime;charset=utf8', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,  // ✅ 获取对象格式
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // ✅ 让 SQL 报错时更容易调试
]);

// 启动 Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 跳转函数
function redirect($url) {
    header("Location: $url");
    exit();
}

?>
