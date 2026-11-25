<?php
session_start();
$gid = trim($_POST["gid"] ?? "");
$num = trim($_POST["num"] ?? "");

// ① 未入力チェック（最優先）
if ($gid === "" || $num === "") {
    $_SESSION["errMsg"] = "未入力箇所があります";
    header("Location: ./Sample06-1.php");
    exit();
}

// ② 個数が数字（半角数字）かチェック
if (!is_numeric($num)) {
    $_SESSION["errMsg"] = "個数は数字で入力してください";
    header("Location: ./Sample06-1.php");
    exit();
}

require_once("dbconf.inc");

$mysqli = mysqli_connect(DB_URL, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit();
}

$mysqli->select_db(DB_NAME);

// 商品を検索（Stockも取得）
$gid_safe = (int)$gid;
$result = $mysqli->query("SELECT * FROM Goods WHERE GoodsID={$gid_safe}");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // ③ 在庫数チェック
    if ((int)$num > (int)$row["Stock"]) {
        $_SESSION["errMsg"] = "在庫数が足りません";
        header("Location: ./Sample06-1.php");
        exit();
    }

    // すべてOKならセッションに保存して計算画面へ
    $_SESSION["gid"] = $gid_safe;
    $_SESSION["num"] = (int)$num;
    header("Location: ./Sample06-3.php");
    exit();

} else {
    // ④ 商品が存在しない場合
    $_SESSION["errMsg"] = "その商品IDは存在しません";
    header("Location: ./Sample06-1.php");
    exit();
}

$result->free();
$mysqli->close();
?>