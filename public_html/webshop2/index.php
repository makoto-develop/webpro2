<?php
require_once("dbconf.inc");
// HTMLに表示する文字を安全に変換します。
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS, DB_NAME);
if (!$mysqli) {
    exit('データベースに接続できませんでした。');
}
mysqli_set_charset($mysqli, 'utf8');

$categoryResult = mysqli_query(
    $mysqli,
    'SELECT CategoryID, CategoryName FROM GoodsCategory ORDER BY CategoryID'
);
if (!$categoryResult) {
    mysqli_close($mysqli);
    exit('カテゴリ一覧を取得できませんでした。');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カテゴリ検索 | 日電通販サイト</title>
</head>
<body>
<h1>日電通販サイト</h1>
<h2>カテゴリ検索</h2>
<p>お探しの商品のカテゴリを選択してください。</p>
<ul>
<?php
while ($category = mysqli_fetch_assoc($categoryResult)) {
    $categoryId = (int)$category['CategoryID'];
    $categoryName = h($category['CategoryName']);
    echo '<li><a href="CategorySearch.php?cid=' . $categoryId . '">';
    echo $categoryName;
    echo '</a></li>';
}
?>
</ul>
</body>
</html>
<?php
mysqli_free_result($categoryResult);
mysqli_close($mysqli);
?>
