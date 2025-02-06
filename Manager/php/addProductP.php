<?php
include('auth_check.php');
include('db_connect.php');
// $conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Lấy thông tin người dùng
$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

// Lấy storeid dựa trên userid
$store_query = "SELECT storeid FROM store WHERE userid = ?";
$stmt = $conn->prepare($store_query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$stmt->bind_result($storeid);
$stmt->fetch();
$stmt->close();

// Kiểm tra nếu có sản phẩm được thêm vào
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pname = trim($_POST['pname']);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $costPrice = filter_input(INPUT_POST, 'costPrice', FILTER_VALIDATE_FLOAT);
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $stockQuantity = filter_input(INPUT_POST, 'stockQuantity', FILTER_VALIDATE_INT);
    $barcode = trim($_POST['barcode']);
    $category = $_POST['category']; // Lấy giá trị lựa chọn danh mục
    $category_id = null;

    // Kiểm tra tính duy nhất của tên sản phẩm và mã barcode
    $check_product_sql = "SELECT productid FROM product WHERE (pname = ? OR barcode = ?) AND storeid = ?";
    $stmt = $conn->prepare($check_product_sql);
    $stmt->bind_param("ssi", $pname, $barcode, $storeid);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // echo "Tên sản phẩm hoặc mã barcode đã tồn tại.";//////////
        header("Location: ../productAdd.php?error=exist&productname=" . urlencode($pname). "&price=" . urlencode($price). "&costprice=" . urlencode($costPrice). "&description=" . urlencode($description). "&stock=" . urlencode($stockQuantity). "&barcode=" . urlencode($barcode));
        exit();
    }
    $stmt->close();

    // Lấy tên danh mục nếu người dùng chọn "新しいカテゴリーを追加"
    if ($category === "new") {
        if (!empty($_POST['categoryText'])) {
            // $category_name = strtolower(trim($_POST['categoryText']));
            $category_name = trim($_POST['categoryText']);


            // Kiểm tra nếu danh mục mới đã tồn tại
            $check_category_sql = "SELECT category_id FROM category WHERE LOWER(cname) = ? AND storeid = ?";
            $stmt = $conn->prepare($check_category_sql);
            $stmt->bind_param("si", $category_name, $storeid);
            $stmt->execute();
            $stmt->store_result();

            // if ($stmt->num_rows === 0) {
            //      // Nếu không tồn tại, thêm mới danh mục
            //      $insert_category_sql = "INSERT INTO category (category_id, storeid, cname) VALUES (?, ?, ?)";
            //      // Xác định category_id mới
            //      $new_category_id = $conn->insert_id;
            //      $stmt = $conn->prepare($insert_category_sql);
            //      $stmt->bind_param("iis", $new_category_id, $storeid, $category_name);
            //      $stmt->execute();
            //      $category_id = $new_category_id;
            // } else {
            //     // Lấy ID danh mục nếu đã tồn tại
            //     $stmt->bind_result($category_id);
            //     $stmt->fetch();
            // }
            if ($stmt->num_rows === 0) {
                // Xác định `category_id` mới dựa trên giá trị cao nhất hiện tại
                $max_category_id_sql = "SELECT IFNULL(MAX(category_id), 0) + 1 AS next_id FROM category WHERE storeid = ?";
                $stmt_max = $conn->prepare($max_category_id_sql);
                $stmt_max->bind_param("i", $storeid);
                $stmt_max->execute();
                $stmt_max->bind_result($new_category_id);
                $stmt_max->fetch();
                $stmt_max->close();
                
                // Chèn danh mục mới với `category_id` mới xác định
                $insert_category_sql = "INSERT INTO category (category_id, storeid, cname) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_category_sql);
                $stmt->bind_param("iis", $new_category_id, $storeid, $category_name);
                $stmt->execute();
                $category_id = $new_category_id;
            } else {
                // Lấy ID danh mục nếu đã tồn tại
                $stmt->bind_result($category_id);
                $stmt->fetch();
            }
            $stmt->close();
        } else {
            // die("Vui lòng nhập tên danh mục mới.");///////////////////////////
            
            header("Location: ../productAdd.php?error=notexistcate&productname=" . urlencode($pname). "&price=" . urlencode($price). "&costprice=" . urlencode($costPrice). "&description=" . urlencode($description). "&stock=" . urlencode($stockQuantity). "&barcode=" . urlencode($barcode));
        }
    } else {
        // Lấy `category_id` và `cname` nếu chọn một danh mục đã có
        $category_id = $category;
        $category_name_sql = "SELECT cname FROM category WHERE category_id = ? AND storeid = ?";
        $stmt = $conn->prepare($category_name_sql);
        $stmt->bind_param("ii", $category_id, $storeid);
        $stmt->execute();
        $stmt->bind_result($category_name);
        $stmt->fetch();
        $stmt->close();
    }

    // Tạo thư mục cho người dùng
    $user_folder = "../storeproductImg/" . $username;
    if (!file_exists($user_folder)) {
        mkdir($user_folder, 0777, true);
    }

    // Tạo thư mục cho danh mục
    $category_folder = $user_folder . "/" . $category_name;
    if (!file_exists($category_folder)) {
        mkdir($category_folder, 0777, true);
    }
    // Kiểm tra nếu có lỗi tải lên ảnh
    if ($_FILES['productImage']['error'] !== UPLOAD_ERR_OK) {
        // echo "Lỗi khi tải lên ảnh!";
        header("Location: ../productAdd.php?error=imgerrort&productname=" . urlencode($pname). "&price=" . urlencode($price). "&costprice=" . urlencode($costPrice). "&description=" . urlencode($description). "&stock=" . urlencode($stockQuantity). "&barcode=" . urlencode($barcode));
        
        //////////////////hai///////////////////////
        // switch ($_FILES['productImage']['error']) {
        //     case UPLOAD_ERR_INI_SIZE:
        //         echo "Lỗi: Kích thước file vượt quá giới hạn upload_max_filesize trong php.ini.";
        //         break;
        //     case UPLOAD_ERR_FORM_SIZE:
        //         echo "Lỗi: Kích thước file vượt quá giới hạn MAX_FILE_SIZE trong form HTML.";
        //         break;
        //     case UPLOAD_ERR_PARTIAL:
        //         echo "Lỗi: File chỉ được tải lên một phần.";
        //         break;
        //     case UPLOAD_ERR_NO_FILE:
        //         echo "Lỗi: Không có file nào được tải lên.";
        //         break;
        //     case UPLOAD_ERR_NO_TMP_DIR:
        //         echo "Lỗi: Thiếu thư mục tạm.";
        //         break;
        //     case UPLOAD_ERR_CANT_WRITE:
        //         echo "Lỗi: Không thể ghi file vào đĩa.";
        //         break;
        //     case UPLOAD_ERR_EXTENSION:
        //         echo "Lỗi: Upload bị dừng bởi một phần mở rộng PHP.";
        //         break;
        //     default:
        //         echo "Lỗi không xác định khi tải lên ảnh.";
        //         break;
        // }

        ////////////////////////hai//////////////////////////////////
        exit();
    }

    // Tạo tên file duy nhất để lưu ảnh
    $imageExtension = pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION);
    $uniqueImageName = $storeid . "_" . time() . "." . $imageExtension;
    $imagePath = $category_folder . "/" . $uniqueImageName;

    // Khởi tạo giá trị mặc định cho $max_productid
    $max_productid = 0;

    // Tìm `productid` tiếp theo cho cửa hàng hiện tại
    $max_productid_sql = "SELECT IFNULL(MAX(productid), 0) AS max_id FROM product WHERE storeid = ?";
    $stmt = $conn->prepare($max_productid_sql);
    $stmt->bind_param("i", $storeid);

    if ($stmt->execute()) {
        $stmt->bind_result($max_productid);
        $stmt->fetch();
    }
    $stmt->close();

    // Tăng productid lên 1
    $next_productid = $max_productid + 1;
    $dbImagePath =  "./Manager/storeproductImg/" . $username . "/" . $category_name . "/" . $uniqueImageName;
    // Thêm sản phẩm vào bảng product
    $stmt = $conn->prepare("INSERT INTO product (productid, storeid, category_id, pname, price, costPrice, description, stock_quantity, barcode, productImage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissssiss", $next_productid, $storeid, $category_id, $pname, $price, $costPrice, $description, $stockQuantity, $barcode, $dbImagePath);

    if ($stmt->execute()) {
          // Nếu thêm sản phẩm thành công, lưu ảnh vào thư mục
          if (move_uploaded_file($_FILES['productImage']['tmp_name'], $imagePath)) {
            // echo "Sản phẩm đã được thêm thành công!";
            echo '
<div style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
    <!-- Hiển thị hiệu ứng Lottie -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest"></script>
    <lottie-player src="../images/success.json" background="transparent" speed="1.8" style="width: 250px; height: 250px;" autoplay></lottie-player>
    <p style="font-size: 2.5em; font-weight: bold; margin-top: 20px;">商品追加しました、しばらくお待ちください。...</p>
</div>

<script>
    // Trì hoãn 3 giây trước khi chuyển hướng
    setTimeout(function() {
        window.location.href = "../product.php";
    }, 2700); // 3000ms = 3 giây
</script>

        ';
            // header("Location: ../productAdd.php");

        } else {
            // echo "Lỗi khi lưu ảnh!";
            // Nếu lưu ảnh thất bại, xoá sản phẩm vừa thêm khỏi cơ sở dữ liệu
            $delete_stmt = $conn->prepare("DELETE FROM product WHERE productid = ? AND storeid = ?");
            $delete_stmt->bind_param("ii", $next_productid, $storeid);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
    } else {
        // echo "Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.";

        header("Location: ../../error.php?errror=DB_ERROR");
    }

    $stmt->close();
}else{
    header("Location: ../../error.php");
}

$conn->close();
?>
