<?php
require 'auth_check.php';
require 'db_connect.php';

$order_number = $_GET['order_number'];
$storeid = $_SESSION["storeid"];

// 注文の詳細データを取得
$sql = "
    SELECT 
        od.order_number, 
        p.pname, 
        od.quantity, 
        od.item_price,
        (od.quantity * od.item_price) AS order_price,
        o.total_price,
        o.received_amount,
        o.discount
    FROM order_details od
    JOIN product p ON od.productid = p.productid
    JOIN orders o ON od.order_number = o.order_number
    WHERE od.order_number = ? AND p.storeid = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $order_number, $storeid);
$stmt->execute();
$result = $stmt->get_result();

$orderDetails = [];
while ($row = $result->fetch_assoc()) {
    $orderDetails[] = $row;
}

echo json_encode($orderDetails);

$stmt->close();
$conn->close();
