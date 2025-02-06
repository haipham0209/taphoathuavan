<?php
include('./php/auth_check.php'); // Kiểm tra quyền người dùng
include('./php/db_connect.php'); // Kết nối cơ sở dữ liệu
include('./php/POS_process.php'); // Kết nối cơ sở dữ liệu
// include('./php/POS_product.php');

// Khởi tạo kết nối cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="./styles/POS.css">
    <!-- <link rel="stylesheet" href="./styles/proMana.css"> -->
    <script src="./scripts/cameraPos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="./scripts/POS.js"></script>
    <script src="./scripts/search.js"></script>
<!-- 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
    <title>POS</title>
</head>
<body>
<header>
        <div class="main-navbar">
            <div class="search-scan">
                <input type="text" name="barcode" id="barcode-input" class="search-bar" placeholder="商品名又はコード入力">            
                <div id="barcode-suggestions" class="suggestions-list" style="display:none;"></div>
                <img src="./images/camera-icon.png" class="camera-icon" onclick="toggleCamera()">
            </div>
            <div id="suggestionList"></div>
            <script>
                let isCameraRunning = false; 

                function toggleCamera() {
                    if (isCameraRunning) {
                        stopScanner();
                        isCameraRunning = false;
                    } else {
                        startScanner();
                        isCameraRunning = true;
                    }
                }
            </script>
            <a href="main.php">
                <img class="home" src="./images/home.png" alt="Home Mana">
            </a>
        </div>
    </header>
<main>
    <div id="camera" style="display: none;">
        <button id="stopBtn" onclick="toggleCamera()">カメラ停止</button>
    </div>
    <div class="pos">
        <h2>会計</h2>
        <div class="id-time">
            
            <p id="customer-id" hidden><?php echo generateOrderNumber($conn, $_SESSION['storeid']); ?></p>

            <div id="datetime">
                <p id="date"></p>
                <p id="time"></p>
            </div>
        </div>
        <div class="table-wrapper">
            <table id="product-table">
                <thead>
                    <tr>
                        <th>行</th>
                        <th>商品名</th>
                        <th class="num">数量</th>
                        <th>単価</th>
                        <th>小計</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Thêm trống vào đây -->
                <!-- <tr class="e">
                    <td class="sttt"></td> 
                    <td></td>
                    <td class="num1">
                        <input type="number" class="product-quantity" value="" min="1" data-barcode="" onchange="">
                    </td>
                    <td></td>
                    <td class="price2"></td>
                    <td>
                        <button class="delete-btn" title=""></button>
                    </td>
                </tr> -->
                

                </tbody>
            </table>
        </div>


        <div id="payment-form">
            <div class="pay">
                <p>支払方法:
                    <select id="payment-method">
                        <option value="cash">現金</option>
                        <option value="credit">クレジットカード</option>
                        <option value="barcode">バーコード</option>
                    </select>
                </p>
            </div>
            <div class="total">
                <p class="totalp">合計: <span id="total-price">___￥</span></p>
                <!-- <p>(税込10%)</p> -->
                <!-- <p>割引き: 
                    <input type="number" id="waribiki-input" value="0" min="0" max="100" onchange="updateTotal()"> %
                </p> -->
                <p class="totalp"><label for="received-amount">お預かり:</label></p>
                <input type="number" id="received-amount" onchange="calculateChange()">
                <p class="totalp">お釣り: <span id="change-amount">___¥</span></p>
            </div>

        </div>
            <!-- //////////////////////data 取得////////////////////////////// -->
<script src="./scripts/payment_cash.js"></script>
<!-- //////////////////////data 取得////////////////////////////// -->
        <!-- /////////////////////////////////form//////////////////////////// -->
    <form method="POST" action="./php/process_payment.php">
        <!-- Gửi tổng giá trị và số tiền đã nhận -->
        <input type="hidden" name="total_price" id="hidden-total-price" value="0"> <!-- Tổng tiền -->
        <input type="hidden" name="received_amount" id="hidden-received-amount" value="0"> <!-- Số tiền đã nhận -->

        <!-- Gửi thông tin các sản phẩm trong giỏ hàng -->

        <!-- <button type="submit" name="complete" class="button-pay">完了</button> -->
        <div class="btn_container">
            <button class="button-pay clear-btn" type="button" onclick="location.reload()">CLEAR</button>
            <button class="button-pay complete-btn" type="button" onclick="sendDataToServer()">完了</button>
        </div>




    </form>
<!-- /////////////////////////////form///////////////////////////////////////// -->

    </div>
    <!-- <script>const data = prepareFormData(); console.log("2222222222222222222222");console.log(data);</script> -->
</main>
</body>
</html>



