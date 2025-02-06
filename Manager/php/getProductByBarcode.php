<?php
// Kết nối cơ sở dữ liệu
include('auth_check.php');
include('db_connect.php');
// Kiểm tra kết nối
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Lấy dữ liệu từ client
$data = json_decode(file_get_contents('php://input'), true);
$barcode = $data['barcode'];


// Truy vấn sản phẩm dựa trên mã vạch
$sql = "SELECT * FROM product WHERE barcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $barcode);
$stmt->execute();
$result = $stmt->get_result();

// Trả về kết quả
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode($product);
} else {
    echo json_encode(['error' => 'Product not found']);
    // header("Location: ../error.php?error=nopermission");
}

$stmt->close();
$conn->close();
?>
