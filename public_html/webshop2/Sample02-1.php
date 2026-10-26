<lt>
<li>Step1. メーカ名の一覧を表示する</li>
<li>Step2. メーカ名をクリックしたらページ遷移する</li>
</lt>

<?php
require_once("dbconf.inc");
$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);

if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}
$mysqli->select_db(DB_NAME);
$result = $mysqli->query("SELECT * FROM Maker");
while ($row = $result->fetch_assoc()) {
    echo "<a href='./Sample02-2.php?mid=" . $row["MakerID"] . "'>" . $row["MakerName"] . "</a><br>";
}
echo "</table>";
$result->free();
$mysqli->close();
?>