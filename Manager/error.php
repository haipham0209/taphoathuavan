<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERROR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8d7da; /* Màu nền đỏ nhạt */
            color: #721c24; /* Màu chữ đỏ sẫm */
            padding: 20px;
            text-align: center;
        }
        h1 {
            font-size: 2em;
        }
        .error-message {
            background-color: #f5c6cb; /* Màu nền thông báo lỗi */
            border: 1px solid #f5c6cb; /* Đường viền của thông báo */
            padding: 10px;
            margin: 20px auto;
            display: inline-block;
            border-radius: 5px;
            font-size: 1.2em; /* Kích thước chữ cho thông báo */
        }
        .home-link {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            background-color: #007bff; /* Màu nền của nút */
            color: white; /* Màu chữ nút */
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s; /* Hiệu ứng chuyển màu */
        }
        .home-link:hover {
            background-color: #0056b3; /* Màu nền khi hover */
        }
    </style>
</head>
<body>

    <h1>エラーが発生しました。</h1>
    <div class="error-message"> <!-- Sửa tên class từ "2error-message" thành "error-message" -->
        <?php
            // Lấy thông báo lỗi từ URL
            if (isset($_GET['error'])) {
                // Giải mã thông báo lỗi để tránh tấn công XSS
                $error = htmlspecialchars($_GET['error']);

                // Hiển thị thông báo tương ứng với lỗi
                if ($error == "account_activate_false") {
                    echo "アカウントの有効化に  失敗しました。再試行してください。";
                    echo "EROR CODE WRB-01";
                } elseif ($error == "invalid_token") {
                    echo "無効なリンクです。再度ご確認ください。";
                    echo "EROR CODE WRB-02";
                } elseif ($error == "updateError") {
                    echo "商品を更新できませんでした、再度試してみてください。";
                    echo "EROR CODE WRB-03";
                }  elseif ($error == "notAllow") {
                    echo "商品を更新できませんでした、再度試してみてください。";
                    echo "EROR CODE WRB-04";
                }  elseif ($error == "nopermission") {
                    echo "不正なリクエストです。";
                    echo "EROR CODE WRB-05";
                }  
                
                
            }else {
                echo "不明なエラーが発生しました。";
                echo "EROR CODE WRB-000";
            }
        ?>
    </div>
    
    <a href="./main.php" class="home-link">ホームページに戻る</a>

</body>
</html>
