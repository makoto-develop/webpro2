<?php
require_once("dbconf.inc");
// HTMLに表示する文字を安全に変換します。
function h($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errorMessage = '';
$goods        = null;

$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS, DB_NAME);

if (!$mysqli) {
    $errorMessage = 'データベースに接続できませんでした。';
} else {
    mysqli_set_charset($mysqli, 'utf8');

    // gidが数字で1以上のときだけ商品IDとして使います。
    if (!isset($_GET['gid']) || !ctype_digit($_GET['gid']) || (int)$_GET['gid'] < 1) {
        $errorMessage = '商品が指定されていません。';
    } else {
        $goodsId = (int)$_GET['gid'];

        $sql = 'SELECT Goods.GoodsID, Goods.GoodsName, Goods.Price, Goods.Stock, Goods.ImageName, '
             . 'Maker.MakerName, Maker.MakerURL, '
             . 'GoodsCategory.CategoryName '
             . 'FROM Goods '
             . 'INNER JOIN Maker ON Maker.MakerID = Goods.MakerID '
             . 'LEFT JOIN GoodsCategory ON GoodsCategory.CategoryID = Goods.CategoryID '
             . 'WHERE Goods.GoodsID = ?';

        $stmt = mysqli_prepare($mysqli, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $goodsId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $goods  = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            if (!$goods) {
                $errorMessage = '指定された商品が見つかりません。';
            }
        } else {
            $errorMessage = '商品情報を取得できませんでした。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo $goods ? h($goods['GoodsName']) . ' | 日電通販サイト' : '商品詳細 | 日電通販サイト'; ?></title>
    <style>
        * { box-sizing: border-box; }
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
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }
        header { padding: 24px 32px; background: #078a45; color: #fff; }
        h1 { margin: 0; font-size: 28px; letter-spacing: 0.04em; }
        main { padding: 32px; }
        h2 {
            margin: 0 0 24px;
            padding-bottom: 10px;
            border-bottom: 4px solid #07964c;
            font-size: 22px;
        }
        p { margin: 0 0 14px; line-height: 1.8; }
        .detail-wrap {
            display: flex;
            gap: 32px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .detail-img img {
            max-width: 220px;
            max-height: 220px;
            border: 1px solid #d7ddd7;
            border-radius: 4px;
            object-fit: contain;
            background: #fafafa;
        }
        .detail-img .no-image {
            width: 220px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d7ddd7;
            border-radius: 4px;
            color: #aaa;
            font-size: 14px;
            background: #fafafa;
        }
        .detail-info { flex: 1; min-width: 220px; }
        .detail-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-info th,
        .detail-info td {
            padding: 10px 14px;
            border: 1px solid #d7ddd7;
            text-align: left;
        }
        .detail-info th {
            background: #edf5ee;
            width: 120px;
            white-space: nowrap;
        }
        .price {
            font-size: 1.3em;
            font-weight: bold;
            color: #c0392b;
        }
        a { color: #1269b0; }
        .back-link { display: inline-block; margin-top: 24px; }
        @media (max-width: 600px) {
            .container { width: calc(100% - 24px); margin: 20px auto; }
            header, main { padding: 22px 20px; }
            h1 { font-size: 24px; }
            h2 { font-size: 20px; }
            .detail-img img { max-width: 100%; }
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
            <h2>商品詳細</h2>
            <?php
            if ($errorMessage != '') {
                echo '<p>' . h($errorMessage) . '</p>';
            } else {
                $imgFile  = trim((string)($goods['ImageName'] ?? ''));
                $imgPath  = './img/' . $imgFile;
                $makerUrl = trim((string)($goods['MakerURL'] ?? ''));
                $urlParts = $makerUrl ? parse_url($makerUrl) : false;
                $makerLink = ($makerUrl !== '' && $urlParts && isset($urlParts['scheme'])
                              && ($urlParts['scheme'] === 'http' || $urlParts['scheme'] === 'https'))
                           ? '<a href="' . h($makerUrl) . '" target="_blank">' . h($goods['MakerName']) . '</a>'
                           : h($goods['MakerName']);
                ?>
                <div class="detail-wrap">
                    <div class="detail-img">
                        <?php if ($imgFile !== ''): ?>
                            <img src="<?php echo h($imgPath); ?>" alt="<?php echo h($goods['GoodsName']); ?>">
                        <?php else: ?>
                            <div class="no-image">画像なし</div>
                        <?php endif; ?>
                    </div>
                    <div class="detail-info">
                        <table>
                            <tr><th>商品名</th><td><?php echo h($goods['GoodsName']); ?></td></tr>
                            <tr><th>メーカ</th><td><?php echo $makerLink; ?></td></tr>
                            <tr><th>カテゴリ</th><td><?php echo h($goods['CategoryName'] ?? ''); ?></td></tr>
                            <tr><th>単価</th><td class="price">¥<?php echo number_format((int)$goods['Price']); ?></td></tr>
                            <tr><th>在庫数</th><td><?php echo (int)$goods['Stock']; ?> 個</td></tr>
                        </table>
                    </div>
                </div>
                <?php
            }
            ?>
            <p class="back-link"><a href="javascript:history.back()">← 前のページに戻る</a></p>
        </section>
    </main>
</div>
</body>
</html>
<?php
if ($mysqli) { mysqli_close($mysqli); }
?>
