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

//eror//
// Kiểm tra xem có lỗi trong URL hay không và lấy các giá trị từ URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
$productname = isset($_GET['productname']) ? urldecode($_GET['productname']) : '';
$price = isset($_GET['price']) ? urldecode($_GET['price']) : '';
$costPrice = isset($_GET['costprice']) ? urldecode($_GET['costprice']) : '';
$description = isset($_GET['description']) ? urldecode($_GET['description']) : '';
$stockQuantity = isset($_GET['stock']) ? urldecode($_GET['stock']) : '';
$barcode = isset($_GET['barcode']) ? urldecode($_GET['barcode']) : '';
//end

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProductAdd</title>
    <!-- <link rel="stylesheet" href="../styles/All.css"> -->
    <link rel="stylesheet" href="./styles/addProduct.css">
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <main>
        <p class="addTitle">商品追加</p>
        <div class="addContainer">
            <form class="proAddForm" action="./php/addProductP.php" method="POST" enctype="multipart/form-data">
                <!-- Trường chọn ảnh -->
                <label for="productImage">商品画像:</label>
                <img id="imagePreview" src="#" alt="プレビュー画像" style="display:none; max-width:200px; margin-top:10px;">
                <?php if ($error === 'imgerrort'): ?>
                    <p style="color: red; margin:0;">画像をアップロードしてください。</p>
                <?php endif; ?>
                <input type="file" id="productImage" name="productImage" accept="image/*" onchange="previewImage(event)">
                <br>
                

                <label for="category">カテゴリー:</label>
                <div style="display: flex; align-items: center;">
                    <input type="text" id="categoryText" name="categoryText" placeholder="新しいカテゴリーを入力" value="" />
                    <select id="category" name="category" required onchange="updateCategoryText()">
                        <option value="new" selected>新規作成</option> <!-- Chữ ngắn hơn -->
                        <?php
                        if ($category_result->num_rows > 0) {
                            while ($row = $category_result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['category_id']) . "'>" . htmlspecialchars($row['cname']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>カテゴリーがありません</option>";
                        }
                        ?>
                    </select>
                </div>
                <br>

                <script>
                    function updateCategoryText() {
                        const categoryDropdown = document.getElementById('category');
                        const categoryTextInput = document.getElementById('categoryText');
                        const selectedValue = categoryDropdown.value;

                        if (selectedValue === "new") {
                            categoryTextInput.readOnly = false; // Cho phép chỉnh sửa
                            categoryTextInput.value = ""; // Xóa giá trị cũ
                            categoryTextInput.placeholder = "カテゴリー選択か入力";
                            categoryTextInput.focus(); // Đưa con trỏ vào ô nhập
                        } else {
                            categoryTextInput.readOnly = true; // Khóa ô nhập
                            categoryTextInput.value = categoryDropdown.options[categoryDropdown.selectedIndex].text;
                        }
                    }

                    // Đảm bảo ô nhập mặc định có thể chỉnh sửa khi tải trang
                    document.addEventListener("DOMContentLoaded", function () {
                        updateCategoryText();
                    });
                </script>


                <!-- Các trường khác -->
                <?php if ($error === 'exist'): ?>
                    <p style="color: red; margin:0;">商品名またはバーコードが既に存在します</p>
                <?php endif; ?>
                <label for="pname">商品名:</label>
                <input type="text" id="pname" name="pname" required value="<?php echo htmlspecialchars($productname); ?>">
                <br>

                <label for="price">価格:</label>
                <input type="number" id="price" name="price" required min="0" step="0.1" value="<?php echo htmlspecialchars($price); ?>">
                <br>

                <label for="costPrice">仕入れ価格:</label>
                <input type="number" id="costPrice" name="costPrice" required min="0" step="0.1" value="<?php echo htmlspecialchars($costPrice); ?>">
                <br>

                <label for="description">商品説明:</label>
                <textarea id="description" name="description" rows="4" cols="50" required><?php echo htmlspecialchars($description); ?></textarea>
                <br>

                <label for="stockQuantity">在庫数量:</label>
                <input type="number" id="stockQuantity" name="stockQuantity" required min="1" value="<?php echo htmlspecialchars($stockQuantity); ?>">
                <br>

                <?php if ($error === 'exist'): ?>
                    <p style="color: red; margin:0;">商品名またはバーコードが既に存在します</p>
                <?php endif; ?>
                <script>
                    // Lắng nghe sự kiện 'barcodeDetected' để cập nhật mã vào ô input
                    document.addEventListener('barcodeDetected', (event) => {
                        const barcode = event.detail;
                        document.getElementById('barcode').value = barcode;
                    });
                </script>
                <label for="barcode">バーコード:</label>
                <input type="text" id="barcode" name="barcode" required value="<?php echo htmlspecialchars($barcode); ?>">
                <button type="button" id="start-scan">カメラでスキャン</button>
                <!-- Div để hiển thị camera -->
                <!-- <div id="camera" style="display: none;"></div> -->
                <div id="overlay" style="display: none;" onclick="toggleScanner()"></div>
                <div id="camera" style="display: none;">
                    <button id="stopBtn"type="button" onclick="toggleScanner()">カメラ停止</button>
                </div>

                <button type="submit">商品を追加する</button>
            </form>
            <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
                <div id="lottie"></div>
            </div>
            <script>
    // Lottie 起動
    document.addEventListener('DOMContentLoaded', function () {
        // Lottie animation
        const animation = lottie.loadAnimation({
            container: document.getElementById('lottie'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: './images/loading.json'
        });

        // Khi nhấn nút submit
        document.querySelector('.proAddForm').addEventListener('submit', function (event) {
            // event.preventDefault();  // Ngừng hành động submit mặc định

            // Hiển thị loading animation
            document.getElementById('loading').style.display = 'flex';

            // Đợi 2,5 giây rồi mới gửi form
            setTimeout(() => {
                this.submit();  // Gửi form sau 2,5 giây
            }, 2500);
        });
    });
</script>

        </div>
    </main>
    <script src="./scripts/camera.js"></script>
</body>

</html>

<?php
// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>