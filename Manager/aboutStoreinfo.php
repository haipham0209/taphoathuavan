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

// Truy vấn dữ liệu từ bảng StoreDescriptions
$sql = "SELECT id, title, content FROM StoreDescriptions WHERE storeid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $storeid);
$stmt->execute();
$result = $stmt->get_result();

// Khởi tạo mảng để lưu dữ liệu
$descriptions = [];
while ($row = $result->fetch_assoc()) {
    $descriptions[] = $row;
}
// echo '<pre>';
// print_r($descriptions);
// echo '</pre>';
// Lưu dữ liệu vào session
$_SESSION['descriptions'] = $descriptions;

// Đóng kết nối
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>about Store Information Edit</title>
    <!-- <link rel="stylesheet" href="../styles/All.css"> -->
    <link rel="stylesheet" href="./styles/addProduct.css">
    <link rel="stylesheet" href="./styles/aboutStoreinfo.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
<main>
    <h2>お店について</h2>
        <form id="storeForm" action="./php/aboutStoreinfo.php" method="POST">
            <!-- Container để chứa các cặp title-content -->
            <input type="hidden" id="delete_ids" name="delete_ids" value="">
            <div id="descriptionContainer">
                <?php if (!empty($_SESSION['descriptions'])): ?>
                    <?php foreach ($_SESSION['descriptions'] as $index => $description): ?>
                        <div class="descriptionGroup">
                            <img src="../images/delete.png" alt="Delete" class="delete-icon" data-id="<?= $description['id'] ?>" onclick="removeDescription(this)">
                            <input type="hidden" name="id<?= $index + 1 ?>" value="<?= $description['id'] ?>"> 
                            <label for="title<?= $index + 1 ?>">タイトル:</label>
                            <input type="text" id="title<?= $index + 1 ?>" name="title<?= $index + 1 ?>" value="<?= htmlspecialchars($description['title']) ?>" required>
                            <label for="content<?= $index + 1 ?>">内容:</label>
                            <textarea id="content<?= $index + 1 ?>" name="content<?= $index + 1 ?>" required><?= htmlspecialchars($description['content'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Hiển thị trường trống nếu không có dữ liệu -->
                    <div class="descriptionGroup">
                        <img src="../images/delete.png" alt="Delete" class="delete-icon" data-id="<?= $description['id'] ?>" onclick="removeDescription(this)">
                        <label for="title1">タイトル:</label>
                        <input type="text" id="title1" name="title1" required>
                        <label for="content1">内容:</label>
                        <textarea id="content1" name="content1" required></textarea>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Nút để thêm cặp title-content -->
            <button type="button" id="addDescription">Add More</button>
            <!-- <br><br> -->

            <!-- Nút Submit -->
            <button type="submit" id="submit">Submit</button>
        </form>
    </main>
    <script>
        // JavaScript để thêm tối đa 10 cặp title-content
        document.getElementById('addDescription').addEventListener('click', function () {
            const container = document.getElementById('descriptionContainer');
            const currentCount = container.getElementsByClassName('descriptionGroup').length;

            if (currentCount < 10) {
                const newGroup = document.createElement('div');
                newGroup.className = 'descriptionGroup';
                newGroup.innerHTML = `
                    <img src="../images/delete.png" alt="Delete" class="delete-icon" onclick="removeDescription(this)">
                    <label for="title${currentCount + 1}">タイトル:</label>
                    <input type="text" id="title${currentCount + 1}" name="title${currentCount + 1}" required>
                    <label for="content${currentCount + 1}">内容:</label>
                    <textarea id="content${currentCount + 1}" name="content${currentCount + 1}" required></textarea>
                `;
                container.appendChild(newGroup);
            } else {
                alert('You can only add up to 10 descriptions.');
            }
        });
        //xóa dữ liệu khi ấn x
        function removeDescription(button) {
            const descriptionGroup = button.closest('.descriptionGroup');
            const descriptionId = button.getAttribute('data-id');

            // Xóa cặp title-content khỏi giao diện
            descriptionGroup.remove();

            // Lưu ID vào trường ẩn để xóa
            let deleteIds = document.getElementById('delete_ids');
            if (!deleteIds.value) {
                deleteIds.value = JSON.stringify([descriptionId]);
            } else {
                let currentIds = JSON.parse(deleteIds.value);
                currentIds.push(descriptionId);
                deleteIds.value = JSON.stringify(currentIds);
            }
        }
        document.getElementById('storeForm').addEventListener('submit', function (e) {
            //e.preventDefault(); // Ngăn form gửi dữ liệu mặc định

            const formData = new FormData(); // Tạo FormData để gửi dữ liệu

            // Lấy tất cả các nhóm title-content
            const descriptionGroups = document.querySelectorAll('.descriptionGroup');
            descriptionGroups.forEach((group, index) => {
                const title = group.querySelector(`[name="title${index + 1}"]`).value;
                const content = group.querySelector(`[name="content${index + 1}"]`).value;
                const id = group.querySelector(`[name="id${index + 1}"]`) ? group.querySelector(`[name="id${index + 1}"]`).value : null;

                // Thêm title, content và id vào formData nếu có
                formData.append(`descriptions[${index}][title]`, title);
                formData.append(`descriptions[${index}][content]`, content);
                if (id) formData.append(`descriptions[${index}][id]`, id);
            });

            // Lấy danh sách ID đã xóa từ trường ẩn
            const deleteIds = document.getElementById('delete_ids').value;
            if (deleteIds) {
                formData.append('delete_ids', deleteIds);
            }

            // Gửi dữ liệu đến server
            fetch('./php/Storeinfo.php', { 
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data saved successfully!');
                    location.reload(); // Tải lại trang sau khi lưu thành công
                } else {
                    alert('Error saving data: ' + data.message);
                }
            })
            .catch(err => console.error('Error:', err));
        });

    </script>
    
    <!-- INSERT INTO StoreDescriptions (storeid, title, content)
VALUES
(1, 'Welcome to Store A', 'Store A offers a wide range of products, including fresh produce and electronics.'),
(1, 'About Us', 'Store A is committed to providing the best quality products and services. Established in 2020.'),
(1, 'Contact Info', 'You can reach Store A at 123-456-7890 or email us at contact@storea.com.'),
(1, 'Welcome to Store B', 'Store B specializes in home goods and furniture. Visit us today!'),
(1, 'Our Mission', 'At Store B, we aim to bring comfort to your home at affordable prices.'),
(1, 'Welcome to Store C', 'Store C offers the best deals on technology and gadgets. Check out our latest promotions!'); -->

</body>

</html>

