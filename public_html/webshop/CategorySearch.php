<?php
require_once("dbconf.inc");
// HTMLに表示する文字を安全に変換します。
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errorMessage = '';
$categoryName = '';
$goodsResult = false;
$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS, DB_NAME);

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
    <title>日電通販サイト</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6f4;
            color: #222;
            font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans",
            "Yu Gothic", "Meiryo", sans-serif;
        }

        .container {
            width: min(900px, calc(100% - 40px));
            margin: 48px auto;
            background: #fff;
            border: 1px solid #d7ddd7;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        header {
            padding: 24px 32px;
            background: #078a45;
            color: #fff;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 0.04em;
        }

        main {
            padding: 32px;
        }

        h2 {
            margin: 0 0 18px;
            padding-bottom: 10px;
            border-bottom: 4px solid #07964c;
            font-size: 22px;
        }

        p {
            margin: 0 0 18px;
            line-height: 1.8;
        }

        .search-summary {
            padding: 10px 14px;
            border-left: 4px solid #07964c;
            background: #f2f8f3;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            padding: 10px 12px;
            border: 1px solid #d7ddd7;
        }

        th {
            background: #edf5ee;
            text-align: center;
        }

        td.number {
            text-align: right;
            white-space: nowrap;
        }

        td.action {
            text-align: center;
        }

        a {
            color: #1269b0;
        }

        .back-link {
            display: inline-block;
            margin-top: 8px;
        }

        input[type="button"] {
            width: 50px;
            height: 30px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .container {
                width: calc(100% - 24px);
                margin: 20px auto;
            }

            header,
            main {
                padding: 22px 20px;
            }

            h1 {
                font-size: 24px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>日電通販サイト</h1>
    </header>

    <main>
        <section>
            <h2>カテゴリ一覧検索結果</h2>
            <?php
            if ($errorMessage != '') {
                echo '<p>' . h($errorMessage) . '</p>';
            } else {
                echo '<p class="search-summary">「' . h($categoryName) . '」で検索</p>';
                echo '<p>検索結果: ' . mysqli_num_rows($goodsResult) . '件見つかりました。</p>';

                if (mysqli_num_rows($goodsResult) == 0) {
                    echo '<p>検索結果：みつかりませんでした</p>';
                } else {
                    echo '<div class="table-wrap">';
                    echo '<table>';
                    echo '<tr><th>メーカ名</th><th>商品名</th><th>単価</th><th>在庫数</th><th>詳細</th></tr>';

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
                        echo '<td class="number">¥' . number_format((int)$row['Price']) . '</td>';
                        echo '<td class="number">' . (int)$row['Stock'] . '</td>';
                        echo '<td class="action"><input type="button" value="詳細" '
                            . 'onclick="document.location=\'./GoodsDisplay.php?gid=' . (int)$row['GoodsID'] . '\'"></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                }
            }
            ?>

            <p class="back-link"><a href="index.php">カテゴリ一覧に戻る</a></p>
        </section>
    </main>
</div>
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
