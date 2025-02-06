<?php
//xóa coookie khi trang login dc khởi động
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 86400, "/");
}
if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 86400, "/");
}
if (isset($_COOKIE['userid'])) {
    setcookie('userid', '', time() - 86400, "/");
}
if (isset($_COOKIE['loggedin'])) {
    setcookie('loggedin', '', time() - 86400, "/");
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/StoreLogin.css">
    <title>ログイン</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>
    <div class="container">
        <!-- ロゴ部分 -->
        <div class="logo">
            <h1>WRB</h1>
            <p>～Fashion & Boutique～</p>
        </div>
        <div class="login-form">
            <h2>ログイン</h2>
            <form class="login-form2" action="./php/StoreLoginP.php" method="POST">
                <div class="login-info">
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'account_pending'): ?>
                        <span style="color: red;">アカウント見つからない</span>
                    <?php endif; ?>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'username_not_found'): ?>
                        <span style="color: red;">ユーザー名が存在しない！</span>
                    <?php endif; ?>
                    <p>ユーザー名</p>
                    <input type="text" id="username" name="username" placeholder="ユーザー名を入力" required value="<?= isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '' ?>">
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'incorrect_password'): ?>
                        <span style="color: red;">パスワード違います</span>
                    <?php endif; ?>
                    <p>パスワード</p>
                    <input type="password" id="password" name="password" placeholder="パスワードを入力" required>
                    <button type="submit">ログイン</button>
                    <div class="form-row">
                        <label class="custom-checkbox">
                            <input type="checkbox" id="checkbox_remember_account" name="checkbox_remember_account">
                            <span class="checkmark"></span>ログイン状態を保存する
                        </label>
                    </div>
                </div>
            </form>

            <!-- loading -->

            <div id="loading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center;">
                <div id="lottie"></div>
            </div>
            <!-- <script>
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

                // Hiện loading animation khi form được gửi
                document.querySelector('.login-form2').addEventListener('submit', function(event) {
                    document.getElementById('loading').style.display = 'flex';
                    // Không cần dùng setTimeout, form sẽ tự động gửi
                });
            });
        </script> -->
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
        document.querySelector('.login-form2').addEventListener('submit', function (event) {
            event.preventDefault();  // Ngừng hành động submit mặc định

            // Hiển thị loading animation
            document.getElementById('loading').style.display = 'flex';

            // Đợi 2,5 giây rồi mới gửi form
            setTimeout(() => {
                this.submit();  // Gửi form sau 2,5 giây
            }, 1000);
        });
    });
</script>


            <!-- loading -->

            <div class="register-link">
                <p>Don’t have an account yet? <a href="./StoreRegister.php">Signup</a></p>
                <div class="pass">
                <a href="./ForgotPassword.php">パスワードを忘れた場合</a>
                </div>
            </div>

            
        </div>
    </div>
</body>
<footer style="text-align: center">
        <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;">
        </a>
    </footer>

</html>