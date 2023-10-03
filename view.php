<!-- 個別画面の作成 -->
<!-- データベーステーブル、memberの情報をsessionで受け取る -->
<?php
session_start();
require('dbconnect.php');
//ログインしてない場合、トップページに戻る
if (empty($_REQUEST['id'])) {
    header('Location:index.php');
    exit();
}

//htmlspecielcharsを関数で省略
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//投稿の内容を受け取る。受け取る内容→membersテーブルの名前と写真とpostsテーブルの全部
//
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
$posts->execute(array($_REQUEST['id']));
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ひとこと掲示板</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <p>&laquo;<a href="index.php">一覧に戻る</a></p>
            <?php if ($post = $posts->fetch()) : ?>
                <div class="msg">
                    <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" , height="48" alt="<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>">
                    <p>
                        <span class="name">
                            投稿者 : <?php echo h($post['name']); ?>
                        </span>
                        <br>
                        <?php echo h($post['message']); ?>

                    </p>
                    <p class="day"><?php echo h($post['created']) ?></p>
                </div>
            <?php else : ?>
                <p>その投稿は削除されたか、URLが間違っています</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 上に戻るボタン -->
    <footer class="footer">
        <div class="top">
            <button class="up">&#128035;</button>
        </div>
    </footer>
    <!-- jsファイル読み込み -->
    <script src="js/nohara.js"></script>
</body>

</html>