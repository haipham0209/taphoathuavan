<?php
// Gọi file xác thực người dùng trước khi load nội dung trang
include('./php/auth_check.php');
include('./php/storeinfo.php');
include('./php/db_connect.php'); // データベース接続ファイル

// ユーザーがログインしているか確認
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit;
}
// echo "55";
// echo $store['logopath'];


// データベースからロゴパスを取得
// $stmt = $conn->prepare("SELECT logo_path FROM user WHERE userid = ?");
// $stmt->bind_param("i", $_SESSION['userid']);
// $stmt->execute();
// $stmt->bind_result($logoPath);
// $stmt->fetch();
// $stmt->close();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>プロフィール</title>
    <link rel="stylesheet" href="./styles/profileEdit.css">
    <script src="./scripts/profile.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <header style="text-align: center">
    <a href="#">
                <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'">
            </a>
    </header>
    <main>

    <!-- <div class = "back_button">
        <a href="#">
        <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'"> -->
        <!-- <img src="./images/back_button2.png" alt="Back Icon" onclick="location.href='main.php'">
        </a> -->
    <!-- </div> --> 
    <div class="container">
        <div class="profile-form">
            <form class="edit-form" action="./php/storeProEditP.php" method="POST"enctype="multipart/form-data">
                    <!--  -->
                <p>1:3の画像をご利用ください</p>

                <!--  -->
                <div class="form">
                <div class="form logo-container">
                    <!-- Hiển thị logo -->
                    <img id="logo" src="<?php echo htmlspecialchars($_SESSION['logopath'] ?? 'default-logo.png'); ?>" alt="Logo" style="width: 240px; height: 80px; border: 1px solid #ccc; padding: 5px; border-radius: 5px;" />

                    <!-- Nút chọn ảnh -->
                    <button type="button" id="changeLogoButton">ロゴを変更</button>
                    <input type="file" id="fileInput" name="logoFile" accept="image/*" style="display: none;" />
                </div>

                <script>
                    const changeLogoButton = document.getElementById('changeLogoButton');
                    const fileInput = document.getElementById('fileInput');
                    const logo = document.getElementById('logo');

                    // Khi bấm nút, giả lập click vào input file
                    changeLogoButton.addEventListener('click', function() {
                        fileInput.click();
                    });

                    // Khi người dùng chọn tệp
                    fileInput.addEventListener('change', function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                logo.src = e.target.result; // Hiển thị ảnh mới
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                    <input type="hidden" name="userid" id="userid" value="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : ''; ?>">

                    <input type="hidden" name="userid" id="userid" value="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : ''; ?>">
                    <div class="form-row">
                        <label for="shop-name">店名</label>
                        <div class="group">
                            <input type="text" id="shop-name" name="sname" value="<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>" <?php echo isset($_SESSION['sname']) && !empty($_SESSION['sname']) ? 'readonly' : ''; ?> required>
                            <img src="" class="icon_1">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="address">住所</label>
                        <div class="group">
                            <input type="text" id="address" name="address" value="<?php echo isset($_SESSION['address']) ? htmlspecialchars($_SESSION['address']) : ''; ?>" readonly required>
                            <img src="./images/pen.png" alt="編集" class="icon" onclick="toggleEdit('address')">
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="phone">電話</label>
                        <div class="group">
                            <input type="text" id="phone" name="phone" value="<?php echo isset($_SESSION['tel']) ? htmlspecialchars($_SESSION['tel']) : ''; ?>" readonly required>
                            <img src="./images/pen.png" alt="編集" class="icon" onclick="toggleEdit('phone')">
                        </div>
                    </div>
                    <div class="save">
                        <button type="submit" class="save-button">保存</button>
                    </div>
                </div>
                <script>
                    function toggleEdit(fieldId) {
                        var field = document.getElementById(fieldId);
                        field.readOnly = !field.readOnly;
                        field.focus();
                    }
                </script>

                <!-- Link to open the dialog -->
                <div class="edit-password-link">
                    <button onclick="openDialog()">パスワードを編集</button>
                </div>
                <div class="edit-password-link">
                <button onclick="location.href='./aboutStoreinfo.php';">店舗紹介情報編集</button>

                </div>
                <!-- Password Change Modal -->
                <!-- Dialog password không yêu cầu trong form chính -->
                <div id="passwordDialog" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeDialog()">&times;</span>
                        <h2>パスワードを変更</h2>
                        <form action="./php/changePassword.php" method="POST">
                            <div>現在のパスワード</div>
                            <input type="password" id="old-password" name="old_password">

                            <div>新しいパスワード</div>
                            <input type="password" id="new-password" name="new_password">

                            <div>新しいパスワード（確認）</div>
                            <input type="password" id="confirm-password" name="confirm_password">

                            <button type="submit" class="confirm-btn">確認</button>
                        </form>
                    </div>
                </div>


                <div class="store-link">
                    <a href="../main2.php?sname=<?php echo isset($_SESSION['sname']) ? htmlspecialchars($_SESSION['sname']) : ''; ?>" target="_blank" rel="noopener noreferrer">
                        ストアのリンクアドレス
                    </a>
                    <!-- Copy Icon -->
                    <img src="./images/copy.png" alt="Copy Link" class="copy-icon" onclick="copyLink()" style="cursor: pointer; width: 20px; height: 20px; margin-left: 8px;">
                </div>

                <div>
                    <button type="button" class="delete-button" onclick="openDeleteDialog()">アカウントを削除</button>
                </div>

                <!-- アカウント削除確認モーダル -->
                <div id="deleteDialog" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-btn" onclick="closeDeleteDialog()">&times;</span>
                        <h2>アカウントを削除</h2>
                        <p>アカウントを削除するにはパスワードを入力してください。</p>
                        <form id="deleteForm" action="./php/accountDeleteP.php" method="POST" onsubmit="return confirmDelete()">
                            <input type="hidden" name="userid" value="<?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : ''; ?>">
                            <div>パスワード</div>
                            <input type="password" id="delete-password" name="password" required>
                            <button type="submit" class="confirm-btn">削除</button>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_password'): ?>
                    <p style="color: red;">パスワードが間違っています。</p>
                <?php endif; ?>

                <script>
                    function openDeleteDialog() {
                        document.getElementById('deleteDialog').style.display = 'block';
                    }

                    function closeDeleteDialog() {
                        document.getElementById('deleteDialog').style.display = 'none';
                    }

                    // モーダル外クリックで閉じる
                    document.addEventListener('click', function(event) {
                        const deleteDialog = document.getElementById('deleteDialog');
                        if (event.target === deleteDialog) {
                            closeDeleteDialog();
                        }
                    });

                    function closeDialog() {
                        document.getElementById('passwordDialog').style.display = 'none';
                    }

                    // モーダル外クリックで閉じる
                    document.addEventListener('click', function(event) {
                        const passwordDialog = document.getElementById('passwordDialog');
                        if (event.target === passwordDialog) {
                            closeDialog();
                        }
                    });
                </script>

            </form>
        </div>
    </div>


    <!-- loading -->

    <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
        <div id="lottie"></div>
    </div>
    <script>
        // Lottie 起動
        document.addEventListener('DOMContentLoaded', function() {
            // Lottie
            const animation = lottie.loadAnimation({
                container: document.getElementById('lottie'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: './images/loading.json'
            });

            // animation
            document.querySelector('.edit-form').addEventListener('submit', function(event) {
                // 
                event.preventDefault();
                document.getElementById('loading').style.display = 'flex';

                // set time animation
                setTimeout(() => {
                    this.submit();
                }, 1500);
            });
        });
    </script>

    <!-- loading -->
    </main>
    <footer style="text-align: center">
    <!-- <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='main.php'">
        </a> -->

</footer>
</body>


</html>