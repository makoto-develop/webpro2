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

        section + section {
            margin-top: 40px;
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

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        label {
            font-weight: 700;
        }

        input[type="text"] {
            width: min(320px, 100%);
            padding: 9px 10px;
            border: 1px solid #aeb8b1;
            border-radius: 4px;
            font: inherit;
        }

        input[type="submit"] {
            padding: 9px 18px;
            border: 0;
            border-radius: 4px;
            background: #078a45;
            color: #fff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #056d36;
        }

        .category-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .category-list li + li {
            margin-top: 8px;
        }

        .category-list a {
            color: #1269b0;
            text-decoration: none;
        }

        .category-list a:hover {
            text-decoration: underline;
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
            <h2>商品検索</h2>
            <p>
                商品名の一部または全てを入力してください。
                空白のままで全商品を表示します。
            </p>

            <form class="search-form" method="get">
                <label for="goods_name">商品名：</label>
                <input type="text" id="goods_name" name="goods_name" placeholder="商品名を入力してください">
                <input type="submit" value="検索">
            </form>
        </section>

        <section>
            <h2>カテゴリ検索</h2>
            <p>お探しの商品の種類をクリックしてください。</p>
            <?php
            $mysqli = mysqli_connect('mysql', 'user1', 'user1password');
            //$mysqli = mysqli_connect('mysql403.phy.lolipop.lan', 'LAA1710026', 'rootpassword');
            if ($mysqli->connect_errno) {
                echo $mysqli->connect_error;
                exit();
            }
            $mysqli->select_db('webshop');
            //$mysqli->select_db('LAA1710026-webshop');
            $result = $mysqli->query("SELECT * FROM GoodsCategory");
            echo '<ul class="category-list">';

            while ($row = $result->fetch_assoc()) {
                $categoryName = htmlspecialchars(
                        $row['CategoryName'],
                        ENT_QUOTES,
                        'UTF-8'
                );

                echo '<li><a href="?category=' . urlencode($row['CategoryName']) . '">';
                echo $categoryName;
                echo '</a></li>';
            }

            echo '</ul>';

            $result->free();
            $mysqli->close();
            ?>
        </section>
    </main>
</div>
</body>
</html>