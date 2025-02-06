<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/inventory.css">
    <title>在庫</title>
</head>
<body>
    <div class="container">
        <h1>在庫管理</h1>
        <!-- <div class="bar">
            <h1>在庫管理</h1>
            <img src="./images/back_button2.png" alt="Back Icon" onclick="location.href='main.php'">
        </div> -->
        <input type="text" id="search" placeholder="商品を探" onkeyup="searchProducts()">
        <div class="table-container">
            <table id="productTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>商品コード</th>
                        <th>商品名</th>
                        <th>在庫</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Thông tin kết nối cơ sở dữ liệu
                include('../Manager/php/db_connect.php');
                include('../Manager/php/auth_check.php');

                // Tạo kết nối
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Kiểm tra kết nối
                if ($conn->connect_error) {
                    die("Kết nối thất bại: " . $conn->connect_error);
                }

                // Lấy storeid từ session
                $storeid = $_SESSION['storeid'];

                // Truy vấn dữ liệu từ bảng sản phẩm với điều kiện storeid
                $sql = "SELECT barcode, pname, stock_quantity FROM product WHERE storeid = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $storeid);  // "i" cho kiểu dữ liệu int
                $stmt->execute();
                $result = $stmt->get_result();

                // Kiểm tra và hiển thị dữ liệu
                if ($result->num_rows > 0) {
                    // Lặp qua từng dòng dữ liệu và hiển thị trong bảng
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $count++ . "</td><td>" . htmlspecialchars($row["barcode"]) . "</td><td>" . htmlspecialchars($row["pname"]) . "</td><td>" . htmlspecialchars($row["stock_quantity"]) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>商品見つかりません</td></tr>";
                }

                // Đóng kết nối
                $conn->close();
                ?>
                <!-- Thêm nhiều sản phẩm hơn nếu cần -->
                    <!-- <script>
                        for (let i = 1; i <= 100; i++) {
                            document.write('<tr><td>' + i + '</td><td>SP00' + i + '</td><td>Sản phẩm ' + i + '</td><td>' + (i * 10) + '</td></tr>');
                        }
                    </script> -->
                </tbody>
            </table>
        </div>
    </div>
    <script src="./scripts/inventory.js"></script>
</body>
<footer style="text-align: center">
        <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'">
        </a>
    </footer>
</html>