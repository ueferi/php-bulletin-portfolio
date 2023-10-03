<?php
session_start();
require('dbconnect.php');

//*ログイン状態のチェック
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

//*DBへ投稿内容を登録する
if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));
        //!投稿の重複対策
        header('Location: index.php');
        exit();
    }
}

//*投稿を取得
$page = $_REQUEST['page'] ?? "";
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

//*最終ページを取得
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
//*1ページあたり15件表示
$maxPage = ceil($cnt['cnt'] / 15);
$page = min($page, $maxPage);

$start = ($page - 1) * 15;
$start = max(0, $start);

$posts = $db->prepare('SELECT m.name, m.picture, p.*, COUNT(l.post_id) AS like_cnt FROM members m, posts p LEFT JOIN likes l ON p.id=l.post_id WHERE m.id=p.member_id GROUP BY p.id ORDER BY p.created DESC LIMIT ?, 15');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

//*いいねボタンの実装
if (isset($_REQUEST['like'])) {

    //*2-1いいねを押したメッセージの投稿者を調べる
    $contributor = $db->prepare('SELECT member_id FROM posts WHERE id=?');
    $contributor->execute(array($_REQUEST['like']));
    $pressed_message = $contributor->fetch();

    //*2-2いいねを押した人とメッセージ投稿者が同一人物でないか確認
    if ($_SESSION['id'] != $pressed_message['member_id']) {

        //*2-3過去にいいね済みであるか確認
        $pressed = $db->prepare('SELECT COUNT(*) AS cnt FROM likes WHERE post_id=? AND member_id=?');
        $pressed->execute(array(
            $_REQUEST['like'],
            $_SESSION['id']
        ));
        $my_like_cnt = $pressed->fetch();

        //*2-4いいねのデータを挿入or削除
        if ($my_like_cnt['cnt'] < 1) {
            $press = $db->prepare('INSERT INTO likes SET post_id=?, member_id=?, created=NOW()');
            $press->execute(array(
                $_REQUEST['like'],
                $_SESSION['id']
            ));
            header("Location: index.php?page={$page}");
            exit();
        } else {
            $cancel = $db->prepare('DELETE FROM likes WHERE post_id=? AND member_id=?');
            $cancel->execute(array(
                $_REQUEST['like'],
                $_SESSION['id']
            ));
            header("Location: index.php?page={$page}");
            exit();
        }
    }
}

//*返信の場合
if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.*FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'];
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cherry+Bomb+One&family=Zen+Maru+Gothic:wght@700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>ひとこと掲示板</h1>
        </div>
        <div id="content">
            <!-- ログアウトリンク -->
            <div style="text-align: right"><a href="logout.php"><img src="images/logout.png" alt="ログアウト"></a></div>
            <div style="text-align: right"><a href="my_page.php">マイページへ</a></div>
            <!-- 投稿フォーム -->
            <form action="" method="post">
                <dl>
                    <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                    <dd>
                        <!-- 文字数を140文字に制限する -->
                        <textarea name="message" cols="50" rows="5" class="textlimit textbox" oninput="limitTextLength();"><?php echo h($message ?? ""); ?></textarea>
                        <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res'] ?? ""); ?>">
                    </dd>
                </dl>
                <div>
                    <!-- 文字数カウント -->
                    <p><input type="image" src="images/16.png" value="投稿する" class="submit-btn">あと<span class="numcounter">140</span>文字</p>
                </div>
            </form>

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

                        <!--いいねボタンの実装-->
                        <?php
                        //*ログインしている人がいいねしたメッセージをすべて取得
                        $like = $db->prepare('SELECT post_id FROM likes WHERE member_id=?');
                        $like->execute(array($_SESSION['id']));
                        while ($like_record = $like->fetch()) {
                            $my_like[] = $like_record;
                        }
                        $my_like_cnt = 0;
                        if (!empty($my_like)) {
                            foreach ($my_like as $like_post) {
                                foreach ($like_post as $like_post_id) {
                                    if ($like_post_id == $post['id']) {
                                        $my_like_cnt = 1;
                                    }
                                }
                            }
                        }
                        ?>
                        <!--いいねボタンをクリックすると$_REQUEST['like']に$post['id']の値が入る-->
                        <?php if ($my_like_cnt < 1) : ?>
                            <a class="heart" href="index.php?like=<?php echo h($post['id']); ?>&page=<?php echo h($page); ?>">&#9825;</a>
                            <!--URLパラメータのlikeに$post['id'](postsテーブルのidを取得した値-->
                        <?php else : ?>
                            <a class="heart red" href="index.php?like=<?php echo h($post['id']); ?>$page=<?php echo h($page); ?>">&#x1f493;</a>
                            <!--2-3 いいねボタンのハート表示を切り替える &#9825,&#9829 は特殊文字で♡を表示させる-->
                        <?php endif; ?>

                        <?php if (!$post['like_cnt'] == 0) : ?>
                            <span><?php echo h($post['like_cnt']); ?></span> <!-- いいねボタンの横に件数を表示 -->
                        <?php endif; ?>

                        <!-- 自分の投稿のみ削除できる -->
                        <?php if ($_SESSION['id'] == $post['member_id']) : ?>
                            <!-- 削除実行前の確認アラート -->
                            [<a style=" color:#f33;" class="delete" data-post-id="<?php echo h($post['id']); ?>">削除</a>]
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
            <!-- ページング -->
            <ul class="paging">
                <?php if ($page > 1) { ?>
                    <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
                <?php } else { ?>
                    <li>前のページへ</li>
                <?php } ?>
                <?php if ($page < $maxPage) { ?>
                    <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
                <?php } else { ?>
                    <li>次のページへ</li>
                <?php } ?>
            </ul>
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