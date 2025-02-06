<?php
session_start();
include(__DIR__ . '/db_connect.php'); // データベース接続ファイルをインクルード

// ユーザーがログインしているか確認
// if (!isset($_SESSION['userid'])) {
//     header("Location: login.php");
//     exit;
// }
include('db_connect.php');
// パスワードが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // データベースからユーザーのパスワードを取得
    $stmt = $conn->prepare("SELECT password FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // 入力されたパスワードが正しいかをチェック
    if (password_verify($password, $hashedPassword)) {

        // storeテーブルからstoreidを取得
        $storeStmt = $conn->prepare("SELECT storeid FROM store WHERE userid = ?");
        $storeStmt->bind_param("i", $userid);
        $storeStmt->execute();
        $storeStmt->bind_result($storeid);
        $storeStmt->fetch();
        $storeStmt->close();

        if ($storeid) {

            // todo：メールを送る処理

            // Xóa dữ liệu liên quan trong orders_old trước
            $deleteOrdersOldStmt = $conn->prepare("DELETE FROM orders_old WHERE store_id = ?");
            $deleteOrdersOldStmt->bind_param("i", $storeid);
            $deleteOrdersOldStmt->execute();
            $deleteOrdersOldStmt->close();

            // productテーブルの関連レコードを削除
            $deleteProductStmt = $conn->prepare("DELETE FROM product WHERE storeid = ?");
            $deleteProductStmt->bind_param("i", $storeid);
            $deleteProductStmt->execute();
            $deleteProductStmt->close();

            // categoryテーブルの関連レコードを削除
            $deleteCategoryStmt = $conn->prepare("DELETE FROM category WHERE storeid = ?");
            $deleteCategoryStmt->bind_param("i", $storeid);
            $deleteCategoryStmt->execute();
            $deleteCategoryStmt->close();

            // Xóa dữ liệu liên quan trong storedescriptions
            $deleteStoreDescriptionsStmt = $conn->prepare("DELETE FROM storedescriptions WHERE storeid = ?");
            $deleteStoreDescriptionsStmt->bind_param("i", $storeid);
            $deleteStoreDescriptionsStmt->execute();
            $deleteStoreDescriptionsStmt->close();


            // storeテーブルの関連レコードを削除
            $deleteStoreStmt = $conn->prepare("DELETE FROM store WHERE userid = ?");
            $deleteStoreStmt->bind_param("i", $userid);
            $deleteStoreStmt->execute();
            $deleteStoreStmt->close();
        }

        // パスワードが正しければアカウントを削除
        $stmt = $conn->prepare("DELETE FROM user WHERE userid = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $stmt->close();

        // セッションをクリアしてログインページへリダイレクト
        session_destroy();
        header("Location: ../StoreLogin.php");
        exit;
    } else {
        header("Location: ../profileEdit.php?error=invalid_password");
        exit;
    }
}
