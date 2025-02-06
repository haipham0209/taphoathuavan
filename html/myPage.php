

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Thông tin kết nối cơ sở dữ liệu
include('../Manager/php/db_connect.php');


// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}

// Kiểm tra xem có tham số sname trong URL không
if (!isset($_GET['sname'])) {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

$storeName = $_GET['sname'];

// Khởi tạo các biến để tránh lỗi chưa khai báo
$tel = null;
$address = null;
$mail = null;
$sname = null;
$storeid = null;
// $description = null;

// Thực hiện truy vấn để lấy dữ liệu cửa hàng và thông tin người dùng
$query = "SELECT store.storeid,store.logopath, store.sname, store.tel, store.address, user.mail 
          FROM store 
          JOIN user ON store.userid = user.userid 
          WHERE store.sname = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $storeName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $storeData = $result->fetch_assoc();
    $storeid = $storeData['storeid'];
    $sname = $storeData["sname"];
    $tel = $storeData["tel"];
    $address = $storeData["address"];
    $mail = $storeData["mail"];
    // $description = $storeData["description"];
    $logopath = $storeData["logopath"];
    $logopath = str_replace('.../Manager/', '../Manager/', $logopath);

} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

// Truy vấn để lấy mô tả cửa hàng
$descriptionQuery = "SELECT title, content FROM StoreDescriptions WHERE storeid = ?";
$descStmt = $conn->prepare($descriptionQuery);
$descStmt->bind_param("i", $storeid);
$descStmt->execute();
$descResult = $descStmt->get_result();

// Đóng kết nối
$stmt->close();
$descStmt->close();
$conn->close();

//require "resources.php";

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Page</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="../styles/index.css">
    <link rel="stylesheet" href="../styles/myPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
            </button>
            <div class="logobar">
                <a href="../main.php?sname=<?php echo $_GET['sname']  ?>"><img id= logo-main src="<?=$logopath?>" alt="logo"></a>
            </div>
            <nav class="nav-menupc">
                <a href="./main.php?sname=<?php echo $_GET['sname']  ?>" > <img class="avatar" src="../images/home.png"></a>
                <a href="./html/storeInfor.php?sname=<?php echo urlencode($sname); ?>"><img class="avatar" src="../images/info.png"></a>
                <a href="./html/myPage.php?sname=<?php echo $_GET['sname']  ?>"><img class="avatar" src="../images/myself.png"></a>
                <a class="support-title"><img class="avatar" src="../images/phone.png"></a>
            </nav>
            <button class="avatar-toggle">
                <img class="avatar" src="../images/lock.png" alt="Avatar User">
            </button>
        </div>

    <nav class="nav-menu">
        <ul>
        <li><h3><?php echo $sname; ?></h3></li>
          <li><a href="../main.php?sname=<?php echo $_GET['sname']?>">ホームページ</a></li>
          <!-- <li><a href="./html/product.php?sname=<?php echo $_GET['sname']?>">商品</a></li> -->
          <li><a href="./storeInfor.php?sname=<?php echo $_GET['sname']?>">お店について</a></li>
          <li><a href="./html/myPage.php?sname=<?php echo $_GET['sname']  ?>">マイページ</a></li>
          <li class="support-title">サポート</li>
          <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>
          <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a></li>
          <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
         </ul>
    </nav>
    <div class="overlay"></div>
    <nav class="nav-myPage">
        <ul>
            <li><a href="#">登録</a></li>
            <li><a href="#">ログイン</a></li>
        </ul>
    </nav>
        <div class="overlay-avatar"></div>
        <ul class="support-list" style="display: none;"> <!-- Ẩn danh sách ban đầu -->
                    <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>
                    <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mailto:<?php echo $mail; ?>"><?php echo $mail; ?></a></li>
                    <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
        </ul>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const supportTitle = document.querySelector('.support-title');
                        const supportList = document.querySelector('.support-list');

                        supportTitle.addEventListener('click', function() {
                            // Kiểm tra trạng thái hiển thị của danh sách hỗ trợ
                            if (supportList.style.display === "none" || supportList.style.display === "") {
                                supportList.style.display = "block"; // Hiển thị danh sách
                            } else {
                                supportList.style.display = "none"; // Ẩn danh sách
                            }
                        });
                    });
                </script>
    </header>

    <main>
        <div class="mypage-container">
            <!-- Order History Section -->
            <div class="order-history">
                <h2>
                    <img src="../images/time.png" alt="History Icon" class="icon"> 注文履歴
                </h2>
                <div class="order-items">
                    <img src="../images/product/1.jpg" alt="Order Item">
                </div>
            </div>
            <!-- Shopping Cart Section -->
            <div class="cart">
                <h2>
                    <img src="../images/shopping.png" alt="Cart Icon" class="icon"> 買い物カゴ
                </h2>
                <div class="cart-items">
                    <div class="cart-img">
                        <img src="../images/no-image.png" alt="Cart Item">
                    </div>
                    <div class="cart-info">
                        <div class="cart-details">
                            <p>1アイテム</p>
                            <p>1,500¥</p>
                        </div>
                        <button class="buy-button">ご購入</button>
                    </div>
                </div>
            </div>
            <!-- Favorites Section -->
            <div class="favorites">
                <h2>
                    <img src="../images/black-heart.png" alt="Favor Icon" class="icon"> お気に入りリスト
                </h2>
                <div class="favor-items">
                    <img src="../images/no-image.png" alt="Favor Item">
                    <img src="../images/no-image.png" alt="Favor Item">
                    <img src="../images/no-image.png" alt="Favor Item">
                </div>
            </div>
        </div>
    </main>
    <footer>
       
    </footer>

</body>
<script src="../scripts/menu.js"></script>
<script src="../scripts/mypage.js"></script>
</html>
