<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');

// Kết nối cơ sở dữ liệu
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy storeid từ session hoặc từ một nguồn khác
$storeid = $_SESSION['storeid']; // Assuming storeid is stored in session

// Lấy danh sách các danh mục từ cơ sở dữ liệu của người dùng hiện tại
$category_sql = "SELECT category_id, cname FROM category WHERE storeid = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $storeid); // Ràng buộc biến storeid
$stmt->execute();
$category_result = $stmt->get_result();

// Lấy category_ids từ GET parameter (nếu có)
$category_ids = isset($_GET['category_ids']) ? explode(',', $_GET['category_ids']) : [];

// Truy vấn để lấy danh sách sản phẩm từ cơ sở dữ liệu
$product_sql = "
    SELECT p.productid, p.storeid, p.pname, p.price, p.costPrice, p.discounted_price, p.description, p.stock_quantity, 
           p.productImage, u.username, c.cname, p.category_id
    FROM product p
    JOIN store s ON p.storeid = s.storeid
    JOIN user u ON s.userid = u.userid
    JOIN category c ON p.category_id = c.category_id AND p.storeid = c.storeid
    WHERE p.storeid = ?
";

if (!empty($category_ids)) {
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $product_sql .= " AND p.category_id IN ($placeholders)";
}

$product_stmt = $conn->prepare($product_sql);

// バインド変数を動的に設定
$params = array_merge([$storeid], $category_ids);
$types = str_repeat('i', count($params)); // 全て整数型
$product_stmt->bind_param($types, ...$params);

$product_stmt->execute();
$product_result = $product_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/discount.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/cameraScan.js"></script>
    <title>割引</title>
</head>

<body>
<header>
        <div class="main-navbar">
            <div class="search-scan">
                <input type="text" name="barcode" id="barcode-input" class="search-bar" placeholder="商品名又はコード入力">            
                <div id="barcode-suggestions" class="suggestions-list" style="display:none;"></div>
                <img src="./images/camera-icon.png" class="camera-icon" onclick="toggleCamera()">
            </div>
            <div id="suggestionList"></div>
            <script>
                let isCameraRunning = false; 

                function toggleCamera() {
                    if (isCameraRunning) {
                        stopScanner();
                        isCameraRunning = false;
                    } else {
                        startScanner();
                        isCameraRunning = true;
                    }
                }
            </script>
            <a href="main.php">
                <img class="home" src="./images/home.png" alt="Home Mana">
            </a>
        </div>
    </header>
    <main>
        <div id="camera" style="display: none;">
            <button id="stopBtn" onclick="toggleCamera()">カメラ停止</button>
        </div>
        <p class="title">商品割引管理</p>

        <!-- Category -->
        <div class="category">
            <button class="all-categories <?= empty($category_ids) ? 'active' : '' ?>" onclick="showAllCategories()">All</button>
            <?php
            if ($category_result->num_rows > 0) {
                while ($row = $category_result->fetch_assoc()) {
                    $isSelected = in_array($row['category_id'], $category_ids) ? 'active' : '';
                    echo '<button class="' . $isSelected . '" data-category-id="' . $row['category_id'] . '">'
                        . htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8') . '</button>';
                }
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>

        </div>

        <!-- Product Cards -->
        <div class="all-product">
            <?php
            if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
                    $productImagePath = '../' . $product['productImage'];
                    $categoryId = !empty($product['category_id']) ? htmlspecialchars($product['category_id'], ENT_QUOTES, 'UTF-8') : '';
                    $discountedPrice = !is_null($product['discounted_price']) ? htmlspecialchars($product['discounted_price'], ENT_QUOTES, 'UTF-8') : '';

                    echo '
            <div class="product-card" 
                 data-product-id="' . htmlspecialchars($product['productid'], ENT_QUOTES, 'UTF-8') . '" 
                 data-category-id="' . $categoryId . '" 
                 data-original-price="' . htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') . '" 
                 data-store-id="' . htmlspecialchars($product['storeid'], ENT_QUOTES, 'UTF-8') . '">';

                    // 割引中の場合、「セール中」のラベルを表示
                    if (!is_null($product['discounted_price'])) {
                        echo '
                <div class="sale-label">
                    <img src="./images/sale.png" alt="セール中">
                </div>';
                    }

                    echo '
                <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" alt="Product Image">
                <div class="product-info">
                    <p><strong>名前：</strong>' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</p>
                    <p class="productCategory"><strong>カテゴリー：</strong>' . htmlspecialchars($product['cname'], ENT_QUOTES, 'UTF-8') . '</p>
                    <p><strong>元値段：</strong>' . htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') . '</p>
                    <div class="discount">
                        <span><strong>割引後の値段：</strong></span> 
                        <span class="discounted-price">' . $discountedPrice . '</span>
                    </div>
                </div>
            </div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>

        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryButtons = document.querySelectorAll('.category button');
                const productCards = document.querySelectorAll('.product-card');
                const selectedCategories = new Set(); // 選択されたカテゴリを管理

                // "All"ボタンを取得
                const allCategoriesButton = document.querySelector('.all-categories');

                categoryButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const categoryId = this.getAttribute('data-category-id');

                        // "All"ボタンがクリックされた場合、すべてをリセット
                        if (!categoryId) {
                            selectedCategories.clear();
                            categoryButtons.forEach(btn => btn.classList.remove('active'));
                            this.classList.add('active'); // "All"ボタンをアクティブに

                            // 全商品を表示
                            productCards.forEach(card => card.style.display = 'block');
                            return;
                        }

                        // "All"以外のカテゴリを選択
                        if (selectedCategories.has(categoryId)) {
                            // すでに選択済みの場合は選択を解除
                            selectedCategories.delete(categoryId);
                            this.classList.remove('active');
                        } else {
                            // 新しく選択された場合は追加
                            selectedCategories.add(categoryId);
                            this.classList.add('active');
                        }

                        // "All"ボタンの状態を更新
                        if (selectedCategories.size === 0) {
                            allCategoriesButton.classList.add('active');
                        } else {
                            allCategoriesButton.classList.remove('active');
                        }

                        // 商品をフィルタリング
                        filterProducts();
                    });
                });

                function filterProducts() {
                    if (selectedCategories.size === 0) {
                        // 何も選択されていない場合は全商品を表示
                        productCards.forEach(card => card.style.display = 'block');
                        return;
                    }

                    // 選択されたカテゴリのいずれかに一致する商品を表示
                    productCards.forEach(card => {
                        const cardCategoryId = card.getAttribute('data-category-id');
                        if (selectedCategories.has(cardCategoryId)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        </script>

        <div id="product-dialog" class="dialog" style="display: none;">
            <div class="dialog-content">
                <p><strong>商品名：</strong><span id="dialog-product-name"></span></p>
                <p><strong>値段：</strong><span id="dialog-product-price"></span></p>
                <p><strong>割引率：</strong>
                    <input type="number" id="discount-rate" placeholder="割引率 (%)" min="0" max="100" step="1">
                    <strong>%</strong>
                </p>
                <p><strong>割引後の値段：</strong>¥<span id="discounted-price"></span></p>
                <button id="apply-discount">確定</button>
                <button id="cancel-discount-btn">割引キャンセル</button>
                <button id="cancel-discount">キャンセル</button>
            </div>
        </div>
    </main>
    <footer>
        <script src="./scripts/discount.js"></script>
    </footer>
</body>

</html>