click.ecc.ac.jp
se2a_24_bugs
mZ2n6byC

1プロフィール編集 (Profile Edit)======> SUONG
    1.1 名前 (sname) を編集できないようにする
    1.2 デザインの修正
    1.3 各項目に編集ボタンを追加（ボタンを押してから項目を編集する）
    1.4 ストアリンクをコピーするボタンを追加
    1.5 パスワード変更項目を追加する？

1.1 マネージャーメイン (Manager Main)======> SUONG
    1.1.1デザインを修正し、左右を均等にする
2. 登録 (Register)======> HAI
    2.1パスワードを2回入力し、比較する
    2.2パスワードの最低文字数を設定
    2.3PHP Mailer を使って Gmail で認証する
    2.4WRB 用の Gmail アカウントを作成
3. 登録 (Register)======> HAI
    3.1ストア作成時、active カラムが「activated」の場合、各ストアに専用の画像フォルダを作成
    3.2現在、1つのデバイスでのみログイン可能
4. ログイン (Login)======> ご
    4.1 ログイン状態を COOKIE に保存（username と token の有効期限を修正：loginP.php ファイル）
    4.2 チェックがない場合は有効期限を0に設定
    4.3 パスワードを忘れた場合、メールアドレスを確認してパスワードを変更する（新しいページを作成）
    4.4 ハッシュ化されたパスワードを復号して比較する

5. ユーザーテーブル (User Table)
    大文字・小文字を区別する
    status カラムを追加し、「pending」と「activated」の2つの状態を持たせる
    パスワードをハッシュ化して保存
7. 商品追加 (AddProduct)======> LAN
    7.1 SQL ファイルのクエリを確認し、ローカル環境で実行する
    7.2 データを挿入する前に、バーコード (barcode)、商品名 (pname)、商品コード (pcode) の重複をチェックする
    7.3 データベースに商品を追加する

8. メイン (Main)======> LAN
    8.1メインページの ヘッダー と ナビメニュー を完成させる
全体 (All)
エラーが発生した場合、エラーページにリダイレクトし、URL にエラーコードを表示する。エラーログにコードを記録して確認できるようにする

===================================================================
l profile Edit======> SUONG
    1.1ko cho sửa tên sname ở Edit ??
    1.2sửa design
    1.3thêm nút chỉnh sửa cho từng mục (bấm vào nút mới chỉnh sử mục tương ứng)
    1.4thêm nút coppy store link
    1.5 ? thêm mục đổi mật khẩu?

1.1 Manager Main ======>SUONG
sửa design cân bằng 2 bên

2 Register ======>HAI
so sánh 2 password khi đăng ký
mật khẩu tối thiểu 
dùng phpMailer để xác thực gmail.
tạo gmail của WRB

3 register ======>HAI
khi tạo cửa hàng và cột ative là activated thì 1 cửa hàng 1 folder image riêng
hiện tại chỉ đăng nhập dc 1 thiết bị

4 login ======>ご
    4.1 ログイン状態 ->COOKIE (username, token) time を直す(loginP.php ファイル)
    4.2 チェックなければ time 0
    4.3 パスワード忘れた場合は メールアドレスを確認して変更する (新しいページ作成)
    4.4パスワードを hash から解除して比較する

5 user table 
phân biệt chữ lớn và nhỏ
sửa bảng user thêm cột status 2 trạng thái là pending và activated.
mã hóa hash password


7 addproduct ======>LAN
7.1 kiểm tra sql trong file sql rồi chạy trên local
7.2 trc khi insert kiểm tra barcode, pname, pcode ko dc trùng.
7.3 thêm sản phẩm vào db.


8. main ======>LAN
cho hoàn thành header và navmenu của main

All
khi có lỗi điều hướng đến error và kèm mã lỗi lên url, note mã lỗi vào log để tra
mỗi lần tạo page mới phải include auth=check.php

メモ

xóa cookie ở màn hình cửa hàng
sau khi đăng nhập->so sánh và tạo token lưu vao cookie, khi vào màn hình authcheck-> lấy dữ liệu vào session





ubuntu:
icacls haikey2.pem /inheritance:r
icacls haikey2.pem /grant:r "${env:USERNAME}:(R)"

scp -i "C:\Sites\ec2\haikey2.pem" -r "C:\Sites\we are" ubuntu@54.145.40.61:/var/www/html

cd "C:\Sites\ec2\"

ssh -i "haikey2.pem" ubuntu@3.94.208.118


cd /var/www/html/we-are-bugs
git pull origin main



git clone https://github.com/haipham0209/we-are-bugs.git











<section id="product-section" class="category">
    <?php foreach ($groupedProducts as $categoryId => $products): ?>
        <div class="group" id="category-<?php echo $categoryId; ?>">
            <h1 class="title">
                <?php 
                    
                    echo ($categoryId == 1) ? "Men" : (($categoryId == 2) ? "Women" : "Child"); 
                ?>
            </h1>
            <div class="product-showcase">
                <?php renderProducts($products); ?>
            </div>
            <button class="show-more-btn" data-group="category-<?php echo $categoryId; ?>">Show More</button>
        </div>
    <?php endforeach; ?>
</section>

<?php include 'products.php'; ?>

<div class="product-container">
    <?php foreach ($categories as $categoryName => $products): ?>
        <div class="group" id="<?php echo $categoryName; ?>">
            <h1 class="title"><?php echo ucfirst($categoryName); ?></h1>
            <div class="product-showcase">
                <?php foreach ($products as $product): ?>
                    <div class="product-content">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <p class="rotated-text">
                            <?php echo $product['name']; ?><br><?php echo $product['price']; ?> ¥
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($products) > 3): ?>
                <button class="show-more-btn" data-group="<?php echo $categoryName; ?>">Show More</button>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>


<!-- ----------------------------------------------------------- -->
<section class="category">

<h1 class="title">Men</h1>

<!-- <div class="filter-buttons">
    <button class="filter-button">Show All</button>
    <button class="filter-button">Men</button>
    <button class="filter-button">Women</button>
    <button class="filter-button">Children</button>
    <button class="filter-button">Special Events</button>
</div> -->
<!-- Product Showcase -->
<div class="product-showcase">
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
    <div class="product-content">
        <img src="./images/facebook.png" alt="White Dress" />
        <p class="rotated-text">White Dress<br>8000 ¥</p>
    </div>
</div>
<button class="show-more-btn">Show More</button>
<h2 class="collection-title">Fall-Winter Collection</h2>
</section>



<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.7/lottie.min.js"></script>