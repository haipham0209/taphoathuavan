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

// Kiểm tra tham số URL
if (!isset($_GET['sname'])) {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

$storeName = $_GET['sname'];

// Khởi tạo các biến
$tel = null;
$address = null;
$mail = null;
$sname = null;
$storeid = null;

// Truy vấn dữ liệu cửa hàng
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
    // setcookie("storename", $sname);
    setcookie("storename", $sname, time() + (10 * 365 * 24 * 60 * 60), "/");
    setcookie("storeid", $storeid, time() + (10 * 365 * 24 * 60 * 60), "/");
    if ($storeData["logopath"]){
        $logopath = str_replace('../Manager/', './Manager/', $storeData["logopath"]);
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "404 Not Found";
    exit();
}

// Truy vấn sản phẩm bán chạy
$best_sellers_sql = "
    SELECT 
        p.productid, 
        p.pname, 
        p.price, 
        p.productImage, 
        SUM(od.quantity) AS total_quantity
    FROM product p
    JOIN order_details od ON p.productid = od.productid
    WHERE p.storeid = ?
    GROUP BY p.productid, p.pname, p.price, p.productImage
    ORDER BY total_quantity DESC
    LIMIT 3";

$stmt_best_sellers = $conn->prepare($best_sellers_sql);
$stmt_best_sellers->bind_param("i", $storeid);
$stmt_best_sellers->execute();
$best_sellers_result = $stmt_best_sellers->get_result();

// Đóng kết nối
$stmt->close();
$stmt_best_sellers->close();
$conn->close();
require "resources.php";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRB - Home</title>
    <!-- Bootstrap CSS (cục bộ) -->
    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="./styles/main2.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Gloria+Hallelujah&display=swap" rel="stylesheet">
    <!-- Thêm vào phần <head> của HTML -->
    <link href="https://fonts.googleapis.com/css2?family=Murecho:wght@400;700&display=swap" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
            <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="./scripts/main2oSearch.js"></script>

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
            <img src="./images/search-icon.png" alt="Search Icon" class="search-icon" id="search1" onclick="performSearchFromInput()">
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
    

    <main class="container mt-4">
        
            <?php
            // if ($best_sellers_result->num_rows > 0) {
            //     while ($product = $best_sellers_result->fetch_assoc()) {
            //         $productImagePath = $product['productImage'];
            //         echo '
            //         <div class="col-md-4 mb-4">
            //             <div class="card">
            //                 <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '">
            //                 <div class="card-body">
            //                     <h5 class="card-title">' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</h5>
            //                     <p class="card-text">¥' . number_format($product['price'], 0) . '</p>
            //                 </div>
            //             </div>
            //         </div>';
            //     }
            // }
            ?>
        
         <!-- <p>------------------------------------------------------------------------</p> -->
        <section id="product-section" class="category">
            <?php
            function renderProductGroup($categories, $maxProducts) {
                foreach ($categories as $category): ?>
                    <div class="group" id="<?= htmlspecialchars(strtolower($category['cname'])) ?>">
                        <h3 class="title" data-aos="fade-right" data-aos-duration="1000">
                            <?= htmlspecialchars($category['cname']) ?>
                        </h3>
                        <div class="product-showcase">
                            <!-- Hiển thị sản phẩm -->
                            <?php foreach (array_slice($category['products'], 0, $maxProducts) as $product): ?>
                                <a href="productDetail.php?id=<?= htmlspecialchars($product['productid']) ?>">
                                    <div class="product-content" data-aos="fade-up" data-aos-duration="1000">
                                        <div class="image-wrapper">
                                            <img src="./images/placeholder.jpg" data-src="<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['pname']) ?>" class="product-image lazyload" />
                                            <?php if (!is_null($product['discounted_price'])): ?>
                                                <img src="Manager/images/sale.png" alt="Sale" class="sale-icon" />
                                            <?php endif; ?>
                                        </div>
                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
                                        <p class="rotated-text">
                                            <span class="product-name"><?= htmlspecialchars($product['pname']) ?></span><br>
                                            <?php if (!is_null($product['discounted_price'])): ?>
                                                <s><?= number_format($product['price']) ?> ¥</s>
                                            <?php else: ?>
                                                <?= number_format($product['price']) ?> ¥
                                            <?php endif; ?>

                                            <?php if (!is_null($product['discounted_price'])): ?>
                                                <br><span class="discounted-price"><?= number_format($product['discounted_price']) ?> ¥</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <!-- Nếu số lượng sản phẩm > maxProducts, hiển thị nút Show More -->
                        <?php if (count($category['products']) > $maxProducts): ?>
                            <button 
                                class="show-more-btn" 
                                data-group="<?= htmlspecialchars(strtolower($category['cname'])) ?>" 
                                data-aos="fade-up" 
                                data-aos-duration="1000" 
                                onclick="showMore(<?= htmlspecialchars(json_encode($category['products'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) ?>)">
                                全て表示
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
            }
            ?>

            <!--PC-->
            <div class="product-pc">
                <?php renderProductGroup($categories, 4); ?>
            </div>

            <!--Mobile-->
            <div class="product-mobile">
                <?php renderProductGroup($categories, 2); ?>
            </div>
        </section>

    </main>
</body>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Khởi tạo hiệu ứng AOS
        AOS.init({
            duration: 1000, // Thời gian hiệu ứng (ms)
            easing: 'ease-in-out', // Loại easing cho hiệu ứng
            once: true // Hiệu ứng chỉ diễn ra một lần khi cuộn
        });
    });

    function formatPrice(price) {
        return new Intl.NumberFormat('ja-JP').format(price);
    }

    function showMore(products) {
    const productShowcase = event.target.previousElementSibling;
    const button = event.target; // Nút Show More/Show Less hiện tại
    const displayedProducts = productShowcase.querySelectorAll('.product-content');
    const buttonText = button.textContent.trim(); // Loại bỏ khoảng trắng thừa

    // Kiểm tra trạng thái của nút
    if (buttonText === "全て表示") {
        console.log("Hiển thị thêm sản phẩm");

        // Chỉ lấy các sản phẩm chưa hiển thị
        const remainingProducts = products.slice(displayedProducts.length);

        remainingProducts.forEach(product => {
            const productContent = document.createElement('div');
            productContent.classList.add('product-content');
            productContent.setAttribute('data-aos', 'fade-up');
            productContent.setAttribute('data-aos-duration', '1000');

            // Định dạng giá
            const formattedPrice = formatPrice(product.price);
            const formattedDiscountedPrice = product.discounted_price ? formatPrice(product.discounted_price) : null;

            // Tạo liên kết đến trang chi tiết sản phẩm
            const productLink = document.createElement('a');
            productLink.href = `productDetail.php?id=${product.productid}`; // Thêm liên kết vào sản phẩm

            // Thêm nội dung của sản phẩm vào trong liên kết
            productLink.innerHTML = `
                <div class="image-wrapper">
                    <img src="./images/placeholder.jpg" data-src="${product.productImage}" alt="${product.pname}" class="product-image lazyload">
                    ${formattedDiscountedPrice ? `<img src="Manager/images/sale.png" alt="Sale" class="sale-icon">` : ''}
                </div>
                <p class="rotated-text">
                    ${product.pname}<br>
                    ${formattedDiscountedPrice ? `<s>${formattedPrice} ¥</s><br><span class="discounted-price">${formattedDiscountedPrice} ¥</span>` : `${formattedPrice} ¥`}
                </p>
            `;

            // Thêm sản phẩm vào giao diện
            productContent.appendChild(productLink);
            productShowcase.appendChild(productContent);
        });

        // Khởi tạo lại hiệu ứng AOS
        AOS.refresh();

        // Đổi nút thành Show Less
        button.textContent = "閉じる";
    } else if (buttonText === "閉じる") {
        console.log("Ẩn bớt sản phẩm");

        // Quay về trạng thái chỉ hiển thị 2 sản phẩm đầu tiên
        productShowcase.innerHTML = ''; // Xóa toàn bộ sản phẩm hiện tại

        // Kiểm tra kích thước màn hình
        const isMobile = window.innerWidth <= 768;
        // Hiển thị số lượng sản phẩm tùy thuộc vào kích thước màn hình
        const visibleProducts = isMobile ? 2 : 4; // 2 sản phẩm cho điện thoại, 4 sản phẩm cho máy tính
        // Chỉ hiển thị 2 sản phẩm đầu
        products.slice(0, visibleProducts).forEach(product => {
            const productContent = document.createElement('div');
            productContent.classList.add('product-content');

            // Định dạng giá
            const formattedPrice = formatPrice(product.price);
            const formattedDiscountedPrice = product.discounted_price ? formatPrice(product.discounted_price) : null;

            // Tạo liên kết đến trang chi tiết sản phẩm
            const productLink = document.createElement('a');
            productLink.href = `productDetail.php?id=${product.productid}`; // Thêm liên kết vào sản phẩm

            // Thêm nội dung của sản phẩm vào trong liên kết
            productLink.innerHTML = `
                <div class="image-wrapper">
                    <img src="./images/placeholder.jpg" data-src="${product.productImage}" alt="${product.pname}" class="product-image lazyload">
                    ${formattedDiscountedPrice ? `<img src="Manager/images/sale.png" alt="Sale" class="sale-icon">` : ''}
                </div>
                <p class="rotated-text">
                    ${product.pname}<br>
                    ${formattedDiscountedPrice ? `<s>${formattedPrice} ¥</s><br><span class="discounted-price">${formattedDiscountedPrice} ¥</span>` : `${formattedPrice} ¥`}
                </p>
            `;

            // Thêm sản phẩm vào giao diện
            productContent.appendChild(productLink);
            productShowcase.appendChild(productContent);
        });

        productShowcase.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Khởi tạo lại hiệu ứng AOS
        AOS.refresh();

        // Đổi nút thành Show More
        button.textContent = "全て表示";
    } else {
        console.log("1");
    }
}
</script>


<footer>
    <!-- Social Media Section -->
    <div class="social-media">
        <a href="#"><img src="./images/twitter.png" alt="Twitter"></a>
        <a href="#"><img src="./images/facebook.png" alt="Facebook"></a>
        <a href="#"><img src="./images/instagram.png" alt="Instagram"></a>
    </div>
</footer>
</html>
