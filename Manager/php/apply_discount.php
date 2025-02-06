<?php
// 必要なファイルを読み込む
include('./auth_check.php');
include('./db_connect.php');

$conn = new mysqli($servername, $username, $password, $dbname);
// 接続エラーチェック
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'データベース接続に失敗しました: ' . $conn->connect_error]));
    exit;
}

// JSON形式でデータを受信
$requestData = json_decode(file_get_contents('php://input'), true);

// データを取得
$productId = $requestData['productId'] ?? null;
$storeId = $requestData['storeId'] ?? null;
$discountRate = $requestData['discountRate'] ?? null;
$discountedPrice = $requestData['discountedPrice'] ?? null;

// 入力データのバリデーション
if (!$productId || !is_numeric($productId) || !$discountRate || !is_numeric($discountRate) || !$discountedPrice || !is_numeric($discountedPrice)) {
    echo json_encode(['success' => false, 'message' => '無効な入力データ']);
    exit;
}

// 商品が存在するか確認
$checkSql = "SELECT productid FROM product WHERE productid = ? AND storeid = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $productId, $storeId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '指定された商品が見つかりません']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

// 割引を適用（discounted_price を更新）
$sql = "UPDATE product SET discounted_price = ? WHERE productid = ? AND storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dii", $discountedPrice, $productId, $storeId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '割引が適用されました']);
} else {
    echo json_encode(['success' => false, 'message' => '割引の適用に失敗しました']);
}

$stmt->close();
$conn->close();
