<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./styles/StoreRegister.css">
    <title>登録</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>
</head>

<body>

    <script>
        //local storage check
        if (localStorage.getItem('registerSuccess')) {
            localStorage.removeItem('registerSuccess');
            setTimeout(function() {
                window.location.href = './StoreLogin.php'; //loginpage 移動
            }, 0);
        }
    </script>
    <div class="container">
        <!-- ロゴ部分 -->
        <div class="logo">
            <h1>WRB</h1>
            <p>～Fashion & Boutique～</p>
        </div>

        <div class="register-form">
            <form class="register-form2" action="./php/register.php" method="post">
                <div class="register-info">
                    <h2>新規登録</h2>

                    <label for="username"></label>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'username_exists'): ?>
                        <span style="color: red;">ユーザー名がすでに存在します！</span>
                    <?php endif; ?>
                    <p>ユーザー名</p>
                    <input type="text" id="username" name="username" placeholder="ユーザー名入力" required value="<?= isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '' ?>">

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'sname_exists'): ?>
                        <span style="color: red;">店名が既に存在します！</span>
                    <?php endif; ?>
                    <label for="sname"></label>
                    <p>店名</p>
                    <input type="text" id="sname" name="sname" placeholder="店名を入力" required value="<?= isset($_GET['sname']) ? htmlspecialchars($_GET['sname']) : '' ?>">
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
                        <span style="color: red;">既に登録しているメールです！</span>
                    <?php endif; ?>
                    <label for="email"></label>
                    <p>メール</p>
                    <input type="email" id="email" name="email" placeholder="メールを入力" required value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>">

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'password_too_short'): ?>
                        <span style="color: red;">パスワードは6文字以上でなければなりません！</span>
                    <?php endif; ?>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'password_short'): ?>
                        <span style="color: red;">パスワード6文字以上入力ください</span>
                    <?php endif; ?>
                    <label for="password"></label>
                    <p>パスワード</p>
                    <input type="password" id="password" name="password" placeholder="パスワードを入力" required>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'password_mismatch'): ?>
                        <span style="color: red;">パスワードが一致しません！</span>
                    <?php endif; ?>

                    <label for="confirm_password"></label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="もう一回パスワード" required>


                    <button type="submit">登録</button>
                </div>
                <div class="login-link">
                <p>Already have account? <a href="./StoreLogin.php">Login</a></p>
            </div>
            </form>

        </div>




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
        document.querySelector('.register-form2').addEventListener('submit', function (event) {
            event.preventDefault();  // Ngừng hành động submit mặc định

            // Hiển thị loading animation
            document.getElementById('loading').style.display = 'flex';

            // Đợi 2,5 giây rồi mới gửi form
            setTimeout(() => {
                this.submit();  // Gửi form sau 2,5 giây
            }, 2500);
        });
    });
</script>

        
        
        <?php
        // if (isset($_GET['success']) && $_GET['success'] === 'true') {
        //     // cache 
        //     header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        //     header("Pragma: no-cache"); // HTTP 1.0.
        //     header("Expires: 0"); // 
        //     header('Location: ./StoreLogin.html');

        //     exit;
        // }
        ?>
    </div>
</body>
<footer style="text-align: center">
        <a href="#">
            <img src="./images/backicon.png" alt="Back Icon" style="width: 40px; height: 40px;" onclick="location.href='StoreLogin.php'">
        </a>
    </footer>


</html>