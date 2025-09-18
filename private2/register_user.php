<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーの登録</title>
</head>
<body>
    <h2>ユーザーの登録</h2>
    <form action="register_user.php" method="post">
        <p>おなまえ：<input type="text" name="name"></p>
        <p>
            性別：
            <label><input type="radio" name="gender" value="1" checked>男性</label>
            <label><input type="radio" name="gender" value="2">女性</label>
        </p>
        <input type="submit" value="作成">
    </form>

    <?php
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : null;
    $gender = isset($_POST['gender']) ? htmlspecialchars($_POST['gender']) : null;

    // 「おなまえ」欄が空欄＝nullじゃない時：ユーザーを登録
    if($name <> null){
        require_once("../../data/db_info_kakeibo.php");
        $con = new PDO("mysql:host=$SERVER;dbname=$DBNM", "$USER", "$PASS");
        $con->query("INSERT INTO account (aname, gender) VALUES ('{$name}', {$gender})");
        print "<p>追加しました</p>";
    }
    ?>

    <hr>
    <a href="index.php">もどる</a>
</body>
</html>