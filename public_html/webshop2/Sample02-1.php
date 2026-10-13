<lt>
<li>Step1. メーカ名の一覧を表示する</li>
<li>Step2. メーカ名をクリックしたらページ遷移する</li>
</lt>

<?php
$mysqli = mysqli_connect('mysql', 'user1', 'user1password');
//$mysqli = mysqli_connect('mysql403.phy.lolipop.lan', 'LAA1710026', 'rootpassword');
if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}
$mysqli->select_db('webshop');
//$mysqli->select_db('LAA1710026-webshop');
$result = $mysqli->query("SELECT * FROM Maker");
while ($row = $result->fetch_assoc()) {
    echo "<a href='./Sample02-2.php?mid=" . $row["MakerID"] . "'>" . $row["MakerName"] . "</a><br>";
}
echo "</table>";
$result->free();
$mysqli->close();
?>