<?php 
//共通接続プログラム
//require('dbconnect.php'); で呼び出し
try{
    $db = new PDO('mysql:dbname=mini_bbs;host=127.0.0.1;charset=utf8','root','');
}catch(PDOException $e){
    echo 'DB接続エラー: ' . $e->getMessage();
}