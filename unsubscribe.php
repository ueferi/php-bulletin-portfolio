<?php
session_start();

// Cookie情報も削除
setcookie('email', '', time() - 3600);
setcookie('password', '', time() - 3600);

?>


<?php require('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>退会ページ</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ご利用ありがとうございました</h1>
    </div>
    <div id="content">
      <?php

      /* 未ログイン状態ならトップへリダイレクト */
      /*if (!isset($_SESSION['login'])) {
    header('Location: ./');
    exit;
  }*/

      /* 退会 */



      $id = $_SESSION['id'];
      $statement = $db->prepare('DELETE FROM members WHERE id=?');
      $statement->execute(array($id));
      // セッション情報を削除
      $_SESSION = array();
      if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
          session_name(),
          '',
          time() - 42000,
          $params["path"],
          $params["domain"],
          $params["secure"],
          $params["httponly"]
        );
      }
      session_destroy();

      // Cookie情報も削除
      setcookie('email', '', time() - 3600);
      setcookie('password', '', time() - 3600);
      ?>

      <p>退会手続きが完了しました</p>
      <p><a href="login.php">戻る</a></p>


      <!--<div id ="content">
<div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>-->
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