<?php
include('./php/auth_check.php');
include('./php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$storeid = $_SESSION['storeid']; 

$category_sql = "SELECT category_id, cname FROM category WHERE storeid = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $storeid); 
$stmt->execute();
$category_result = $stmt->get_result();
$category_ids = isset($_GET['category_ids']) ? explode(',', $_GET['category_ids']) : [];

// Truy vấn lấy sản phẩm bán chạy, có thể lọc theo category
$best_sellers_sql = "
    SELECT 
        p.productid, 
        p.pname, 
        p.price, 
        p.productImage, 
        p.category_id,
        SUM(od.quantity) AS total_quantity
    FROM product p
    JOIN order_details od ON p.productid = od.productid
    WHERE p.storeid = ?
";

if (!empty($category_ids)) {
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $best_sellers_sql .= " AND p.category_id IN ($placeholders)";
}

$best_sellers_sql .= "
    GROUP BY p.productid, p.pname, p.price, p.productImage, p.category_id
    ORDER BY total_quantity DESC
    LIMIT 3
";

$params = array_merge([$storeid], $category_ids);
$types = str_repeat('i', count($params));
$stmt = $conn->prepare($best_sellers_sql);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$best_sellers_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/setBestSel.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/cameraPos.js"></script>
    <script src="./scripts/search.js"></script>
    <title>Best Sellers</title>
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
        <p class="title">Best Sellers</p>

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
        <!-- Best sellers products -->
        <div class="all-product">
            <?php
            if ($best_sellers_result->num_rows > 0) {
                while ($product = $best_sellers_result->fetch_assoc()) {
                    $productImagePath = '../' . $product['productImage']; 

                    echo '
                        <div class="product-card" data-category-id="' . htmlspecialchars($product['category_id'], ENT_QUOTES, 'UTF-8') . '">
                            <img src="' . htmlspecialchars($productImagePath, ENT_QUOTES, 'UTF-8') . '" alt="Product Image">
                            <div class="product-info">
                                <p><strong>商品名:</strong> ' . htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') . '</p>
                                <p><strong>値段:</strong> ¥' . htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') . '</p>
                                <p><strong>販売:</strong> ' . htmlspecialchars($product['total_quantity'], ENT_QUOTES, 'UTF-8') . ' 個</p>
                            </div>
                        </div>';
                }
            } 
            ?>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryButtons = document.querySelectorAll('.category button');
                const productCards = document.querySelectorAll('.product-card');
                const selectedCategories = new Set(); // Manage selected categories

                // "All" button
                const allCategoriesButton = document.querySelector('.all-categories');

                categoryButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const categoryId = this.getAttribute('data-category-id');

                        // Reset if "All" button is clicked
                        if (!categoryId) {
                            selectedCategories.clear();
                            categoryButtons.forEach(btn => btn.classList.remove('active'));
                            this.classList.add('active'); // Mark "All" button as active

                            // Show all products
                            productCards.forEach(card => card.style.display = 'block');
                            return;
                        }

                        // Handle other category selections
                        if (selectedCategories.has(categoryId)) {
                            // Deselect if already selected
                            selectedCategories.delete(categoryId);
                            this.classList.remove('active');
                        } else {
                            // Add to selected categories if not selected
                            selectedCategories.add(categoryId);
                            this.classList.add('active');
                        }

                        // Update "All" button status
                        if (selectedCategories.size === 0) {
                            allCategoriesButton.classList.add('active');
                        } else {
                            allCategoriesButton.classList.remove('active');
                        }

                        // Filter products
                        filterProducts();
                    });
                });

                function filterProducts() {
                    if (selectedCategories.size === 0) {
                        // Show all products if no category is selected
                        productCards.forEach(card => card.style.display = 'block');
                        return;
                    }

                    // Show only products that match selected categories
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
    </main>
    <footer></footer>
</body>

</html>
