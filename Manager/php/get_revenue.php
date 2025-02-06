<?php
include('auth_check.php');
include('db_connect.php');
header('Content-Type: application/json');
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $store_id = $_SESSION['storeid']; // Lấy store_id từ session hoặc biến khác

    // Truy vấn doanh thu và lợi nhuận theo ngày
    $stmt = $conn->prepare("SELECT total_revenue, total_profit FROM daily_revenue WHERE store_id = ? AND revenue_date = ?");
    $stmt->bind_param("is", $store_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra và trả về kết quả
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'total_revenue' => $row['total_revenue'],
            'total_profit' => $row['total_profit']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No date provided']);
}

$conn->close();
?>
