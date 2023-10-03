<?php
session_start();
require('dbconnect.php');

//*ログイン状態をチェック
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //*ログインしている
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT*FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    //*ログインしていない
    header('Location: login.php');
    exit();
}

//*DBへ投稿を記録する
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));

        header('Location: index.php');
        exit();
    }
}

//*返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ';
}

//*htmlspecielcharsを関数で省略
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

//*本文内のURLにリンクを設定
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
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <!-- ログアウトリンク -->
            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <!-- 投稿フォーム -->
            <form action="" method="post">
                <dl>
                    <dt><?php echo h($member['name']); ?>さん、返信をどうぞ</dt>
                    <!-- 返信元メッセージを表示 -->
                    <dd>
                        <?php
                        $response = $db->prepare('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ');
                        $response->execute(array($_REQUEST['res']));
                        $table = $response->fetch();
                        $message = '@' . $table['name'] . ' ' . $table['message'];
                        print($message)
                        ?>
                    </dd>
                    <dd>
                        <!-- 返信の際テキストエリアに表示される内容 -->
                        <?php
                        if (isset($_REQUEST['res'])) {
                            $response = $db->prepare('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
                            $response->execute(array($_REQUEST['res']));
                            $table = $response->fetch();
                            $message = '@' . $table['name'] . ' ';
                        }
                        ?>
                        <!-- 文字数を140文字に制限する -->
                        <textarea name="message" cols="50" rows="5" class="textlimit textbox" oninput="limitTextLength();"></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res'] ?? ""); ?>">
                    </dd>
                </dl>
                <div>
                    <!-- 文字数カウント -->
                    <p><input type="submit" value="投稿する" class="submit-btn">あと<span class="numcounter">140</span>文字</p>
                    <!-- 一覧に戻るリンク -->
                    <p><a href="index.php">一覧に戻る</a></p>
                </div>
            </form>
            </ul>
        </div>
    </div>
    <footer class="footer">
        <div class="top">
            <button class="up">&#128035;</button>
        </div>
    </footer>
    <!-- jsファイル読み込み -->
    <script src="js/nohara.js"></script>
</body>

</html>