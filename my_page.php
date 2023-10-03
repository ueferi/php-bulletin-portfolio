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

//DBへ投稿を記録する
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));
        //投稿の重複防止
        //ページをリロードしようとすると確認アラートが出る
        header('Location: my_page.php');
        exit();
    }
}

//投稿を取得
$page = $_REQUEST['page'] ?? "";
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

//$posts = $db->query('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

//最終ページを取得
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 15);
$page = min($page, $maxPage);

$start = ($page - 1) * 15;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND m.id=? ORDER BY p.created DESC LIMIT ?,15');
$posts->bindParam(1, $_SESSION['id'], PDO::PARAM_INT);
$posts->bindParam(2, $start, PDO::PARAM_INT);
$posts->execute();


//返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
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
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>MY PAGE</h1>
        </div>
        <div id="content">
            <!-- ログアウトリンク -->
            <div style="text-align: right"><a href="index.php">投稿画面に戻る</a></div>
            <div style="text-align: right"><a href="change.php">登録情報の変更</a></div>

            <div id="profile">
                <table>
                    <tr>
                        <th>ニックネーム</th>
                        <td><?php echo h($member['name']); ?></td>
                    </tr>
                    <tr>
                        <th>メールアドレス</th>
                        <td><?php echo h($member['email']); ?></td>
                    </tr>
                    <tr>
                        <th>パスワード</th>
                        <td>●●●●●●●●</td>
                    </tr>
                    <tr>
                        <th>写真</th>
                        <!--　↓　$postではforeachでしか機能しないため$member['picture']を指定する-->
                        <td><img src="member_picture/<?php echo h($member['picture']); ?>" width="48" height="48" alt="<?php echo h($member['name']); ?>"></td>
                    </tr>
                </table>

            </div>



            <!-- 投稿フォーム -->
            <form action="" method="post">
                <dl>
                    <dt style="padding-top: 10px;"><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <textarea name="message" cols="50" rows="5" class="textlimit textbox" oninput="limitTextLength();"><?php echo h($message ?? ""); ?></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res'] ?? ""); ?>">
                    </dd>
                </dl>
                <div>
                    <!-- 文字数カウント -->
                    <p><input type="submit" value="投稿する" class="submit-btn">あと<span class="numcounter">140</span>文字</p>
                </div>
            </form>
            <dl>

                <dt>このページでは自分の投稿のみ反映されます</dt>
            </dl>

            <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
            <div style="text-align: right"><a href="confimation.php">退会手続きはこちら</a></div>

            <?php foreach ($posts as $post) : ?>
                <div class="msg">

                    <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="<?php echo h($post['name']); ?>">

                    <p><span class="name">投稿者:<?php echo h($post['name']); ?></span><br><?php echo makeLink(h($post['message'])); ?>
                        <!-- 返信ページをreply.phpにリンク -->
                        [<a href="reply.php?res=<?php echo h($post['id']); ?>">Re</a>]
                    </p>
                    <p class="day">
                        <a href="view.php?id=<?php echo h($post['id']); ?>"><?php echo h($post['created']); ?></a>
                        <?php if ($post['reply_post_id'] > 0) : ?>
                            <a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
                        <?php endif; ?>

                        <!-- 削除用リンク -->
                        <?php if ($_SESSION['id'] == $post['member_id']) : ?>
                            [<a href="delete.php?id=<?php echo h($post['id']); ?>" style=" color:#f33;">削除</a>]
                        <?php endif; ?>
                    </p>
                </div>

            <?php endforeach; ?>
            <!-- ページング -->
            <ul class="paging">
                <?php if ($page > 1) { ?>
                    <li><a href="my_page.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                <?php } else { ?>
                    <li>前のページへ</li>
                <?php } ?>
                <?php if ($page < $maxPage) { ?>
                    <li><a href="my_page.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                <?php } else { ?>
                    <li>次のページへ</li>
                <?php } ?>
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