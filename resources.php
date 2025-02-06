<?php
// Kết nối cơ sở dữ liệu
include('./Manager/php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Tạo mảng categories để lưu trữ sản phẩm theo category
$categories = [];

// Truy vấn sản phẩm và thông tin danh mục theo storeid
$productQuery = "
    SELECT p.productid, p.pname, p.price, p.discounted_price, p.productImage, 
           c.cname
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    WHERE p.storeid = ? AND c.storeid = ?
";
$productStmt = $conn->prepare($productQuery);
$productStmt->bind_param("ii", $storeid, $storeid);  // Truyền storeid 2 lần vào câu truy vấn
$productStmt->execute();
$productResult = $productStmt->get_result();

if ($productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $categoryName = $row['cname'];

        // Kiểm tra nếu danh mục chưa có trong categories, tạo mới
        if (!isset($categories[$categoryName])) {
            $categories[$categoryName] = [
                'cname' => $categoryName,
                'products' => []
            ];
        }

        // Thêm sản phẩm vào danh mục tương ứng
        $categories[$categoryName]['products'][] = [
            'productid' => $row['productid'],
            'pname' => $row['pname'],
            'price' => $row['price'],
            'discounted_price' => $row['discounted_price'],
            'productImage' => $row['productImage']
        ];
    }
}

// Đóng kết nối
$productStmt->close();
$conn->close();
?>
