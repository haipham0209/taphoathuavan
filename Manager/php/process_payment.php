<?php
include('auth_check.php');
include('db_connect.php');
// include('profit.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Khởi tạo giao dịch
$conn->set_charset("utf8");

$customer_id = null;  // Lấy customer_id từ session, nếu có
$store_id = $_SESSION['storeid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu JSON từ php://input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Lấy dữ liệu từ JSON
    $total_price = $data['total_price'] ?? 0;
    $received_amount = $data['received_amount'] ?? 0;
    $products = $data['products'] ?? [];

    // Kiểm tra dữ liệu nhận được
    if (empty($products)) {
        echo json_encode(['success' => false, 'error' => '操作違います']);
        exit;
    }

    if ($total_price <= 0 || $received_amount < $total_price) {
        echo json_encode(['success' => false, 'error' => '金額不足している']);
        exit;
    }

    try {
        // Bắt đầu giao dịch
        $conn->begin_transaction();

        $order_number = $_SESSION['order_number'];
        ///////////////////////////////kiem tra ma don hang
        // Kiểm tra xem order_number đã tồn tại hay chưa
        $stmt_check_order = $conn->prepare("SELECT COUNT(*) FROM orders WHERE order_number = ?");
        $stmt_check_order->bind_param("s", $order_number);
        $stmt_check_order->execute();
        $stmt_check_order->bind_result($order_count);
        $stmt_check_order->fetch();
        $stmt_check_order->free_result();

        if ($order_count > 0) {
            echo json_encode(['success' => false, 'error' => '解決済みのオーダーです。CLEARしてください']); // Order number already exists
            $conn->rollback();
            exit;
        }

        ///////////////////////////////////////////////////////////////
        // echo $order_number;
        // Lưu đơn hàng vào bảng orders
        // $total_discount = $_COOKIE['totalDiscount'];
        $total_discount = 0;  // 総割引額を初期化

        foreach ($products as $item) {
            $barcode = $item['barcode'];
            $quantity = $item['quantity'];

            // 商品の情報を取得
            $stmt = $conn->prepare("SELECT price, discounted_price FROM product WHERE barcode = ? AND storeid = ?");
            $stmt->bind_param("si", $barcode, $store_id);
            $stmt->execute();
            $stmt->bind_result($price, $discounted_price);
            $stmt->fetch();
            $stmt->free_result();

            if ($price !== null && $discounted_price !== null) {
                $discount_value = $price - $discounted_price;  // 割引額を計算
                $total_discount += $discount_value * $quantity;  // 各商品の割引額を累積
            }
        }
        

        $stmt = $conn->prepare("INSERT INTO orders (order_number, store_id, customer_id, total_price, status, received_amount, discount) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
        $stmt->bind_param("sddsdi", $order_number, $store_id, $customer_id, $total_price, $received_amount, $total_discount);
        $stmt->execute();
        // setcookie('totalDiscount', "", 0, "/");
        // $order_id = $stmt->insert_id;

        // Lưu chi tiết đơn hàng vào bảng order_details
        foreach ($products as $item) {
            $barcode = $item['barcode'];  // Lấy barcode từ giỏ hàng
            $quantity = $item['quantity']; // Số lượng sản phẩm

            // Truy vấn productid dựa vào barcode và storeid
            $stmt = $conn->prepare("SELECT productid FROM product WHERE barcode = ? AND storeid = ?");
            $stmt->bind_param("si", $barcode, $store_id);
            $stmt->execute();
            $stmt->bind_result($productid);
            $stmt->fetch();

            // Giải phóng kết quả truy vấn
            $stmt->free_result();  // Giải phóng kết quả truy vấn SELECT
            // $item_price = $price; // Đây là đơn giá của sản phẩm trước khi giảm giá
            $item_price = ($discounted_price !== null) ? $discounted_price : $price;
            // Kiểm tra nếu productid tồn tại
            if ($productid) {
                // Thêm chi tiết vào bảng order_details
                $stmt = $conn->prepare("INSERT INTO order_details (order_number, productid, item_price, quantity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("siii", $order_number, $productid, $item_price, $quantity);
                $stmt->execute();


                // Cập nhật tồn kho sản phẩm
                $stmt = $conn->prepare("UPDATE product SET stock_quantity = stock_quantity - ? WHERE productid = ?");
                $stmt->bind_param("ii", $quantity, $productid);
                $stmt->execute();
            } else {
                // Nếu không tìm thấy productid, trả về lỗi
                echo json_encode(['success' => false, 'error' => '商品存在しない']);
                $conn->rollback();
                exit;
            }
        }
        ///////////////////////////cap nhat profit//////////////////////////
        /////////////////////////// Tính lợi nhuận //////////////////////////
        // Khởi tạo biến lợi nhuận cho đơn hàng này
        $total_cost = 0;  // Tổng chi phí nhập hàng
        foreach ($products as $item) {
            $barcode = $item['barcode'];
            $quantity = $item['quantity'];

            // Lấy giá nhập hàng (costPrice) từ bảng product
            $stmt = $conn->prepare("SELECT costPrice FROM product WHERE barcode = ? AND storeid = ?");
            $stmt->bind_param("si", $barcode, $store_id);
            $stmt->execute();
            $stmt->bind_result($costPrice);
            $stmt->fetch();
            $stmt->free_result();

            if ($costPrice !== null) {
                // Tính tổng chi phí nhập hàng
                $total_cost += $costPrice * $quantity;
            }
        }

        // Tính lợi nhuận cho đơn hàng này
        $profit = $total_price - $total_cost;

        /////////////////////////// Cập nhật bảng daily_revenue //////////////////////////
        $order_date = date('Y-m-d');  // Lấy ngày của đơn hàng

        // Kiểm tra xem doanh thu và lợi nhuận cho ngày này đã tồn tại chưa
        $stmt_check = $conn->prepare("SELECT total_revenue, total_profit FROM daily_revenue WHERE store_id = ? AND revenue_date = ?");
        $stmt_check->bind_param("is", $store_id, $order_date);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Nếu đã có doanh thu và lợi nhuận cho ngày này, cập nhật lại
            $stmt_update = $conn->prepare("
            UPDATE daily_revenue 
            SET total_revenue = total_revenue + ?, total_profit = total_profit + ? 
            WHERE store_id = ? AND revenue_date = ?
        ");
            $stmt_update->bind_param("ddis", $total_price, $profit, $store_id, $order_date);
            $stmt_update->execute();
        } else {
            // Nếu chưa có doanh thu và lợi nhuận cho ngày này, thêm mới
            $stmt_insert = $conn->prepare("
            INSERT INTO daily_revenue (store_id, revenue_date, total_revenue, total_profit) 
            VALUES (?, ?, ?, ?)
        ");
            $stmt_insert->bind_param("isdd", $store_id, $order_date, $total_price, $profit);
            $stmt_insert->execute();
        }

        /////////////////////end/////////////////////////////////////////////
        // Commit giao dịch nếu tất cả lệnh thành công
        $conn->commit();
        echo json_encode(['success' => true, 'order_number' => $order_number]);
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
}
