<?php
require_once("../../data/db_info_kakeibo.php");
$con = new PDO("mysql:host=$SERVER;dbname=$DBNM", "$USER", "$PASS");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>たのしいポリテク家計簿2</title>
</head>
<body>
    <hr>
    <h1>たのしいポリテク家計簿</h1>
    <hr>

    <h2>登録</h2>
        <p><a href="register_user.php">ユーザーの登録</a></p>
        <p><a href="register.php">買ったものの登録</a></p>
    <hr>

    <!-- 検索フォーム -->
    <h2>検索</h2>
        <form action="index.php" method="post">
            【ユーザー名】<br>
            <?php
            $sel = $con->query("SELECT aname FROM account");
            while($rs = $sel->fetch(PDO::FETCH_ASSOC)){
                print <<< EOT
                    <label><input type="checkbox" name="userNames[]" value="{$rs['aname']}">{$rs['aname']}</label>
                EOT;
            }
            ?>
            <br><br>
            【買ったもの】<br>
            <input type="text" name="goodsName">

            <br><br>
            【購入日】<br>
            <input type="date" name="date">

            <br>
            <input type="hidden" name="search" value="yes">
            <p><input type="submit" value="検索スタート"></p>
        </form>
    <hr>

    <!-- 検索結果表示用のテーブル -->
    <table border="1">
        <tr>
            <th>ユーザー</th>
            <th>購入日　</th>
            <th>買ったもの　</th>
            <th>値段　</th>
            <th>個数　</th>
        </tr>
        <?php
        // 「検索スタート」ボタンが押された時
        if(isset($_POST["search"])){
            $sql = "SELECT aname, date, gname, price, quantity
                    FROM list AS l
                    INNER JOIN account AS a ON l.aid = a.aid
                    INNER JOIN goods AS g ON l.gid = g.gid
                    WHERE 1 = 1";
            
            // 「ユーザー名」で検索処理
            $userNames = array();
            if(isset($_POST["userNames"])){
                $sql .= " AND aname IN (%s)";
                $userNames = $_POST["userNames"];

                $sql = sprintf($sql, substr(str_repeat(",?", count($userNames)), 1));
            }
            // 「買ったもの」で検索
            $bindGoodsName = "";
            if(isset($_POST["goodsName"])){
                $sql .= " AND gname LIKE ?";
                $goodsName = htmlspecialchars($_POST["goodsName"]);
                $bindGoodsName = "%".$goodsName."%";
            }
            // 「購入日」で検索
            $date = "";
            if(!empty($_POST["date"])){
                $sql .= " AND date = ?";
                $date = $_POST["date"];
            }

            $stmt = $con->prepare($sql);

            // 「ユーザー名」の情報をバインド
            $i = 0;
            for($i = 0; $i < count($userNames); $i++){
                $stmt->bindParam($i + 1, $userNames[$i], PDO::PARAM_STR);
            }
            // 「買ったもの」の情報をバインド
            $stmt->bindParam(++$i, $bindGoodsName, PDO::PARAM_STR);
            // 「購入日」の情報をバインド
            if(!empty($_POST["date"])){
                $stmt->bindParam(++$i, $date);
            }

            $stmt->execute();

            while($rs = $stmt->fetch(PDO::FETCH_ASSOC)){
                print <<< EOT
                    <tr>
                        <th>{$rs['aname']}　</th>
                        <th>{$rs['date']}　</th>
                        <th>{$rs['gname']}　</th>
                        <th>{$rs['price']}　</th>
                        <th>{$rs['quantity']}　</th>
                    </tr>
                EOT;
            }
        }
        ?>
    </table>
    <?php
    ?>
</body>
</html>