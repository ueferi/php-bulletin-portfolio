<?php
//セッション
session_start();
//DB接続プログラムを呼び出す
require('dbconnect.php');
//ログイン状態をチェック
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログインしている
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT*FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    //ログインしていない
    header('Location: login.php');
    exit();
}

//htmlspecielcharsを関数で省略
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//本文内のURLにリンクを設定
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ありがとうございます</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>更新完了</h1>
        </div>
        <div id="content">
            <p>更新が完了しました</p>
            <p><a href="my_page.php">マイページへ</a></p>
        </div>
        <!-- 上に戻るボタン -->
        <footer class="footer">
            <div class="top">
                <button class="up">&#128035;</button>
            </div>
        </footer>
        <!-- jsファイル読み込み -->
</body>

</html>