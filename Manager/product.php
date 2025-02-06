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
    SELECT p.productid, p.pname, p.price, p.costPrice, p.discounted_price, p.description, p.stock_quantity, 
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
    <link rel="stylesheet" href="./styles/proMana.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/cameraScan.js"></script>
    <script src="./scripts/search_productphp.js"></script>
    <title>商品管理</title>
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
            <a href="main.php">
                <img class="home" src="./images/home.png" alt="Home Mana">
            </a>
        </div>
    </header>
    <main>
        <div id="overlay" style="display: none;" onclick="toggleCamera()"></div>
        <div id="camera" style="display: none;">
            <button id="stopBtn" onclick="toggleCamera()">カメラ停止</button>
        </div>
        <p class="title">商品管理</p>

        <!-- Category -->
        <div class="category">
            <button class="category-button active" data-category-id="all">All</button>
            <?php
            if ($category_result->num_rows > 0) {
                while ($row = $category_result->fetch_assoc()) {
                    echo '<button class="category-button" data-category-id="' . $row['category_id'] . '">'
                        . htmlspecialchars($row['cname'], ENT_QUOTES, 'UTF-8') . '</button>';
                }
            } else {
                echo '<p>No categories found.</p>';
            }
            ?>
        </div>

        <!-- Add Product Button -->
        <div class="add-product">
            <a href="productAdd.php">
                <img class="addbtn" src="./images/add1.png" alt="addbtn">
            </a>
        </div>

        <!-- Product Cards -->
        <div class="all-product">
            <?php
            if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
                    $productImagePath = '../' . $product['productImage'];
                    $categoryId = !empty($product['category_id']) ? htmlspecialchars($product['category_id'], ENT_QUOTES, 'UTF-8') : '';

                    // 割引後の値段か、元の値段を表示
                    $priceToDisplay = isset($product['discounted_price']) && !is_null($product['discounted_price']) ? $product['discounted_price'] : $product['price'];

                    // 割引価格があるかチェック
                    $discounted_price = $product['discounted_price']; // 商品に割引があるか確認

                    echo '
                    <div class="product-card" data-product-id="' . htmlspecialchars($product['productid'], ENT_QUOTES, 'UTF-8') . '" data-category-id="' . $categoryId . '">
                        <a href="productEdit.php?id=' . $product['productid'] . '" class="edit-icon">
                            <img src="../images/edit.png" alt="Edit">
                        </a>
                        <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" alt="Product Image">
                        <div class="product-info">
                            <p><strong>名前：</strong>' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</p>
                            <p class="productCategory"><strong>カテゴリー：</strong>' . htmlspecialchars($product['cname'], ENT_QUOTES, 'UTF-8') . '</p>
                            <p><strong>原価：</strong>' . htmlspecialchars($product['costPrice'], ENT_QUOTES, 'UTF-8') . '</p>';
                    // 割引がある場合、割引価格を表示
                    if ($discounted_price !== null) {
                        echo '
                                <p style="color: red;"><strong>値段：</strong>' . htmlspecialchars($priceToDisplay, ENT_QUOTES, 'UTF-8') . '</p>
                                <p class="discountNotice" style="color: red;">割引中</p>';
                    } else {
                        echo '<p><strong>値段：</strong>' . htmlspecialchars($priceToDisplay, ENT_QUOTES, 'UTF-8') . '</p>';
                    }
                    echo ' <p><strong>説明：</strong>' . htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') . '</p>
                        </div>
                        <div class="stock">在庫: ' . htmlspecialchars($product['stock_quantity'], ENT_QUOTES, 'UTF-8') . '</div>
                    </div>';
                }
            } else {
                echo '<p>No products found.</p>';
            }
            ?>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryButtons = document.querySelectorAll('.category-button');
                const productCards = document.querySelectorAll('.product-card');
                const selectedCategories = new Set(); // 選択されたカテゴリを保存するSet

                categoryButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const categoryId = this.dataset.categoryId;

                        // "All"ボタンがクリックされた場合、すべてをリセット
                        if (categoryId === 'all') {
                            selectedCategories.clear(); // 選択状態をリセット
                            categoryButtons.forEach(btn => btn.classList.remove('active'));
                            this.classList.add('active'); // "All"ボタンをアクティブに

                            // 全商品を表示
                            productCards.forEach(card => card.style.display = 'block');
                            return;
                        }

                        // "All"以外のカテゴリがクリックされた場合
                        if (selectedCategories.has(categoryId)) {
                            // 選択済みの場合は解除
                            selectedCategories.delete(categoryId);
                            this.classList.remove('active');
                        } else {
                            // 新しく選択された場合は追加
                            selectedCategories.add(categoryId);
                            this.classList.add('active');
                        }

                        // "All"ボタンのアクティブ状態を解除
                        document.querySelector('[data-category-id="all"]').classList.remove('active');

                        // 選択されているカテゴリがなくなったら "All" をアクティブに戻す
                        if (selectedCategories.size === 0) {
                            document.querySelector('[data-category-id="all"]').classList.add('active');
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
                        const cardCategoryId = card.dataset.categoryId;
                        if (selectedCategories.has(cardCategoryId)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        </script>

    </main>
    <footer></footer>
</body>

</html>