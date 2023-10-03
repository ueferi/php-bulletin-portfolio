<?php
session_start();
require('dbconnect.php');

if (!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}

$members = $db->prepare('SELECT*FROM members WHERE id=?');
$members->execute(array($_SESSION['id']));
$member = $members->fetch();

if (!empty($_POST)) {

    if (empty($_SESSION['join']['name'])) {
        $_SESSION['join']['name'] = $member['name'];
    }

    if (empty($_SESSION['join']['email'])) {
        $_SESSION['join']['email'] = $member['email'];
    }

    if (empty($_SESSION['join']['password'])) {
        $_SESSION['join']['password'] = $member['password'];
    }

    if (empty($_SESSION['join']['image'])) {
        $_SESSION['join']['image'] = $member['picture'];
    }
    //変更処理をする

    if ($_SESSION['join']['image'] != $member['picture']) {
        unlink('member_picture/' . $member['picture']);
    }

    //画像を変えた場合の処理
    // $imagename = $_SESSION['join']['image']. $_FILES['image']['name'];
    if (!empty($_FILES['image']['name'])) {
        $imagename = date('YmdHis') . $_FILES['image']['name'];
    } else {
        $imagename = $_SESSION['join']['image'];
    }

    $statement = $db->prepare('UPDATE members SET name=?, email=?,password=?, picture=? WHERE id=?');

    $statement->bindParam(1, $_SESSION['join']['name'], PDO::PARAM_STR);
    $statement->bindParam(2, $_SESSION['join']['email'], PDO::PARAM_STR);
    $statement->bindParam(3, sha1($_SESSION['join']['password']), PDO::PARAM_STR);
    $statement->bindParam(4, $imagename, PDO::PARAM_STR);
    $statement->bindParam(5, $member['id'], PDO::PARAM_INT);
    $statement->execute();

    unset($_SESSION['join']);

    header('Location: complete.php');
    exit();
}

//htmlspecielcharsを関数で省略
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$error = array();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認画面</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1>会員登録</h1>

        </div>
        <div id="content">

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
                        <td>
                            <img src="member_picture/<?php echo h($member['picture']); ?>" width="48" height="48" alt="<?php echo h($member['name']); ?>">
                        </td>
                    </tr>

                </table>
            </div>
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
                        <!-- <img src="member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES); ?>" width="100" height="100" alt="" /> -->
                        <img src="member_picture/<?php echo htmlspecialchars($member['picture'], ENT_QUOTES); ?>" width="100" height="100" alt="" />
                    </dd>

                </dl>
                <div><a href="change.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
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