<ol>
    <li>画像をただ表示してみる</li>
</ol>

<?php
echo "<img src='./img/celeron.jpg' />";
$imgName = "celeron.jpg";
echo "<img src='./img/" . $imgName . "'>";

require_once("dbconf.inc");

$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->select_db(DB_NAME);
$sql = "SELECT GoodsName, ImageName FROM Goods where GoodsID=401";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $row=$result->fetch_assoc();
    echo "<img src='./img/" . $row["ImageName"] . "'>";
} else {
    echo "検索結果はありません";
}

$result->free();
$mysqli->close();
?>