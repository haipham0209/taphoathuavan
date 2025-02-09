<?php
session_start();
include(__DIR__ . '/db_connect.php'); // データベース接続ファイルをインクルード

// ユーザーがログインしているか確認
// if (!isset($_SESSION['userid'])) {
//     header("Location: login.php");
//     exit;
// }
include('db_connect.php');
// パスワードが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // データベースからユーザーのパスワードを取得
    $stmt = $conn->prepare("SELECT password FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();
    
    // Kiểm tra mật khẩu
    if (password_verify($password, $hashedPassword)) {
    
        // Lấy storeid từ bảng store
        $storeStmt = $conn->prepare("SELECT storeid FROM store WHERE userid = ?");
        $storeStmt->bind_param("i", $userid);
        $storeStmt->execute();
        $storeStmt->bind_result($storeid);
        $storeStmt->fetch();
        $storeStmt->close();
    
        if ($storeid) {
            // todo：Gửi email xác nhận xóa tài khoản
    
            // Xóa order_details trước để tránh lỗi khóa ngoại
            $deleteOrderDetailsStmt = $conn->prepare("DELETE FROM order_details WHERE order_number IN (SELECT order_number FROM orders WHERE store_id = ?)");
            $deleteOrderDetailsStmt->bind_param("i", $storeid);
            $deleteOrderDetailsStmt->execute();
            $deleteOrderDetailsStmt->close();
    
            // Xóa dữ liệu trong bảng orders
            $deleteOrdersStmt = $conn->prepare("DELETE FROM orders WHERE store_id = ?");
            $deleteOrdersStmt->bind_param("i", $storeid);
            $deleteOrdersStmt->execute();
            $deleteOrdersStmt->close();
    
            // Xóa dữ liệu trong bảng daily_revenue
            $deleteDailyRevenueStmt = $conn->prepare("DELETE FROM daily_revenue WHERE store_id = ?");
            $deleteDailyRevenueStmt->bind_param("i", $storeid);
            $deleteDailyRevenueStmt->execute();
            $deleteDailyRevenueStmt->close();
    
            // Xóa dữ liệu trong bảng discounts
            $deleteDiscountsStmt = $conn->prepare("DELETE FROM discounts WHERE productid IN (SELECT productid FROM product WHERE storeid = ?)");
            $deleteDiscountsStmt->bind_param("i", $storeid);
            $deleteDiscountsStmt->execute();
            $deleteDiscountsStmt->close();
    
            // Xóa dữ liệu trong bảng product
            $deleteProductStmt = $conn->prepare("DELETE FROM product WHERE storeid = ?");
            $deleteProductStmt->bind_param("i", $storeid);
            $deleteProductStmt->execute();
            $deleteProductStmt->close();
    
            // Xóa dữ liệu trong bảng category
            $deleteCategoryStmt = $conn->prepare("DELETE FROM category WHERE storeid = ?");
            $deleteCategoryStmt->bind_param("i", $storeid);
            $deleteCategoryStmt->execute();
            $deleteCategoryStmt->close();
    
            // Xóa dữ liệu trong bảng StoreDescriptions
            $deleteStoreDescriptionsStmt = $conn->prepare("DELETE FROM StoreDescriptions WHERE storeid = ?");
            $deleteStoreDescriptionsStmt->bind_param("i", $storeid);
            $deleteStoreDescriptionsStmt->execute();
            $deleteStoreDescriptionsStmt->close();
    
            // Xóa dữ liệu trong bảng store
            $deleteStoreStmt = $conn->prepare("DELETE FROM store WHERE userid = ?");
            $deleteStoreStmt->bind_param("i", $userid);
            $deleteStoreStmt->execute();
            $deleteStoreStmt->close();
        }
    
        // Xóa tài khoản user sau khi đã xóa tất cả dữ liệu liên quan
        $deleteUserStmt = $conn->prepare("DELETE FROM user WHERE userid = ?");
        $deleteUserStmt->bind_param("i", $userid);
        $deleteUserStmt->execute();
        $deleteUserStmt->close();
    
        // Xóa session và chuyển hướng về trang đăng nhập
        session_destroy();
        header("Location: ../StoreLogin.php");
        exit;
    } else {
        header("Location: ../profileEdit.php?error=invalid_password");
        exit;
    }
    
}
