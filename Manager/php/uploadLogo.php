<?php
session_start();
include('./db_connect.php'); // データベース接続ファイル

// ロゴファイルがアップロードされたか確認
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $userid = $_SESSION['userid']; // ユーザーIDを取得
    $uploadDir = '../uploads/'; // アップロードフォルダのパス
    $uploadFile = $uploadDir . basename($_FILES['logo']['name']);

    // 画像ファイルかどうかを確認
    $fileType = pathinfo($uploadFile, PATHINFO_EXTENSION);
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        // ファイルを指定のフォルダに移動
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
            // データベースにファイルのパスを保存
            $stmt = $conn->prepare("UPDATE user SET logo_path = ? WHERE userid = ?");
            $stmt->bind_param("si", $uploadFile, $userid);
            $stmt->execute();
            $stmt->close();

            // アップロード成功時のリダイレクト
            header("Location: ../profile.php?upload=success");
        } else {
            echo "ファイルのアップロードに失敗しました。";
        }
    } else {
        echo "許可されていないファイル形式です。";
    }
} else {
    echo "ファイルがアップロードされていないか、エラーが発生しました。";
}
