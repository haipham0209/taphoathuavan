<?php
require 'auth_check.php';
require 'db_connect.php';

$date = $_GET['date'];
$storeid = $_SESSION["storeid"];

$sql = "SELECT o.order_number, o.store_id, SUM(od.quantity) as total_quantity, o.total_price
        FROM orders o
        JOIN order_details od ON o.order_number = od.order_number
        WHERE DATE(o.order_date) = ? AND o.store_id = ?
        GROUP BY o.order_number";

$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $date, $storeid);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);

$stmt->close();
$conn->close();
?>

<?php
// require 'db_connect.php';

// $date = $_GET['date'];
// $sql = "SELECT o.order_number, SUM(od.quantity) as total_quantity, o.total_price
//         FROM orders o
//         JOIN order_details od ON o.order_number = od.order_number
//         WHERE DATE(o.order_date) = ?
//         GROUP BY o.order_number";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param('s', $date);
// $stmt->execute();
// $result = $stmt->get_result();

// $orders = [];
// while ($row = $result->fetch_assoc()) {
//     $orders[] = $row;
// }

// echo json_encode($orders);

// $stmt->close();
// $conn->close();
?>