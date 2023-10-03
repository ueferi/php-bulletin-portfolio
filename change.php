<?php
require('dbconnect.php');
session_start();

//htmlspecielcharsを関数で省略
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$members = $db->prepare('SELECT*FROM members WHERE id=?');
$members->execute(array($_SESSION['id']));
$member = $members->fetch();

$error = array();
// error_reporting(0);
if (!empty($_POST)) {
    //エラー項目の確認
    /*
        if($_POST['name'] == '') {
                $error['name'] = 'blank';
        }
        if ($_POST['email'] =='') {
                $error['email'] = 'blank';
        }
*/
    if (empty($_POST['password']) && strlen($_POST['password']) >= 1 && strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    /*
        if ($_POST['password'] == '') {
            $error['password'] = 'blank' ;
        }
*/
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
    }

    //重複アカウントのチェック
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*)AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (!empty($_FILES['image']['name'])) {
        //画像をアップロードする
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'member_picture/' . $image);
    }
    if (empty($error)) {
        $_SESSION['join'] = $_POST;
        if (!empty($_FILES['image']['name'])) {
            $_SESSION['join']['image'] = $image;
        }
        header('Location: change_check.php');
        exit();
    }
}

// 書き直し
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'rewrite')) {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録情報変更ページ</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

</head>

<body>
    <div id='wrap'>
        <div id='head'>
            <h1>会員情報の変更</h1>
        </div>
        <div id='content'>
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

            <p style="padding-top:10px;">次のフォームに必要事項をご記入ください。</p>
            <span class="required">※入力がない場合、元の内容を引き継ぎます</span>
            <form action="" method="post" enctype="multipart/form-data">
                <dl>

                    <dt>ニックネーム</dt>
                    <dd>
                        <input type="text" class="textbox" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'] ?? "", ENT_QUOTES); ?>" />
                        <!--
                <?php // if (isset($error['name']) && ($error['name'] == 'blank')):
                ?>
                <p class="error">* ニックネームを入力してください</p>
                <?php // endif;
                ?>
                -->
                    </dd>
                    <dt>メールアドレス</dt>
                    <dd>
                        <input type="text" class="textbox" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'] ?? "", ENT_QUOTES); ?>" />
                        <?php // if (isset($error['email']) && ($error['email'] == 'blank')):
                        ?>
                        <!--
                <p class="error">* メールアドレスを入力してください</p>
                -->
                        <?php // endif;
                        ?>
                        <?php if (isset($error['email']) && ($error['email'] == 'duplicate')) : ?>
                            <p class="error">* 指定されたメールアドレスは既に登録されています</p>
                        <?php endif; ?>
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        <input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'] ?? "", ENT_QUOTES); ?>">
                        <?php // if (isset($error['password']) && ($error['password']  == 'blank')):
                        ?>
                        <!--
                <p class="error">パスワードを入力してください</p>
                -->
                        <?php // endif;
                        ?>
                        <?php if (/*isset($error['password']) && */($error['password'] ?? "" == 'length')) : ?>
                            <p class="error">* パスワードは4文字以上で入力してください</p>
                        <?php endif; ?>
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <input type="file" name="image" size="35" />
                        <?php if (isset($error['image']) && ($error['image'] == 'type')) : ?>
                            <p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください</p>
                        <?php endif; ?>
                        <?php // if (!empty($error)):
                        ?>
                        <!--
                <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                <?php // endif;
                ?>
                -->
                    </dd>
                </dl>
                <div><input type="submit" value="入力内容を確認する" /></div>
            </form>
            <div style="text-align: right; padding-bottom: 20px;"><a href="my_page.php">My pageに戻る</a></div>
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