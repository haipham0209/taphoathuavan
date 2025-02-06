

<?php
$servername = "localhost";
$username = "se2a_24_bugs";
$password = "X@7zERHL";
$dbname = "se2a_24_bugs";

$servername = "localhost";
$username = "dbuser";
$password = "ecc";
$dbname = "wearebugs";

// データベース接続の確立
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラー確認
if ($conn->connect_error) {
    die("接続に失敗しました: " . $conn->connect_error);
}
?>