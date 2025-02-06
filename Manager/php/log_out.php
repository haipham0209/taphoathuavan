<?php
// if (isset($_COOKIE['username'])) {
//     setcookie('username', '', time() - 86400, "/");
// }
if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 86400, "/");
}
if (isset($_COOKIE['userid'])) {
    setcookie('userid', '', time() - 86400, "/");
}
if (isset($_COOKIE['loggedin'])) {
    setcookie('loggedin', false, time() + (8640000 * 30), "/");
}
// session_start();
session_unset();  // Xóa tất cả biến session
session_destroy();  // Hủy session hiện tại

// Chuyển hướng về main.php
header("Location: ../StoreLogin.php?username=" . urlencode($_COOKIE['username']));

exit();
?>