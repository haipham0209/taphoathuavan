<?php
// connect

// include('db_connect.php');
include('auth_check.php');
include('db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Giả sử bạn đã lưu storeid vào session khi người dùng đăng nhập

if (isset($_SESSION['storeid'])) {
    $storeid = $_SESSION['storeid'];

    // Truy vấn dữ liệu cửa hàng dựa vào storeid
    $store_sql = "SELECT * FROM store WHERE storeid = ?";
    $stmt = $conn->prepare($store_sql);
    $stmt->bind_param("i", $storeid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lấy dữ liệu cửa hàng
        $store = $result->fetch_assoc();
        // Lưu thông tin vào session
        $_SESSION['sname'] = $store['sname']; // Tên cửa hàng
        $_SESSION['address'] = $store['address']; // Địa chỉ cửa hàng
        $_SESSION['tel'] = $store['tel']; // Số điện thoại
        $_SESSION['description'] = $store['description']; // Mô tả
        $_SESSION['logopath'] = $store['logopath']; // Mô tả
        // Có thể thêm thông tin khác nếu cần
    } else {
        header("Location: ./error.php?storeinfo36");
    }

    $stmt->close();
} else {
    header("Location: ./error.php?StoreIDnotfoundinsession");
}

$conn->close();
?>
