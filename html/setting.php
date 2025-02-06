

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Page</title>
    <link rel="stylesheet" href="../styles/All.css">
    <link rel="stylesheet" href="../styles/myPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
            </button>
            <div class="logobar">
                <img src="../images/wrb-logo.png" alt="Store Logo">
            </div>
            <button class="avatar-toggle">
                <img class="avatar" src="../images/avataricon.jpg" alt="Avatar User">
            </button>
        </div>

    <nav class="nav-menu">
        <ul>
        <li><h3><?php echo $sname; ?></h3></li>
          <li><a href="./main.php?sname=<?php echo $_GET['sname']  ?>">ホームページ</a></li>
          <li><a href="./html/product.php">商品</a></li>
          <li><a href="./html/storeInfor.php">お店について</a></li>
          <li class="support-title">サポート</li>
          <li class="support"><i class="fa fa-phone"></i><a class="support" href="tel:<?php echo $tel; ?>"><?php echo $tel; ?></a></li>
          <li class="support"><i class="fa fa-envelope"></i><a class="support" href="mail:"><?php echo $mail; ?></a></li>
          <li class="support"><i class="fa fa-map-marker"></i><a target="blank" class="support" href=""><?php echo $address; ?></a></li>
         </ul>
    </nav>
    <div class="overlay"></div>
    <nav class="nav-myPage">
        <ul>
            <li><a href="#">登録</a></li>
            <li><a href="#">ログイン</a></li>
        </ul>
    </nav>
       <div class="overlay-avatar"></div>
    </header>

    <main>
       
    </main>
    <footer>
       
    </footer>

</body>
<script src="../scripts/menu.js"></script>
<script src="../scripts/mypage.js"></script>
</html>