<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メーカー検索 | 演習４</title>
</head>

<body>
<div class="container">
    <h1>商品金額計算</h1>
    <form action="./Sample06-2.php" method="post">
        <label>商品コード：
            <input type="text" name="gid" />
        </label><br/>
        <label>個数：
            <input type="text" name="num" />
        </label><br/>
        <input type="submit" value="計算" /><br/>
        <?php
            echo $_SESSION["errMsg"];
            unset($_SESSION["errMsg"]);
        ?>
    </form>
</div>
</body>
</html>