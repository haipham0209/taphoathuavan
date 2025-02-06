<?php
$password = '123456';
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
echo $hashed_password;
?>
<?php
session_start();

// Kiểm tra nếu có dữ liệu trong session
if (isset($_SESSION)) {
    // Hiển thị tất cả các dữ liệu trong session
    echo '<pre>'; // Để định dạng dễ đọc hơn
    print_r($_SESSION); // Hoặc sử dụng var_dump($_SESSION);
    echo '</pre>';
} else {
    echo "Session không có dữ liệu.";
}
?>
