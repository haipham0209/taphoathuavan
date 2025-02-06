<?php
// include('./Manager/php/auth_check.php');

include('./Manager/php/db_connect.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productid = $_GET['id'] ?? null;
//cookie sname
if (isset($_COOKIE['storename'])) {
    $storeid = $_COOKIE['storeid'];
} else {
    die("404");
    exit;
}

if (!$productid) {
    die("Product ID not provided.");
}

$product_sql = "
    SELECT p.productid, p.pname, p.price, p.costPrice, p.description, p.stock_quantity, p.productImage, p.category_id, 
           p.barcode, c.cname AS category_name
    FROM product p
    JOIN category c ON p.category_id = c.category_id
    WHERE p.productid = ? AND p.storeid = ?";

$stmt = $conn->prepare($product_sql);
$stmt->bind_param("ii", $productid, $storeid);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    die("Product not found.");
}

$category_sql = "SELECT category_id, cname FROM category WHERE storeid = ?";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param("i", $_SESSION['storeid']);
$category_stmt->execute();
$category_result = $category_stmt->get_result();
$sname= $_COOKIE["storename"];
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Grape Nuts' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Grenze' rel='stylesheet'>
    <link rel="stylesheet" href="./styles/productDetail.css">
    <title>商品詳細</title>
</head>

<body>
    <header>
        <a href="javascript:void(0)" onclick="window.history.back();">
            <img src="./images/backicon2.png" alt="Back Icon" style="width: 40px; height: 40px;">
        </a>
        <div class="favorite-button">
            <img id="favorite-icon" src="./images/white-heart.png" style="width: 40px; height: 40px;" alt="お気に入り">
        </div>
    </header>
    <main>
        <div class="product-form">
            <div class="product-img">
                <img src="<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['pname']) ?>">
            </div>
            <div class="product-detail">
                <h1 class="description-title">Description：</h1>
                <div class="product-name">
                    <p><?= htmlspecialchars($product['pname'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <div class="product-price">
                    <p><?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?> &yen;</p>
                </div>
                <div class="prodcut-description">
                    <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="addCart">
                <button id="add-to-cart">Add to Cart</button>
            </div>
        </div>
    </main>
    <script>
        // JavaScript for toggling favorite icon
        const favoriteIcon = document.getElementById('favorite-icon');

        favoriteIcon.addEventListener('click', () => {
            if (favoriteIcon.src.includes('white-heart.png')) {
                favoriteIcon.src = './images/red-heart.png';
            } else {
                favoriteIcon.src = './images/white-heart.png';
            }
        });
    </script>
</body>

</html>