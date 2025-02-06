<?php
include('./db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // トークンでユーザーを特定してパスワードを更新
        $stmt = $conn->prepare("UPDATE user SET password = ?, token = NULL WHERE token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);

        if ($stmt->execute()) {
            echo "<div style='text-align: center; margin-top: 50px;'>
                <h2>パスワードが正常にリセットされました</h2>
                <p><a href='../StoreLogin.php'>ログイン画面へ</a></p>
                </div>";
        } else {
            echo "パスワードリセットに失敗しました。";
        }
    } else {
        echo "パスワードが一致しません。";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "不正なリクエストです。";
}
