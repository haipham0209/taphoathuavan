

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Thông tin kết nối cơ sở dữ liệu
include('./Manager/php/db_connect.php');


// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    echo "SERVER NOT FOUND";
    exit();
}


$storeName= $_COOKIE["storename"];

// Khởi tạo các biến để tránh lỗi chưa khai báo
$tel = null;
$address = null;
$mail = null;
$sname = null;
$storeid = null;
// $description = null;

// Thực hiện truy vấn để lấy dữ liệu cửa hàng và thông tin người dùng
$query = "SELECT store.storeid, store.logopath, store.sname, store.tel, store.address, user.mail 
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
    if ($storeData["logopath"]){
        $logopath = str_replace('../Manager/', './Manager/', $storeData["logopath"]);
    }
} 

// Truy vấn để lấy mô tả cửa hàng
$descriptionQuery = "SELECT title, content FROM StoreDescriptions WHERE storeid = ?";
$descStmt = $conn->prepare($descriptionQuery);
$descStmt->bind_param("i", $storeid);
$descStmt->execute();
$descResult = $descStmt->get_result();



// Đóng kết nối
$stmt->close();
$conn->close();
require "resources.php";

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - My Page</title>
    <!-- Bootstrap CSS (cục bộ) -->
    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/main2.css" rel="stylesheet">
    <link href="./styles/myPage.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap" rel="stylesheet">
    <!-- Thêm vào phần <head> của HTML -->
    <link href="https://fonts.googleapis.com/css2?family=Murecho:wght@400;700&display=swap" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

