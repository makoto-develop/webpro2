<?php
$mid=$_GET["mid"];
$mysqli = mysqli_connect('mysql', 'user1', 'user1password');
//$mysqli = mysqli_connect('mysql403.phy.lolipop.lan', 'LAA1710026', 'rootpassword');
if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}
$mysqli->select_db('webshop');
//$mysqli->select_db('LAA1710026-webshop');
$result = $mysqli->query("SELECT * FROM Goods where MakerID={$mid}");
//echo "<h1>" . $result->num_rows . "</h1>";
//select * from Goods inner join Maker on Maker.MakerID = Goods. MakerID where CategoryID=1

while ($row = $result->fetch_assoc()) {
    echo $row["GoodsName"] . "/";
    echo $row["Price"] . "/";
    echo $row["Stock"];
    echo "<br>";
}
echo "</table>";
$result->free();
$mysqli->close();
?>