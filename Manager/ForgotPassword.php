<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
    <title>パスワードリセット</title>
    <link rel="stylesheet" href="./styles/StoreLogin.css">
</head>

<body>
    <div class="container">
        <div class="logo">
            <h1>WRB</h1>
            <p>～Password Reset～</p>
        </div>
        <div class="login-form">
            <h2>ユーザー確認</h2>
            <form action="./php/VerifyUser.php" method="POST">
                <p>ユーザー名</p>
                <input type="text" name="username" placeholder="ユーザー名を入力" required>

                <p>メールアドレス</p>
                <input type="email" name="email" placeholder="メールアドレスを入力" required>

                <button type="submit">次へ</button>
            </form>
            <div class="register-link">
                <p><a href="./StoreLogin.php">ログイン画面に戻る</a></p>
            </div>
        </div>
    </div>
</body>

</html>