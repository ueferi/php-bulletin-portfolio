<?php
require('dbconnect.php');

session_start();
//error配列の初期化
$error = array();
//Undefined対策
$email = filter_input(INPUT_POST, 'email');
$password = filter_input(INPUT_POST, 'password');
//COOKIE
if (!empty($_COOKIE['email'])) {
  $email = $_COOKIE['email'];
  $password = $_COOKIE['password'];
  $_POST['save'] = 'on';
}
//LOGIN情報の保持
if (!empty($_POST)) {

  if ($email != '' && $password != '') {
    $login = $db->prepare("SELECT * FROM members WHERE email=? AND password=?");

    $login->execute(array($email, sha1($password)));

    $member = $login->fetch();

    if ($member) {

      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

      if ($_POST['save'] == 'on') {
        setcookie('email', $email, time() + 60 * 60 * 24 * 14);
        setcookie('password', $password, time() + 60 * 60 * 24 * 14);
      }

      header('Location: confi2.php');
      exit;
    } else {
      $error['login'] = 'failed';
    }
  } else {
    $error['login'] = 'blank';
  }
}
?>
<!doctype html>
<html lang="ja">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <title>ログインページ</title>
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>退会手続きはこちら</h1>
    </div>
    <div id="content">

      <form action="" method="post">
        <dl>

          <dt>メールアドレス</dt>
          <dd>
            <input type="text" class="textbox" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>">
            <?php if (!empty($error['login'])) : ?>
              <?php if ($error['login'] == 'blank') : ?>
                <p class="error">* メールアドレスとパスワードをご記入ください</p>
              <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($error['login'])) : ?>
              <?php if ($error['login'] == 'failed') : ?>
                <p class="error">* ログインに失敗しました。正しくご記入ください。</p>
              <?php endif; ?>
            <?php endif; ?>

          </dd>

          <dt>パスワード</dt>
          <dd>
            <input type="password" class="textbox" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($password, ENT_QUOTES); ?>">
          </dd>

        </dl>

        <div>
          <input type="submit" value="退会する">
        </div>

      </form>

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