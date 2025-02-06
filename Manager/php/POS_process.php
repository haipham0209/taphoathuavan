<?php

function generateOrderNumber($conn, $storeid) {
    // Lấy ngày hiện tại
    $today = date('Y-m-d');

    $month = date('m');
    $day = date('d');

    // Truy vấn để đếm số đơn hàng của cửa hàng hôm nay
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM orders WHERE store_id = ? AND DATE(order_date) = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("is", $storeid, $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Tạo số thứ tự với định dạng 001, 002, ...
    $order_number = str_pad($result['count'] + 1, 3, "0", STR_PAD_LEFT);

    // Tạo mã đơn hàng hoàn chỉnh
    $order_code = $month . $day . $storeid . $order_number;

    // Lưu mã vào session (nếu cần)
    $_SESSION['order_number'] = $order_code;

    return $order_code;
}

// require_once('./db_connect.php');

// Hàm lấy thông tin sản phẩm qua barcode
// function getProductByBarcode($conn, $barcode, $storeid) {
//     $sql = "SELECT pname, price, stock_quantity, productid FROM product WHERE barcode = ? AND storeid = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("si", $barcode, $storeid);
//     $stmt->execute();
//     return $stmt->get_result()->fetch_assoc();
// }

// // Hàm cập nhật số lượng tồn kho
// function updateProductStock($conn, $productid, $quantity) {
//     $sql = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("ii", $quantity, $productid);
//     return $stmt->execute();
// }
?>
