<?php
// Kết nối cơ sở dữ liệu
include('db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ form
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Không cần escape vì sẽ dùng password_verify

    // Kiểm tra xem người dùng có tồn tại không
    $check_sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Nếu người dùng không tồn tại
    if ($result->num_rows === 0) {
        header("Location: ../StoreLogin.php?error=username_not_found&username=" . urlencode($username));
        exit();
    } else {
        // Người dùng tồn tại, lấy dữ liệu
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];  // Mật khẩu đã băm
        $userid = $user["userid"];
        $mail = $user["mail"];
        $status = $user["status"]; // Giả sử trạng thái lưu trong cột 'status'

        // Kiểm tra trạng thái tài khoản
        if ($status === 'pending') {
            header("Location: ../StoreLogin.php?error=account_pending&username=" . urlencode($username));
            exit();
        }

        // Kiểm tra mật khẩu bằng password_verify
        if (password_verify($password, $stored_password)) {
            // Tạo token ngẫu nhiên
            $token = bin2hex(random_bytes(16));

            // Cập nhật token trong cơ sở dữ liệu
            $update_token_sql = "UPDATE user SET token = ? WHERE userid = ?";
            $stmt = $conn->prepare($update_token_sql);
            $stmt->bind_param("si", $token, $userid);
            $stmt->execute();

            // Lấy thông tin cửa hàng dựa trên userid
            $store_sql = "SELECT * FROM store WHERE userid = ?";
            $stmt = $conn->prepare($store_sql);
            $stmt->bind_param("i", $userid);
            $stmt->execute();
            $store_result = $stmt->get_result();

            // Nếu có thông tin cửa hàng
            if ($store_result->num_rows > 0) {
                $store = $store_result->fetch_assoc();
                $storeid = $store["storeid"];
                // session_start();
                // $_SESSION['storeid'] = $storeid;
            }

            // Kiểm tra xem checkbox "ghi nhớ đăng nhập" có được chọn không
            if (isset($_POST['checkbox_remember_account'])) {
                $expire = time() + (86400 * 30); // Cookie có hiệu lực 30 ngày
            } else {
                $expire = 0; // Hết hiệu lực khi đóng trình duyệt
            }

            // Thiết lập cookie
            setcookie('username', $username, $expire, "/");
            setcookie('token', $token, $expire, "/");
            setcookie('loggedin', true, $expire, "/");

            // Chuyển hướng đến trang chính
            header("Location: ../main.php");
            exit();
        } else {
            // Mật khẩu không đúng
            header("Location: ../StoreLogin.php?error=incorrect_password&username=" . urlencode($username));
            exit();
        }
    }

    $stmt->close();
    $conn->close();
} else {
    // echo "POSTではない";
    header("Location: ../error.php?error=nopermission");
}
?>
