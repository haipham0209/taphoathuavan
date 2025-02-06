
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
// if (!isset($_GET['sname'])) {
//     header("HTTP/1.0 404 Not Found");
//     echo "404 Not Found";
//     exit();
// }

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
    // header("HTTP/1.0 404 Not Found");
    // echo "404 Not Found";
    // exit();
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
    <title><?= htmlspecialchars($sname) ?> - Store Information</title>
    <!-- <link rel="stylesheet" href="../styles/myPage.css"> -->
    <link rel="stylesheet" href="../styles/storeInfor.css">
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="../styles/index.css">
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
                <a href="../main.php?sname=<?php echo $_GET['sname']?>"><img id= logo-main src="<?=$logopath?>" alt="logo"></a>
            </div>
            <nav class="nav-menupc">
                <a href="./main.php?sname=<?php echo $_GET['sname']  ?>" ><img class="avatar" src="../images/home.png"></a>
                <a href="./html/storeInfor.php?sname=<?php echo urlencode($sname); ?>"><img class="avatar" src="../images/info.png"></a>
                <a href="./html/myPage.php?sname=<?php echo $_GET['sname']  ?>"><img class="avatar" src="../images/myself.png"></a>
                <a class="support-title"><img class="avatar" src="../images/phone.png"></a>
            </nav>
            <button class="account-toggle">
                <img class="avatar" src="../images/lock.png" alt="Avatar User"<?php echo $_GET['sname']  ?>>
                <!-- <img class="avatar" src="../images/avataricon.jpg" alt="Avatar User" onclick="window.location.href='myPage.php?sname=<?php echo $_GET['sname']  ?>';"> -->
            </button>
        </div>
        <nav class="nav-menu">
            <ul>
            <li><h3><?php echo htmlspecialchars($sname); ?></h3></li>
            <li><a href="./main2.php?sname=<?= urlencode($sname) ?>">ホームページ</a></li>
            <!-- <li><a href="../main.php?sname=<?php echo urlencode($storeName); ?>">商品</a></li> -->
            <li><a href="../html/storeInfor.php?sname=<?php echo urlencode($storeName); ?>">お店について</a></li>
            <li class="support-title">サポート</li>
            <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo htmlspecialchars($tel); ?>"><?php echo htmlspecialchars($tel); ?></a></li>
            <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mailto:<?php echo htmlspecialchars($mail); ?>"><?php echo htmlspecialchars($mail); ?></a></li>
            <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href="#"><?php echo htmlspecialchars($address); ?></a></li>
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
                        const navLinks = document.querySelectorAll('.nav-menu a');
                        const overlay = document.querySelector('.overlay');
                        const navMenu = document.querySelector('.nav-menu');

                        navLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                // Đóng menu
                                navMenu.classList.remove('active');
                                overlay.style.display = 'none';
                            });
                        });
                    });


                    // document.addEventListener("DOMContentLoaded", function() {
                    //     const supportTitle = document.querySelector('.support-title');
                    //     const supportList = document.querySelector('.support-list');

                    //     supportTitle.addEventListener('click', function() {
                    //         // Kiểm tra trạng thái hiển thị của danh sách hỗ trợ
                    //         if (supportList.style.display === "none" || supportList.style.display === "") {
                    //             supportList.style.display = "block"; // Hiển thị danh sách
                    //         } else {
                    //             supportList.style.display = "none"; // Ẩn danh sách
                    //         }
                    //     });
                    // });
                </script>
    </header>

    <main>
        <!-- Store Information Section -->
        <div class="store-info">
            <div class="logo">
                <img src="../images/welcome.png" alt=" ">
            </div>
             <!-- About Store Section -->
                <div class="about-store">
                <!-- <h2>店舗紹介</h2> -->
                <?php while ($descriptionRow = $descResult->fetch_assoc()): ?>
                    <h2><?php echo htmlspecialchars($descriptionRow['title']); ?></h2>
                    <p><?php echo htmlspecialchars($descriptionRow['content']); ?></p>
                <?php endwhile; ?>

                <h2>所在地</h2>
                <p><?php echo htmlspecialchars($address); ?></p>

                <h2>電話番号</h2>
                <p>📞<?php echo htmlspecialchars($tel); ?></p>


                <h2>お客様の声</h2>
                <p>「とても美味しいパンと料理に感動しました！新鮮で、毎回違うメニューを楽しむことができるので、何度も訪れています。店員さんも親切で、居心地の良い空間です。これからも通い続けます！」</p>
                <p>「このお店のパンは、ふわふわで香りもよく、一口食べると幸せな気分になります。ベトナム料理も本格的で、味に深みがあって本当に美味しいです。」</p>
            </div>  
        </div>
    </main>
    <footer>
        <!-- Social Media Section -->
        <div class="social-media">
            <a href="#"><img src="../images/twitter.png" alt="Twitter"></a>
            <a href="#"><img src="../images/facebook.png" alt="Facebook"></a>
            <a href="#"><img src="../images/instagram.png" alt="Instagram"></a>
        </div>
    </footer>

</body>
<script src="../scripts/menu.js"></script>
<script src="../scripts/mypage.js"></script>
</html>
