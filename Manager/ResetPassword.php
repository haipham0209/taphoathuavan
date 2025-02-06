<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワードリセット</title>
</head>

<body>
    <h2>パスワードリセット</h2>
    <form action="./php/ProcessResetPassword.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <p>新しいパスワード</p>
        <input type="password" name="new_password" required>
        <p>新しいパスワード（確認用）</p>
        <input type="password" name="confirm_password" required>
        <button type="submit">リセット</button>
    </form>
</body>

</html>