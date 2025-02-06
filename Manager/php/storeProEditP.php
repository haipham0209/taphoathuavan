<?php
// Kết nối cơ sở dữ liệu
include('db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = (int)$_POST['userid']; // Lấy userid từ form
    $address = $conn->real_escape_string($_POST['address']);
    $tel = $conn->real_escape_string($_POST['phone']);
    $currentLogoPath = $_POST['currentLogoPath'] ?? null; // Logo path cũ
    $logopath = $currentLogoPath;
    $targetPath = $logopath;

    // Xử lý upload file logo
    if (isset($_FILES['logoFile']) && $_FILES['logoFile']['error'] === UPLOAD_ERR_OK) {
        $directory = '../uploads/logos/';
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                die("Không thể tạo thư mục '$directory'.");
            }
        }

        $uploadDir = '../uploads/logos/';
        $fileName = basename($_FILES['logoFile']['name']);
        $targetPath = $uploadDir . time() . '_' . $fileName;

        // Kiểm tra định dạng file (chỉ cho phép ảnh)
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['logoFile']['tmp_name']);
        if (in_array($fileType, $allowedMimeTypes)) {
            if (move_uploaded_file($_FILES['logoFile']['tmp_name'], $targetPath)) {
                
                $logopath = $targetPath; // Đường dẫn file mới lưu trong database
            } else {
                header("Location: ../error.php?uploadfail");
                exit();
            }
        } else {
            header("Location: ../error.php?invalidfile");
            exit();
        }
    }

    // Kiểm tra xem userid có hợp lệ không
    $check_user_sql = "SELECT * FROM user WHERE userid = ?";
    $stmt = $conn->prepare($check_user_sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../error.php?uname_not_exist");
        exit();
    }

    // Kiểm tra xem có bản ghi nào trong bảng store không
    $check_store_sql = "SELECT * FROM store WHERE userid = ?";
    $stmt = $conn->prepare($check_store_sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $logopath = str_replace('../uploads/', '../Manager/uploads/', $targetPath);
        // Nếu có, thực hiện UPDATE
        $update_sql = "UPDATE store SET address = ?, tel = ?, logopath = ? WHERE userid = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $address, $tel, $logopath, $userid);

        if ($stmt->execute()) {
            header("Location: ../profileEdit.php");
        } else {
            header("Location: ../error.php?updateerror");
        }
    } else {
        $logopath = str_replace('../uploads/', '../Manager/uploads/', $targetPath);
        // Nếu không có, thực hiện INSERT
        $insert_sql = "INSERT INTO store (userid, address, tel, logopath) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("isss", $userid, $address, $tel, $logopath);

        if ($stmt->execute()) {
            header("Location: ../profileEdit.php");
        } else {
            header("Location: ../error.php?storeProEditP");
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../error.php?notPost");
}
?>
