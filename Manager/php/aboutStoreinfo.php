<?php
session_start();
// Kết nối cơ sở dữ liệu
include('db_connect.php');

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['storeid'])) {
        die(json_encode(['success' => false, 'message' => 'Store ID is not set in the session']));
    }

    $storeid = $_SESSION['storeid']; // Lấy storeid từ session
    $delete_ids = isset($_POST['delete_ids']) ? json_decode($_POST['delete_ids'], true) : [];
    $response = ['success' => false];

    // Xóa các bản ghi có ID trong delete_ids
    if (!empty($delete_ids)) {
        $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM StoreDescriptions WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($delete_ids)), ...$delete_ids);
        $stmt->execute();
        $stmt->close();
    }

    // Duyệt qua từng cặp title-content
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'title') === 0) {
            $index = str_replace('title', '', $key);
            $title = $conn->real_escape_string($value);
            $content = isset($_POST["content$index"]) ? $conn->real_escape_string($_POST["content$index"]) : '';
            $id = isset($_POST["id$index"]) ? $_POST["id$index"] : null;
    
            // Nếu có ID thì cập nhật
            if ($id) {
                $stmt = $conn->prepare("UPDATE StoreDescriptions SET title = ?, content = ? WHERE id = ?");
                $stmt->bind_param('ssi', $title, $content, $id);
                $stmt->execute();
                $stmt->close();
            } else {
                // Nếu không có ID (chắc chắn là thêm mới)
                $stmt = $conn->prepare("INSERT INTO StoreDescriptions (storeid, title, content) VALUES (?, ?, ?)");
                $stmt->bind_param('iss', $storeid, $title, $content);
                $stmt->execute();
                $stmt->close();
            }
        }
    }    


    // Chuyển hướng về trang profileEdit.php sau khi lưu thành công
    header('Location: ../profileEdit.php');
    exit;
}
?>