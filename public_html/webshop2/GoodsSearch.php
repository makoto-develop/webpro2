<?php
require_once("dbconf.inc");
// HTMLに表示する文字を安全に変換します。
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errorMessage = '';
$searchLabel = '';
$goodsResult = false;
$stmt = null;
$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS, DB_NAME);

if (!$mysqli) {
    $errorMessage = 'データベースに接続できませんでした。';
} else {
    mysqli_set_charset($mysqli, 'utf8');

    // 商品・メーカ・カテゴリを結合する共通のSQL（WHERE以降は検索方法で切り替えます）
    $baseSql = 'SELECT Maker.MakerID, Maker.MakerName, Maker.MakerURL, '
        . 'GoodsCategory.CategoryName, '
        . 'Goods.GoodsID, Goods.GoodsName, Goods.Price, Goods.Stock '
        . 'FROM Goods '
        . 'INNER JOIN Maker ON Maker.MakerID = Goods.MakerID '
        . 'LEFT JOIN GoodsCategory ON GoodsCategory.CategoryID = Goods.CategoryID ';
    $orderSql = ' ORDER BY Maker.MakerName, Goods.GoodsName';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // ---- 商品名検索（POST）：部分一致、未入力なら全件 ----
        $keyword = trim($_POST['goods_name'] ?? '');
        $searchLabel = ($keyword === '') ? '全商品' : '"' . $keyword . '"';

        // キーワード中の % _ を普通の文字として扱うためにエスケープします。
        $like = '%' . addcslashes($keyword, '%_\\') . '%';

        $stmt = mysqli_prepare($mysqli, $baseSql . 'WHERE Goods.GoodsName LIKE ?' . $orderSql);
        mysqli_stmt_bind_param($stmt, 's', $like);
    } else {
        // ---- カテゴリ検索（GET）：cidが1以上の数字のときだけ使います ----
        if (!isset($_GET['cid']) || !ctype_digit($_GET['cid']) || (int)$_GET['cid'] < 1) {
            $errorMessage = 'カテゴリが指定されていません。';
        } else {
            $categoryId = (int)$_GET['cid'];

            $nameStmt = mysqli_prepare($mysqli, 'SELECT CategoryName FROM GoodsCategory WHERE CategoryID = ?');
            mysqli_stmt_bind_param($nameStmt, 'i', $categoryId);
            mysqli_stmt_execute($nameStmt);
            $category = mysqli_fetch_assoc(mysqli_stmt_get_result($nameStmt));
            mysqli_stmt_close($nameStmt);

            if (!$category) {
                $errorMessage = '指定されたカテゴリが見つかりません。';
            } else {
                $searchLabel = '"' . $category['CategoryName'] . '"';
                $stmt = mysqli_prepare($mysqli, $baseSql . 'WHERE Goods.CategoryID = ?' . $orderSql);
                mysqli_stmt_bind_param($stmt, 'i', $categoryId);
            }
        }
    }

    if ($stmt) {
        if (mysqli_stmt_execute($stmt)) {
            $goodsResult = mysqli_stmt_get_result($stmt);
        }
        if (!$goodsResult) {
            $errorMessage = '商品一覧を取得できませんでした。';
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
                <h2>商品一覧検索結果</h2>
                <?php
                if ($errorMessage != '') {
                    echo '<p>' . h($errorMessage) . '</p>';
                } else {
                    $count = mysqli_num_rows($goodsResult);
                    echo '<p class="search-summary">' . h($searchLabel) . 'で検索</p>';

                    if ($count == 0) {
                        echo '<p>検索結果：みつかりませんでした</p>';
                    } else {
                        echo '<p>検索結果：' . $count . '件見つかりました。</p>';
                        echo '<p>※商品は詳細画面から購入できます。</p>';
                        echo '<div class="table-wrap">';
                        echo '<table>';
                        echo '<tr><th>メーカ名</th><th>カテゴリ</th><th>商品名</th><th>単価</th><th>在庫数</th><th>詳細</th></tr>';

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
                            echo '<td>' . h($row['CategoryName'] ?? '') . '</td>';
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

                <p class="back-link"><a href="index.php">商品検索に戻る</a></p>
            </section>
        </main>
    </div>
    </body>
    </html>
<?php
if ($goodsResult) {
    mysqli_free_result($goodsResult);
}
if ($stmt) {
    mysqli_stmt_close($stmt);
}
if ($mysqli) {
    mysqli_close($mysqli);
}
?>