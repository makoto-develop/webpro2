<?php
    session_start();

    $gid = $_SESSION["gid"];
    $num = $_SESSION["num"];

    require_once("dbconf.inc");

    $mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);
    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit;
    }

    $mysqli->select_db(DB_NAME);
    $result = $mysqli->query("select Price from Goods where GoodsID={$gid}");
    $row = $result->fetch_assoc();
?>

<h2>商品金額計算結果</h2>
商品コード：<?php echo $gid; ?><br/>
単価：<?php echo $row["Price"]; ?><br/>
注文個数：<?php echo $num; ?><br/>
総額：<?php echo ($row["Price"] * $num); ?><br/>
