<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/mainMgr.css">
    <link rel="stylesheet" href="./styles/management.css">
    <title>管理者画面</title>
</head>

<body>
<div class="wrapper">
    <div class="container">
        <!-- ロゴ部分 -->
        <div class="logo">
            <a href="../<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>"><img id="logo" src="<?php echo htmlspecialchars($_SESSION['logopath'] ?? 'default-logo.png'); ?>" alt="Logo" style="width: 240px; height: 80px; padding: 0px; border-radius: 15px;     box-shadow: 
    -5px 5px 10px rgba(42, 41, 41, 0.15),  
    5px 5px 10px rgba(46, 42, 42, 0.15),   
    0px 8px 15px rgba(0, 0, 0, 0.2);
                                                                                                                                                                                                            ;" /></a>
        </div>

        <!-- 左側のボタンメニュー -->
        <div class="menu">
            <button class="menu-button" onclick="location.href='product.php'">
                <img src="./images/product-icon.png" alt="Quản lý sản phẩm" class="icon">
                <span>QL Sản phẩm</span>
            </button>

            <button class="menu-button" onclick="location.href='discount.php'">
                <img src="./images/sale-icon.png" alt="割引" class="icon">
                <span>Giảm giá sp</span>
            </button>
            <button class="menu-button" onclick="location.href='inventory.php'">
                <img src="./images/stock-icon.png" alt="在庫" class="icon"  >
                <span>QL tồn kho</span>
            </button>
            <button class="menu-button" onclick="location.href='order.php'">
                <img src="./images/order-icon.png" alt="注文歴史" class="icon">
                <span>Lịch sử bán </span>
            </button>
            <button class="menu-button"onclick="location.href='setBestSel.php2'"disabled>
                <img src="./images/customer-icon.png" alt="顧客" class="icon">
                <span>QL khách hàng</span>
            </button>
            <button class="menu-button" onclick="location.href='setBestSel.phps'"disabled>
                <img src="./images/bestseller.png" alt="BestSellers" class="icon">
                <span>Bán chạy</span>
            </button>
            <button class="menu-button" onclick="location.href='POS.php'">
                <img src="./images/regi.png" alt="レジ" class="icon">
                <span>Máy tính tiền</span>
            </button>
            <button class="menu-button" onclick="location.href='profileEdit.php'">
                <img src="./images/profile-icon.png" alt="プロフィール" class="icon">
                <span>QL thông tin CH</span>
            </button>

        </div>

        <!-- 日期顯示 -->
        <div class="date-control">
            <button id="prev-date" class="date-button">◀</button>
            <input type="date" id="date-picker" class="date-picker">
            <button id="next-date" class="date-button">▶</button>
        </div>

        <!-- 売上與利益顯示 -->
        <div class="financial-info">
            <div class="row">
                <span class="label">Doanh thu: </span>
                <span id="sales">_____________</span> đ
            </div>
            <div class="row">
                <span class="label">Lợi nhuận</span>
                <span id="profit">_____________</span> đ
            </div>
        </div>
        <div class="logout-container" onclick="location.href='StoreLogin.php'">
        <a href="./php/log_out.php" class="logout-button">Log Out</a>
    </div>
    </div>
    <script src="./scripts/date.js"></script>
</div>
</body>
<footer style="text-align: center">
        <!-- <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='StoreLogin.php'">
        </a> -->
</footer>

</html>