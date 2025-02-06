<?php
// Kết nối cơ sở dữ liệu
include('auth_check.php');
include('db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm từ yêu cầu AJAX
$keyword = $_GET['keyword'] ?? '';
$storeid = $_SESSION['storeid'];
if ($keyword !== '') {
    // Truy vấn sản phẩm theo mã sản phẩm hoặc tên sản phẩm
    $sql = "SELECT *
            FROM product 
            WHERE storeid = ? 
            AND (barcode LIKE ? OR pname LIKE ?) 
            LIMIT 5;";
    $stmt = $conn->prepare($sql);

    $searchTerm = '%' . $keyword . '%';
    $stmt->bind_param("iss", $storeid, $searchTerm, $searchTerm); // Sửa lại đúng số tham số và kiểu
    $stmt->execute();
    $result = $stmt->get_result();

    // Tạo danh sách sản phẩm để trả về
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Thêm dấu "../" vào đầu đường dẫn ảnh
        $row['productImage'] = '../' . ltrim($row['productImage'], './'); // Loại bỏ dấu ./ nếu có và thêm ../ vào đầu
        
        $products[] = $row;
    }

    // Trả về JSON
    echo json_encode($products);
}

// Đóng kết nối
$conn->close();
?>
