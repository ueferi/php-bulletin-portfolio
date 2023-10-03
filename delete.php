<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    session_start();
    require('dbconnect.php');

    if (isset($_SESSION['id'])) {
        $id = $_REQUEST['id'];


        //投稿を検査する
        $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
        $messages->execute(array($id));
        $message = $messages->fetch();

        if ($message['member_id'] == $_SESSION['id']) {
            //削除する
            $del = $db->prepare('DELETE FROM posts WHERE id=?');
            $del->execute(array($id));
        }
    }

    header('Location: index.php');
    exit();

    ?>
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