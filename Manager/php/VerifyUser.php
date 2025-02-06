<?php
include('./db_connect.php');
require '../../vendor/autoload.php'; // PHPMailerのインクルード

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    // ユーザーが存在するかチェック
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND mail = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ユーザーが存在する場合、パスワードリセット用のトークンを生成
        $token = bin2hex(random_bytes(16));

        // トークンをデータベースに保存
        $stmt = $conn->prepare("UPDATE user SET token = ? WHERE username = ?");
        $stmt->bind_param("ss", $token, $username);
        $stmt->execute();

        // リセットリンクの作成
        $reset_link = "https://click.ecc.ac.jp/ecc/se2a_24_bugs/we%20are/Manager/ResetPassword.php?token=" . urlencode($token);

        // メールの送信
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wearewrb@gmail.com';
            $mail->Password = 'prdgyjrdieqldvnt'; // アプリケーションパスワード
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('wearewrb@gmail.com', 'スマラクシステム サポートチーム');
            $mail->addAddress($email, $username);
            $mail->CharSet = 'UTF-8';

            $mail->isHTML(true);
            $mail->Subject = 'パスワードリセットのご案内 - スマラクシステム';
            $mail->Body = "
                $username 様<br><br>
                パスワードリセットのリクエストを受け付けました。<br>
                以下のリンクをクリックして、パスワードリセットを行ってください。<br><br>
                <a href='$reset_link'>パスワードリセットリンク</a><br><br>
                リンクの有効期限は24時間です。<br>
                ご不明な点がございましたら、お気軽にお問い合わせください。<br><br>
                スマラクシステム サポートチーム<br>
                support mail: wearewrb@gmail.com
            ";

            $mail->send();
            echo "<div style='text-align: center; margin-top: 50px;'>
                <h2>リセットリンクをメールに送信しました。</h2>
                </div>";
        } catch (Exception $e) {
            echo "メールの送信に失敗しました: {$mail->ErrorInfo}";
        }
    } else {
        echo "ユーザー名またはメールアドレスが正しくありません。";
    }

    $stmt->close();
    $conn->close();
} else {
    // echo "不正なリクエストです。";
    header("Location: ../error.php?error=nopermission");
}
