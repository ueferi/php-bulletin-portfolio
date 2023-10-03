<?php
session_start();
require('../dbconnect.php');

if (!isset($_SESSION['join'])) {
    header('Location: 7-24.index.php');
    exit();
}

if (!empty($_POST)) {
    //登録処理をする
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?,password=?, picture=?, created=NOW()');
    echo $ret = $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        sha1($_SESSION['join']['password']),
        $_SESSION['join']['image']
    ));
    unset($_SESSION['join']);

    header('Location: thanks.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認画面</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>
        </div>
        <div id="content">
            <form action="" method="post">
                <input type="hidden" name="action" value="submit" />
                <dl>
                    <dt>ニックネーム</dt>
                    <dd>
                        <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES); ?>
                    </dd>
                    <dt>メールアドレス</dt>
                    <?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES); ?>
                    <dd>
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        [表示されません]
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES); ?>" width="100" height="100" alt="" />
                    </dd>

                </dl>
                <div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
            </form>
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