<?php
require_once("../dbconf.inc");
$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);

if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}

$mysqli->select_db(DB_NAME);
$result = $mysqli->query("SELECT * FROM GoodsCategory");
while ($row = $result->fetch_assoc()) {
    echo $row["CategoryName"];
    echo "<br>";
}
echo "</table>";
$result->free();
$mysqli->close();
?>