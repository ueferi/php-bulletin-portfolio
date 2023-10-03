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

        //cookie情報も削除

        location.href = "logout.php";
      }
    </script>
</body>

</html>