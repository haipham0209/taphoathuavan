<?php
// Kết nối cơ sở dữ liệu
include('./db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy token từ URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Kiểm tra xem token có tồn tại và trạng thái là 'pending'
    $sql = "SELECT * FROM user WHERE token = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Kích hoạt tài khoản - chuyển trạng thái sang 'active'
        $update_sql = "UPDATE user SET status = 'active', token = NULL WHERE token = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("s", $token);

        if ($stmt->execute()) {
            // echo "Tài khoản của bạn đã được kích hoạt thành công!";
            header("Location: ../success.html");
            // header("Location: ../StoreLogin.php?success=activated");
            exit();
        } else {
            // echo "Lỗi khi kích hoạt tài khoản.";
            header("Location: ../error.php?error=account_activate_false");
        }
    } else {
        
        // echo "Token không hợp lệ hoặc tài khoản đã được kích hoạt.";
        header("Location: ../error.php?error=invalid_token");
    }

    $stmt->close();
} else {
    // echo "Không có token trong yêu cầu.";
    header("Location: ../error.php?error=nopermission");
}

$conn->close();
?>
