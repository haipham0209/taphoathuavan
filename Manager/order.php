<?php
include('./php/auth_check.php');
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// データベースから注文データを取得
$sql = "
    SELECT 
        o.order_id,
        o.order_number,
        SUM(od.quantity) AS total_quantity,
        o.total_price,
        o.order_date,
        o.status
    FROM orders o
    LEFT JOIN order_details od ON o.order_number = od.order_number
    GROUP BY o.order_id, o.order_number, o.total_price, o.order_date, o.status
";
$result = $conn->query($sql);

// データを配列に格納
$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/order.css">
    <title>Order History</title>
</head>

<body>
    <header>
        <div class="main-navbar">
            <div class="search-scan">
                <input type="text" name="barcode" id="barcode-input" class="search-bar" placeholder="商品名又はコード入力">
                <div id="barcode-suggestions" class="suggestions-list" style="display:none;"></div>
                <img src="./images/camera-icon.png" class="camera-icon" onclick="toggleCamera()">
            </div>
            <a href="main.php">
                <img class="home" src="./images/home.png" alt="Home Mana">
            </a>
        </div>
    </header>
    <main>
        <p class="title">注文履歴</p>
        <div class="date-control">
            <button id="prev-date" class="date-button">◀</button>
            <input type="date" id="date-picker" class="date-picker">
            <button id="next-date" class="date-button">▶</button>
        </div>
        <p id="no-data-message" style="text-align: center; display:none;">データがありません。</p>
        <table class="order-list">
            <thead>
                <tr>
                    <th>番号</th>
                    <th>注文番号</th>
                    <th>数量</th>
                    <th>金額</th>
                </tr>
            </thead>
            <tbody id="order-list-body">
                <!-- Data will be inserted here via JavaScript -->
            </tbody>
        </table>

    </main>
    <footer></footer>
    <script src="./scripts/dateorder.js"></script>
</body>

</html>