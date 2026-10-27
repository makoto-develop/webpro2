<?php
// HTMLに表示する文字を安全に変換します。
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errorMessage = '';
$categoryName = '';
$goodsResult = false;
$mysqli = mysqli_connect('mysql', 'user1', 'user1password', 'webshop');

if (!$mysqli) {
    $errorMessage = 'データベースに接続できませんでした。';
} else {
    mysqli_set_charset($mysqli, 'utf8');

    // cidが数字で、1以上のときだけカテゴリIDとして使います。
    if (!isset($_GET['cid']) || !ctype_digit($_GET['cid']) || (int)$_GET['cid'] < 1) {
        $errorMessage = 'カテゴリが指定されていません。';
    } else {
        $categoryId = (int)$_GET['cid'];

        // 1つ目のSQL：カテゴリIDからカテゴリ名を調べます。
        $categorySql = 'SELECT CategoryName FROM GoodsCategory WHERE CategoryID = ' . $categoryId;
        $categoryResult = mysqli_query($mysqli, $categorySql);

        if (!$categoryResult) {
            $errorMessage = 'カテゴリを取得できませんでした。';
        } else {
            $category = mysqli_fetch_assoc($categoryResult);
            if ($category) {
                $categoryName = $category['CategoryName'];

                // 2つ目のSQL：商品とメーカを結合して商品一覧を調べます。
                $goodsSql = 'SELECT Maker.MakerID, Maker.MakerName, Maker.MakerURL, '
                    . 'Goods.GoodsID, Goods.GoodsName, Goods.Price, Goods.Stock '
                    . 'FROM Goods INNER JOIN Maker '
                    . 'ON Maker.MakerID = Goods.MakerID '
                    . 'WHERE Goods.CategoryID = ' . $categoryId . ' '
                    . 'ORDER BY Maker.MakerName, Goods.GoodsName';
                $goodsResult = mysqli_query($mysqli, $goodsSql);

                if (!$goodsResult) {
                    $errorMessage = '商品一覧を取得できませんでした。';
                }
            } else {
                $errorMessage = '指定されたカテゴリが見つかりません。';
            }
            mysqli_free_result($categoryResult);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カテゴリ検索結果 | 日電通販サイト</title>
</head>
<body>
<h1>日電通販サイト</h1>
<h2>カテゴリ一覧検索結果</h2>

<?php
if ($errorMessage != '') {
    echo '<p>' . h($errorMessage) . '</p>';
} else {
    echo '<p>「' . h($categoryName) . '」で検索</p>';
    echo '<p>検索結果: ' . mysqli_num_rows($goodsResult) . '件見つかりました。</p>';
    echo '<p>※商品は詳細画面から購入できます。</p>';

    if (mysqli_num_rows($goodsResult) == 0) {
        echo '<p>検索結果：みつかりませんでした</p>';
    } else {
        echo '<table border="1">';
        echo '<tr><th>メーカ名</th><th>商品名</th><th>単価 (Price)</th><th>在庫数</th><th>詳細</th></tr>';

        // 同じメーカの商品が続く場合、メーカ名は最初の行だけに表示します。
        $shownMakers = array();
        while ($row = mysqli_fetch_assoc($goodsResult)) {
            echo '<tr><td>';

            $makerId = $row['MakerID'];
            if (!isset($shownMakers[$makerId])) {
                $shownMakers[$makerId] = true;
                $makerName = h($row['MakerName']);
                $makerUrl = trim((string)$row['MakerURL']);
                $urlParts = parse_url($makerUrl);

                // メーカURLが設定されている場合は、メーカ名をリンクにします。
                if ($makerUrl != '' && $urlParts && isset($urlParts['scheme'])
                    && ($urlParts['scheme'] == 'http' || $urlParts['scheme'] == 'https')) {
                    echo '<a href="' . h($makerUrl) . '" target="_blank">' . $makerName . '</a>';
                } else {
                    echo $makerName;
                }
            }

            echo '</td>';
            echo '<td>' . h($row['GoodsName']) . '</td>';
            echo '<td>¥' . number_format((int)$row['Price']) . '</td>';
            echo '<td>' . (int)$row['Stock'] . '</td>';
            echo '<td><input type="button" value="詳細" '
                . 'onclick="document.location=\'./GoodsDisplay.php?gid=' . (int)$row['GoodsID'] . '\'"></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
?>

<p><a href="index.php">カテゴリ一覧に戻る</a></p>
</body>
</html>
<?php
if ($goodsResult) {
    mysqli_free_result($goodsResult);
}
if ($mysqli) {
    mysqli_close($mysqli);
}
?>
