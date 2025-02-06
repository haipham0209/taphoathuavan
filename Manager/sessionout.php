<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Session Expired</title>
  <link rel="stylesheet" href="./styles/session.css">
</head>
<body>
  <div class="overlay">
    <div class="modal">
      <h1>セッションタイムアウトしました。</h1>
      <p>再度ログインしてください</p>
      <p id="countdown">5秒後にリダイレクトします。</p> <!-- Thông báo đồng hồ đếm ngược -->
      <button id="redirect-button">ログインページ</button>
    </div>
  </div>
  <script>
    const countdownElement = document.getElementById('countdown');
    let secondsRemaining = 5;

    // Cập nhật đồng hồ đếm ngược
    const countdownInterval = setInterval(() => {
      secondsRemaining--;
      countdownElement.textContent = `${secondsRemaining}  秒後にリダイレクトします。`;

      // Nếu hết thời gian, dừng đồng hồ
      if (secondsRemaining <= 0) {
        clearInterval(countdownInterval);
      }
    }, 1000);

    // Tự động chuyển hướng sau 5 giây
    setTimeout(() => {
      window.location.href = './StoreLogin.php'; // Thay bằng đường dẫn đến trang đăng nhập của bạn
    }, 5000);

    // Chuyển hướng ngay khi nhấn nút
    document.getElementById('redirect-button').addEventListener('click', () => {
      window.location.href = './StoreLogin.php'; // Thay bằng đường dẫn đến trang đăng nhập của bạn
    });
  </script>
</body>
</html>
