<?php
//$mysqli = mysqli_connect('mysql', 'user1', 'user1password');
$mysqli = mysqli_connect('mysql403.phy.lolipop.lan', 'LAA1710026', 'rootpassword');
if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}
//$mysqli->select_db('webshop');
$mysqli->select_db('LAA1710026-webshop');
$result = $mysqli->query("SELECT * FROM Maker");
echo "<table border='1'>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["MakerID"] . "</td>";
    echo "<td style='color: red;'>" . $row["MakerName"] . "</td>";
    echo "<td>" . $row["MakerURL"] . "</td>";
    echo "</tr>";
}
?>