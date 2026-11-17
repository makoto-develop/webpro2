<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メーカー検索 | 演習４</title>
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
            width: min(600px, calc(100% - 40px));
            margin: 48px auto;
            background: #fff;
            border: 1px solid #d7ddd7;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            padding: 32px;
        }
        h1 {
            margin: 0 0 24px;
            padding-bottom: 10px;
            border-bottom: 4px solid #07964c;
            font-size: 24px;
        }
        p { margin: 0 0 16px; line-height: 1.8; }
        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        input[type="text"] {
            flex: 1;
            min-width: 180px;
            padding: 9px 10px;
            border: 1px solid #aeb8b1;
            border-radius: 4px;
            font: inherit;
            font-size: 1em;
        }
        input[type="submit"] {
            padding: 9px 20px;
            border: 0;
            border-radius: 4px;
            background: #078a45;
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        input[type="submit"]:hover { background: #056d36; }
    </style>
</head>
<body>
<div class="container">
    <h1>メーカーを検索</h1>
    <p>メーカー名の一部または全てを入力してください。空白のままで全メーカーを表示します。</p>
    <form class="search-form" action="./Sample04-2.php" method="post">
        <input type="text" name="searchText" placeholder="例：Intel、AMD …" />
        <input type="submit" value="検索" />
    </form>
</div>
</body>
</html>