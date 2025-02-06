<?php
include('auth_check.php');
include('db_connect.php');

$productid = $_GET['id'] ?? null;

if ($productid) {
    // Lấy category_id của sản phẩm trước khi xóa
    $sql_get_category = "SELECT category_id FROM product WHERE productid = ? AND storeid = ?";
    $stmt_get_category = $conn->prepare($sql_get_category);
    $stmt_get_category->bind_param("ii", $productid, $_SESSION['storeid']);
    $stmt_get_category->execute();
    $result = $stmt_get_category->get_result();
    $category_id = $result->fetch_assoc()['category_id'] ?? null;

    // Xóa dữ liệu liên quan trong order_details
    $sql_delete_order_details = "DELETE FROM order_details WHERE productid = ?";
    $stmt_delete_order_details = $conn->prepare($sql_delete_order_details);
    $stmt_delete_order_details->bind_param("i", $productid);
    $stmt_delete_order_details->execute();
    $stmt_delete_order_details->close();


    // Tiến hành xóa sản phẩm
    $sql = "DELETE FROM product WHERE productid = ? AND storeid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productid, $_SESSION['storeid']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Kiểm tra nếu danh mục không còn sản phẩm nào thì xóa luôn danh mục
        $sql_check_category = "SELECT COUNT(*) as product_count FROM product WHERE category_id = ? AND storeid = ?";
        $stmt_check_category = $conn->prepare($sql_check_category);
        $stmt_check_category->bind_param("ii", $category_id, $_SESSION['storeid']);
        $stmt_check_category->execute();
        $result_check = $stmt_check_category->get_result();
        $product_count = $result_check->fetch_assoc()['product_count'];

        if ($product_count == 0) {
            // Nếu không còn sản phẩm nào, xóa danh mục
            $sql_delete_category = "DELETE FROM category WHERE category_id = ? AND storeid = ?";
            $stmt_delete_category = $conn->prepare($sql_delete_category);
            $stmt_delete_category->bind_param("ii", $category_id, $_SESSION['storeid']);
            $stmt_delete_category->execute();
            $stmt_delete_category->close();
        }

        // Chuyển hướng về trang product.php
        header("Location: ../product.php");
    } else {
        echo "Không thể xóa sản phẩm.";
    }
    
    $stmt->close();
    $stmt_get_category->close();
    $stmt_check_category->close();
} else {
    // echo "ID sản phẩm không hợp lệ.";
    header("Location: ../error.php?error=nopermission");
}

$conn->close();
?>
