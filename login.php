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

      header('Location: index.php');
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cherry+Bomb+One&family=Zen+Maru+Gothic:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <title>ログインページ</title>
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ログインする</h1>
    </div>
    <div id="content">
      <div id="lead">
        <p>メールアドレスとパスワードを記入してログインしてください。</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/">入会手続きをする</a></p>
      </div>

      <form action="" method="post">
        <dl>

          <dt>メールアドレス</dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" class="textbox">
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
            <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($password, ENT_QUOTES); ?>" class="textbox">
          </dd>

          <dt>ログイン情報の記録</dt>
          <dd>
            <input id="save" type="checkbox" name="save" value="on">
            <label for="save">次回からは自動的にログインする</label>
          </dd>

        </dl>

        <div>
          <input type="image" src="images/login.png" value="ログインする">
        </div>

        <div id="lead">
          <p>退会手続きはこちらからどうぞ。</p>
          <p>&raquo;<a href="leave.php">退会手続きをする</a></p>
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