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
            <a href="../main2.php?sname=<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>"><img id="logo" src="<?php echo htmlspecialchars($_SESSION['logopath'] ?? 'default-logo.png'); ?>" alt="Logo" style="width: 240px; height: 80px; padding: 5px; border-radius: 5px;" /></a>
        </div>

        <!-- 左側のボタンメニュー -->
        <div class="menu">
            <button class="menu-button" onclick="location.href='product.php'">
                <img src="./images/product-icon.png" alt="商品" class="icon">
                <span>商品</span>
            </button>

            <button class="menu-button" onclick="location.href='discount.php'">
                <img src="./images/sale-icon.png" alt="割引" class="icon">
                <span>割引</span>
            </button>
            <button class="menu-button" onclick="location.href='inventory.php'">
                <img src="./images/stock-icon.png" alt="在庫" class="icon"  >
                <span>在庫</span>
            </button>
            <button class="menu-button" onclick="location.href='order.php'">
                <img src="./images/order-icon.png" alt="注文歴史" class="icon">
                <span>注文歴史</span>
            </button>
            <button class="menu-button"onclick="location.href='setBestSel.php2'"disabled>
                <img src="./images/customer-icon.png" alt="顧客" class="icon">
                <span>顧客</span>
            </button>
            <button class="menu-button" onclick="location.href='setBestSel.phps'"disabled>
                <img src="./images/bestseller.png" alt="BestSellers" class="icon">
                <span>ベストセラー</span>
            </button>
            <button class="menu-button" onclick="location.href='POS.php'">
                <img src="./images/regi.png" alt="レジ" class="icon">
                <span>レジ</span>
            </button>
            <button class="menu-button" onclick="location.href='profileEdit.php'">
                <img src="./images/profile-icon.png" alt="プロフィール" class="icon">
                <span>プロフ</span>
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
                <span class="label">売上：</span>
                <span id="sales">_____________</span> ¥
            </div>
            <div class="row">
                <span class="label">利益：</span>
                <span id="profit">_____________</span> ¥
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