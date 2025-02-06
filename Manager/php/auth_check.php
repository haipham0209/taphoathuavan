<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Thông tin kết nối cơ sở dữ liệu
include('db_connect.php');

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("SERVER NOT FOUND");
}

// Kiểm tra sự tồn tại của cookie
if (isset($_COOKIE['username']) && isset($_COOKIE['token'])) {
    $user_id = $_COOKIE['username'];
    $token = $_COOKIE['token'];

    // Truy vấn thông tin người dùng dựa trên username và token
    $user_sql = "SELECT * FROM user WHERE username = ? AND token = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("ss", $user_id, $token);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows === 0) {
        // Token không hợp lệ, chuyển về trang đăng nhập
        // header("Location: StoreLogin.php?error=invalid_token");
        header("Location: ../Manager/sessionout.php");
        exit();
    }

    // ------------------------------------Lưu tất cả thông tin người dùng vào session---------------------------------------------------------------------
    $user = $user_result->fetch_assoc();
    foreach ($user as $key => $value) {
        $_SESSION[$key] = $value;
    }

    // Truy vấn thông tin cửa hàng dựa trên userid
    $store_sql = "SELECT * FROM store WHERE userid = ?";
    $stmt = $conn->prepare($store_sql);
    $stmt->bind_param("i", $user['userid']);
    $stmt->execute();
    $store_result = $stmt->get_result();
//-------------------------------------- Lưu tất cả thông tin cửa hàng vào session--------------------------------------------------------------------------
    if ($store_result->num_rows > 0) {
        // Lưu thông tin cửa hàng vào session
        $store = $store_result->fetch_assoc();
        foreach ($store as $key => $value) {
            $_SESSION[$key] = $value;
        }
    } else {
        $_SESSION['store_error'] = "Không tìm thấy thông tin cửa hàng.";
    }

    // Đóng tài nguyên
    $stmt->close();
    $conn->close();
} else {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();  // Xóa tất cả biến session
    session_destroy();  // Hủy session hiện tại
    // Nếu không có cookie, chuyển về trang đăng nhập
    // header("Location: StoreLogin.php?error=auth_check");
    header("Location: ../Manager/sessionout.php");
    exit();
}
?>
