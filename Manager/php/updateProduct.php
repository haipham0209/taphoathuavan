<?php
include('auth_check.php');
include('db_connect.php');

$productid = $_POST['productid'];
$storeid = $_SESSION['storeid'];
$category = $_POST['category'];
$category_name = $_POST['categoryText']; // Tên danh mục nếu tạo mới
// Lưu lại category_id cũ trước khi thay đổi
$old_category_sql = "SELECT category_id FROM product WHERE productid = ? AND storeid = ?";
$old_stmt = $conn->prepare($old_category_sql);
$old_stmt->bind_param("ii", $productid, $storeid);
$old_stmt->execute();
$old_stmt->bind_result($old_category_id);
$old_stmt->fetch();
$old_stmt->close();


// print_r($_POST);
// exit();

// Kiểm tra nếu danh mục chưa tồn tại trong cửa hàng, thì thêm mới
if ($_POST["category"] === "new") {
    // Lấy category_id tiếp theo
    $max_category_id_sql = "SELECT IFNULL(MAX(category_id), 0) + 1 AS next_id FROM category WHERE storeid = ?";
    $stmt_max = $conn->prepare($max_category_id_sql);
    $stmt_max->bind_param("i", $storeid);
    $stmt_max->execute();
    $stmt_max->bind_result($new_category_id);
    $stmt_max->fetch();
    $stmt_max->close();

    // Thêm mới danh mục vào bảng category
    $insert_category_sql = "INSERT INTO category (storeid, category_id, cname) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_category_sql);
    $insert_stmt->bind_param("iis", $storeid, $new_category_id, $category_name);
    $insert_stmt->execute();
    $category = $new_category_id; // Lấy ID của danh mục mới
    $insert_stmt->close();
}

// Cập nhật sản phẩm với các trường được phép thay đổi
$sql = "UPDATE product SET category_id =?, pname = ?, price = ?, costPrice = ?, description = ?, stock_quantity = ?, category_id = ? WHERE productid = ? AND storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isddsiiii", $_POST['category'], $_POST['pname'], $_POST['price'], $_POST['costPrice'], $_POST['description'], $_POST['stockQuantity'], $category, $productid, $storeid);
$stmt->execute();

// Kiểm tra và xóa danh mục nếu cần
if ($stmt->affected_rows > 0) {
    // Kiểm tra nếu category cũ không còn sản phẩm nào
    $check_category_sql = "SELECT COUNT(*) FROM product WHERE category_id = ?";
    $check_stmt = $conn->prepare($check_category_sql);
    $check_stmt->bind_param("i", $old_category_id);
    $check_stmt->execute();
    $check_stmt->bind_result($product_count);
    $check_stmt->fetch();
    $check_stmt->close();

    // Nếu không còn sản phẩm nào, xóa category cũ
    if ($product_count == 0) {
        $delete_category_sql = "DELETE FROM category WHERE category_id = ?";
        $delete_stmt = $conn->prepare($delete_category_sql);
        $delete_stmt->bind_param("i", $old_category_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    }

    // Chuyển hướng sau khi cập nhật thành công
    header("Location: ../product.php?status=success");
    exit();
} else {
    // echo "Cập nhật không thành công.";
    header("Location: ../error.php");
}

$stmt->close();
$conn->close();
?>


