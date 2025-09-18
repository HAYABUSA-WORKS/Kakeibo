<?php
require_once("../../data/db_info_kakeibo.php");
$con = new PDO("mysql:host=$SERVER;dbname=$DBNM", "$USER", "$PASS");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>買い物の記録</title>
</head>
<body>
    <h2>買い物を記録する</h2>

    <form action="register.php" method="post">
        <table border="1">
            <tr>
                <th>ユーザー</th>
                <th>購入日　</th>
                <th>買ったもの　</th>
                <th>値段　</th>
                <th>個数　</th>
            </tr>
            <tr>
                <td>
                    <select name="aid">
                        <?php
                        $sel = $con->query("SELECT aid, aname FROM account");
                        while($rs = $sel->fetch()){
                            print <<< EOT
                                <option value="{$rs['aid']}">{$rs['aname']}</option>
                            EOT;
                        }
                        ?>   
                    </select>
                </td>
                <td><input type="date" name="date"></td>
                <td><input type="text" name="gname" size="30"></td>
                <td><input type="text" name="price" size="5"></td>
                <td><input type="text" name="quantity" size="2"></td>
            </tr>
        </table>
        <br>
        <input type="submit" value="記録する">
    </form>

    <?php
    //買い物の記録
    $aid = isset($_POST['aid']) ? htmlspecialchars($_POST['aid']) : null;
    $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : null;
    $gname = isset($_POST['gname']) ? htmlspecialchars($_POST['gname']) : null;
    $price = isset($_POST['price']) ? htmlspecialchars($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : null;

    if($date <> null && $gname <> null){
        require_once("../../data/db_info_kakeibo.php");
        $con = new PDO("mysql:host=$SERVER;dbname=$DBNM", "$USER", "$PASS");
        // goodsテーブルに入力された商品が存在するか確認する（gnameとpriceの組み合わせ）
        $sel = $con->query("SELECT 1 FROM goods WHERE gname = '{$gname}' AND price = {$price} LIMIT 1");
        // goodsテーブルに入力された商品が存在しない時：入力された商品をgoodsテーブルに追加する
        if($sel->fetchColumn() === false){
            $con->query("INSERT INTO goods (gname, price) VALUES ('{$gname}', {$price})");
        }
        $sel = $con->query("SELECT gid FROM goods WHERE gname = '{$gname}' AND price = {$price}");
        while($rs = $sel->fetch()){
            $gid = $rs['gid'];
        }
        //print "gid = ".$gid;
        $con->query("INSERT INTO list (date, aid, gid, quantity) VALUES ('{$date}', {$aid}, {$gid}, {$quantity})");
    }
    ?>

    <hr>

    <h3>履歴検索</h3>
    
    <form action="register.php" method="get">
        <p>品名：<input type="text" name="gname" size="30">
        <input type="submit" value="検索"></p>
    </form>

    <?php
    // 履歴検索
    $gname = isset($_GET['gname']) ? htmlspecialchars($_GET['gname']) : null;

    if($gname <> null){
        require_once("../../data/db_info_kakeibo.php");
        $con = new PDO("mysql:host=$SERVER;dbname=$DBNM", "$USER", "$PASS");
        $sel = $con->query("SELECT gname, price FROM goods WHERE gname LIKE '%{$gname}%'");
    ?>
        <h3>検索結果</h3>
        <table border="1">
            <tr>
                <th>商品名　</th>
                <th>値段　</th>
            </tr>
    <?php   
        while($rs = $sel->fetch()){
            print <<< EOT
                <tr>
                    <td>{$rs['gname']}</td>
                    <td>{$rs['price']}円</td>
                </tr>
            EOT;
        }
        print "</table>";
    }
    ?>

    <hr>
    <a href="index.php">もどる</a>
</body>
</html>