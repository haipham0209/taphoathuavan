<?php
session_start();
include 'db_connection.php'; // Kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    // Kiểm tra xem id có hợp lệ không
    if (isset($id) && is_numeric($id)) {
        $stmt = $conn->prepare("DELETE FROM StoreDescriptions WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa bản ghi.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ.']);
    }
}

$conn->close();
?>