</head>
<body>
<header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
            <div class="container-fluid">
                <button class="navbar-toggler mobile-only" type="button" onclick="toggleMenu()">
                    <div class="menu-icon">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                </button>
                <a class="navbar-brand" href="./main2.php?sname=<?= urlencode($sname) ?>"><img id="logoContainer" src="<?= $logopath ?>" alt="logo"></a>
                <div class="menu">
                    <div class="nav-menu">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="./main2.php?sname=<?= urlencode($sname) ?>">ホームページ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./storeInfor3.php?sname=<?= urlencode($sname) ?>">お店について</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="./myPage2.php?sname=<?= urlencode($sname) ?>">マイページ</a>
                            </li>
                            <li class="support-title mobile-only">サポート</li>
                            <li class="nav-item">
                                <i class="fa fa-phone"></i><a class="support" href="tel:<?php echo htmlspecialchars($tel); ?>"><?php echo $tel; ?></a>
                            </li>
                            <li class="nav-item">
                                <i class="fa fa-envelope"></i><a class="support" href="mailto:<?php echo htmlspecialchars($mail); ?>"><?php echo $mail; ?></a>
                            </li>
                            <div class="mobile-only">
                                <li class="nav-item">
                                    <i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a>
                                </li>
                            </div>
                        </ul>
                    </div>
                    <!-- Search PC -->
                    <div class="input-pc">
                        <div class="search-container">
                            <input type="text" id="searchInput" class="input-pc" placeholder="商品を検索" onkeypress="handleKeyPress(event)">
                            <img src="./images/search-icon.png" alt="Search Icon" class="search-icon" id="searchIcon" >
                        </div>
                    </div>
                </div>
                <div class="overlay"></div>   
                <button id="searchBtn" class="btn btn-outline-primary ms-2">
                    <i class="fa fa-search"></i>
                </button>
            </div>   
        </nav>
        <div class="spacer"></div>
        <!-- ---------------hiện navbar khi cuộn------------------------------- -->
        <script>
            let lastScrollTop = 0;
            const navbar = document.querySelector('.navbar');

            window.addEventListener('scroll', () => {
                const currentScroll = window.pageYOffset;
                const isScrollingDown = currentScroll > lastScrollTop;

                if (isScrollingDown && currentScroll > navbar.offsetHeight) {
                    navbar.classList.add('navbar-hidden');
                } else {
                    navbar.classList.remove('navbar-hidden');
                }

                lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Tránh giá trị âm
            });
        </script>
        <!-- --------------------------------------------------------------- -->
    </header>
    <!--Search Mobile -->
    <div id="searchContainer" class="d-none">
        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="商品を検索" onkeypress="handleKeyPress(event)">
            <img src="./images/search-icon.png" alt="Search Icon" class="search-icon" onclick="performSearch()">
        </div>
    </div>
    
    <!-- -----------------------search + navmenu--------------------------------- -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchBtn = document.getElementById("searchBtn");
            const searchContainer = document.getElementById("searchContainer");
            const logoContainer = document.getElementById("logoContainer");
            const overlay = document.querySelector(".overlay");
            const searchInputs = document.querySelectorAll("#searchInput");

            // Sự kiện click vào nút tìm kiếm
            searchBtn.addEventListener("click", function () {
                if (searchContainer.classList.contains("d-none")) {
                    // Hiển thị thanh tìm kiếm và ẩn logo
                    searchContainer.classList.remove("d-none");
                    logoContainer.classList.add("hidden");
                    overlay.classList.add("show"); // Hiển thị overlay
                    document.getElementById("searchInput").focus(); // Đặt con trỏ vào thanh input
                } else {
                    // Ẩn thanh tìm kiếm và hiển thị logo
                    searchContainer.classList.add("d-none");
                    logoContainer.classList.remove("hidden");
                    overlay.classList.remove("show"); // Ẩn overlay khi đóng thanh tìm kiếm
                }
            });
            

            // Sự kiện click vào overlay để đóng thanh tìm kiếm
            overlay.addEventListener("click", function () {
                // Ẩn thanh tìm kiếm và hiển thị logo
                searchContainer.classList.add("d-none");
                logoContainer.classList.remove("hidden");
                overlay.classList.remove("show"); // Ẩn overlay
            });

             // Gắn sự kiện keypress và click cho tất cả input tìm kiếm
             searchInputs.forEach(function (input) {
                input.addEventListener("keypress", function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        performSearch(input.value); // Gọi hàm tìm kiếm
                    }
                });
            });
            // Hàm thực hiện tìm kiếm
            function performSearch(query) {
                if (query) {
                    // Chuyển hướng đến trang tìm kiếm với từ khóa trong URL
                    window.location.href = `search.php?sname=<?= urlencode($sname) ?>&query=${encodeURIComponent(query)}`;
                } else {
                    alert('検索キーワードを入力してください。');
                }
            }
        });

        //click vào biểu tượng search icon PC
        document.getElementById('searchIcon').addEventListener('click', function () {
            const query = document.getElementById("searchInput").value.trim();
            if (query) {
                // Chuyển hướng đến trang tìm kiếm
                window.location.href = `search.php?sname=<?= urlencode($sname) ?>&query=${encodeURIComponent(query)}`;
            } else {
                alert('検索キーワードを入力してください。');
            }
        });
        //click vào biểu tượng search icon Mobile
        function performSearch(query) {
            if (query) {
                // Chuyển hướng đến trang tìm kiếm với từ khóa trong URL
                window.location.href = `search.php?sname=<?= urlencode($sname) ?>&query=${encodeURIComponent(query)}`;
            } else {
                alert('検索キーワードを入力してください。');
            }
        }


        document.addEventListener("DOMContentLoaded", function () {
            const menuButton = document.querySelector(".navbar-toggler");
            const navMenu = document.querySelector(".nav-menu");
            const overlay = document.querySelector(".overlay");
            const body = document.body; // Tham chiếu đến body
            const menuIcon = document.querySelector(".menu-icon"); // Tham chiếu đến icon 3 gạch

            // Xử lý mở menu
            menuButton.addEventListener("click", function () {
                navMenu.classList.toggle("open");
                // overlay.classList.toggle("show");
                menuIcon.classList.toggle("active"); // Thêm/xóa lớp chuyển đổi dấu "X"

                // Thêm hoặc xóa lớp khóa cuộn cho body
                if (navMenu.classList.contains("open")) {
                    body.classList.add("no-scroll");
                } else {
                    body.classList.remove("no-scroll");
                }
            });
        });
       
    </script>
    <!-- ------------------------------------------------------ -->
    

    <main>
        <div class="mypage-container">
            <!-- Order History Section -->
            <div class="order-history">
                <h2>
                    <img src="./images/time.png" alt="History Icon" class="icon"> 注文履歴
                </h2>
                <div class="order-items">
                    <img src="./images/no-image.png" alt="Order Item">
                </div>
            </div>
            <!-- Shopping Cart Section -->
            <div class="cart">
                <h2>
                    <img src="./images/shopping.png" alt="Cart Icon" class="icon"> 買い物カゴ
                </h2>
                <div class="cart-items">
                    <div class="cart-img">
                        <img src="./images/no-image.png" alt="Cart Item">
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
                    <img src="./images/black-heart.png" alt="Favor Icon" class="icon"> お気に入りリスト
                </h2>
                <div class="favor-items">
                    <img src="./images/no-image.png" alt="Favor Item">
                    <img src="./images/no-image.png" alt="Favor Item">
                    <img src="./images/no-image.png" alt="Favor Item">
                </div>
            </div>
        </div>
    </main>
    <footer>
        <!-- Social Media Section -->
        <div class="social-media">
            <a href="#"><img src="./images/twitter.png" alt="Twitter"></a>
            <a href="#"><img src="./images/facebook.png" alt="Facebook"></a>
            <a href="#"><img src="./images/instagram.png" alt="Instagram"></a>
        </div>
    </footer>
    <!-- Modal -->
    <div id="loginRegisterModal" class="modal">
        <div class="modal-content">
            <p class="modalTitle">アカウントを作成またはログイン</p>
            <div class="modal-buttons">
                <button id="loginButton" class="btn btn-primary">ログイン</button>
                <button id="registerButton" class="btn btn-secondary">新規登録</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("loginRegisterModal");

            // Hiển thị modal khi trang được tải
            modal.style.display = "block";

            
        });

    </script>

</body>
<script src="../scripts/menu.js"></script>
<script src="../scripts/mypage.js"></script>
</html>
