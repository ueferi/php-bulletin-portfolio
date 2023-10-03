<?php
session_start();
?>


<?php require('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>退会確認ページ</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>退会確認ページです</h1>
    </div>
    <script>
      const result = confirm('本当に削除してよろしいですか？');
      if (result) {
        //削除する処理
        console.log('削除しました');
        location.href = "unsubscribe.php";
      } else {
        console.log('キャンセルしました');
        location.href = "my_page.php";
      }
    </script>
    <!-- 上に戻るボタン -->
    <footer class="footer">
      <div class="top">
        <button class="up">&#128035;</button>
      </div>
    </footer>
    <!-- jsファイル読み込み -->
</body>

</html>