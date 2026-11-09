<?php
require_once("dbconf.inc");

$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->select_db(DB_NAME);
$stext = $_POST["searchText"] ?? "";
if ($stext === "") {
    $sql = "SELECT * FROM Maker";
} else {
    $sql = "SELECT * FROM Maker WHERE MakerName LIKE '%{$stext}%'";
}

$result = $mysqli->query($sql);
while($row = $result->fetch_assoc()) {
    echo $row["MakerID"] . " --- ";
    echo $row["MakerName"] . " --- ";
    echo $row["MakerURL"] . " --- ";
    echo "<br>";
}
$result->free();
$mysqli->close();
?>
