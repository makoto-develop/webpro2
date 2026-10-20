<?php
// HTMLに表示する文字を安全に変換します。
function h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['mid']) || !ctype_digit($_GET['mid']) || (int)$_GET['mid'] < 1) {
    exit('メーカが指定されていません。');
}
$mid = (int)$_GET['mid'];

//$mysqli = mysqli_connect('mysql', 'user1', 'user1password', 'webshop');
$mysqli = mysqli_connect('mysql403.phy.lolipop.lan', 'LAA1710026', 'rootpassword');
if (!$mysqli) {
    exit('データベースに接続できませんでした。');
}
mysqli_set_charset($mysqli, 'utf8');
//$mysqli->select_db('webshop');
$mysqli->select_db('LAA1710026-webshop');
$result = mysqli_query($mysqli, 'SELECT GoodsName, Price, Stock FROM Goods WHERE MakerID = ' . $mid);
if (!$result) {
    mysqli_close($mysqli);
    exit('商品一覧を取得できませんでした。');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
</head>
<body>
<!--<h1>商品一覧</h1>-->
<table border="1">
    <tr>
        <th>商品名</th>
        <th>単価</th>
        <th>在庫数</th>
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . h($row['GoodsName']) . '</td>';
        echo '<td>¥' . number_format((int)$row['Price']) . '</td>';
        echo '<td>' . (int)$row['Stock'] . '</td>';
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>
<?php
mysqli_free_result($result);
mysqli_close($mysqli);
?>
