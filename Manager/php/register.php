<?php
include('./db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "KHÔNG THỂ KẾT NỐI MÁY CHỦ";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $email = $conn->real_escape_string($_POST['email']);
    $sname = $conn->real_escape_string($_POST['sname']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);

    // Kiểm tra username
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=username_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }
    $stmt->close();

    // Kiểm tra sname
    $stmt = $conn->prepare("SELECT * FROM store WHERE sname = ?");
    $stmt->bind_param("s", $sname);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=sname_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }
    $stmt->close();

    // Kiểm tra email
    $stmt = $conn->prepare("SELECT * FROM user WHERE mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header("Location: ../StoreRegister.php?error=email_exists&username=" . urlencode($username) . "&sname=" . urlencode($sname) . "&email=" . urlencode($email));
        exit();
    }
    $stmt->close();

    // Kiểm tra mật khẩu
    if (strlen($password) < 6) {
        header("Location: ../StoreRegister.php?error=password_short");
        exit();
    }
    if ($password !== $confirm_password) {
        header("Location: ../StoreRegister.php?error=password_mismatch");
        exit();
    }

    // Sinh token và hash mật khẩu
    $token = bin2hex(random_bytes(16));
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Chèn vào bảng user
    $stmt = $conn->prepare("INSERT INTO user (username, password, mail, token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashed_password, $email, $token);
    if ($stmt->execute()) {
        $userid = $stmt->insert_id;

        // Chèn vào bảng store
        $stmt = $conn->prepare("INSERT INTO store (sname, userid) VALUES (?, ?)");
        $stmt->bind_param("si", $sname, $userid);
        $stmt->execute();
        $stmt->close();

        require '../../vendor/autoload.php'; // Đảm bảo PHPMailer đã được cài đặt
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wearewrb@gmail.com';
            $mail->Password = 'prdgyjrdieqldvnt'; // Mật khẩu ứng dụng
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            $mail->setFrom('wearewrb@gmail.com', 'スマラクシステム サポートチーム');
            $mail->addAddress($email, $username);
            $mail->CharSet = 'UTF-8'; // Thiết lập mã hóa UTF-8
        
            $verify_link = "https://click.ecc.ac.jp/ecc/se2a_24_bugs/we%20are/Manager/php/account_verify.php?token=" . urlencode($token);
        
            $mail->isHTML(true);
            $mail->Subject = 'アカウント登録ありがとうございます - スマラクシステム';
            $mail->Body = "
                $username こんにちは。<br><br>
                この度は、スマラクシステムにご登録いただき誠にありがとうございます。<br>
                以下のリンクをクリックして、アカウントを有効化してください。<br><br>
                <a href='$verify_link'>アカウント有効化リンク</a><br><br>
                リンクをクリックすると、アカウントの有効化が完了し、サービスのご利用を開始いただけます。<br>
                ご不明な点がございましたら、お気軽にお問い合わせください。<br><br>
                今後ともスマラクシステムをよろしくお願いいたします。<br><br>
                スマラクシステム サポートチーム<br>
                support mail: wearewrb@gmail.com
            ";
        
            $mail->send();
            header("Location: ../mail_check.html");
            exit();
        } catch (Exception $e) {
            echo "Lỗi khi gửi email: {$mail->ErrorInfo}";
        }
        
        
    } else {
        echo "Lỗi khi tạo tài khoản.";
    }
    $conn->close();
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
