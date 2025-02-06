<?php
// セッションやデータベース接続ファイルをインクルード
include('./auth_check.php');
include('./db_connect.php');

// リクエストデータを取得
$requestData = json_decode(file_get_contents('php://input'), true);

if (isset($requestData['productId']) && isset($requestData['storeId'])) {
    $productId = $requestData['productId'];
    $storeId = $requestData['storeId'];

    // データベース接続
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'データベース接続に失敗しました']));
        exit;
    }

    // `discounted_price` が NULL かどうかを確認
    $checkSql = "SELECT discounted_price FROM product WHERE productid = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    // `discounted_price` が NULL かどうかを確認
    $checkSql = "SELECT discounted_price FROM product WHERE productid = ? AND storeid = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $productId, $storeId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        if (is_null($row['discounted_price'])) {
            // 割引が既にない場合
            echo json_encode(['success' => false, 'message' => '割引はありません']);
            $checkStmt->close();
            $conn->close();
            exit;
        }
    } else {
        // 商品が見つからない場合
        echo json_encode(['success' => false, 'message' => '指定された商品が見つかりません']);
        $checkStmt->close();
        $conn->close();
        exit;
    }

    $checkStmt->close();

    // 割引をキャンセル（`discounted_price` を NULL に更新）
    $sql = "UPDATE product SET discounted_price = NULL WHERE productid = ? AND storeid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $productId, $storeId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '割引がキャンセルされました']);
    } else {
        echo json_encode(['success' => false, 'message' => '割引のキャンセルに失敗しました']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => '無効なリクエストです']);
}
