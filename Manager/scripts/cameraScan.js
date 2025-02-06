// let scannerRunning = false; // Trạng thái của camera
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
// カメラのスキャン機能の開始・停止を切り替える
// function toggleScanner() {
//     if (scannerRunning) {
//         stopScanner(); // カメラを停止
//     } else {
//         startScanner(); // カメラを開始
//     }
// }

// Khởi động quét mã
function startScanner() {
    if (isCameraRunning) return; // Nếu đã chạy thì không khởi động lại

    isCameraRunning = true;
    const cameraDiv = document.getElementById('camera');
    const overlay = document.getElementById('overlay');
    cameraDiv.style.display = 'block'; // Hiện camera
    overlay.style.display = 'block'; // Hiện overlay

    Quagga.init(
        {
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: cameraDiv,
            },
            decoder: {
                readers: ["ean_reader", "code_128_reader", "upc_reader"], // Các loại barcode
            },
        },
        (err) => {
            if (err) {
                console.error(err);
                alert("カメラを起動できませんでした。");
                stopScanner();
                return;
            }
            Quagga.start();
        }
    );

    // Khi phát hiện mã, tắt camera và điền vào ô barcode
    Quagga.onDetected((data) => {
        const code = data.codeResult.code;

        // Gửi mã vạch đến server
        fetch('./php/getProductByBarcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ barcode: code }),
        })
        .then((response) => response.json())
        .then((product) => {
            if (product && product.productid) {
                // Chuyển hướng đến trang chỉnh sửa sản phẩm
                window.location.href = `./productEdit.php?id=${product.productid}`;
            } else {
                alert('Product not found!');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });

        stopScanner();
    });
}

// Dừng quét mã và tắt camera
function stopScanner() {
    if (!isCameraRunning) return; // Nếu camera chưa bật thì bỏ qua

    Quagga.stop();
    document.getElementById('camera').style.display = 'none'; // Ẩn camera
    document.getElementById('overlay').style.display = 'none'; // Ẩn overlay
    isCameraRunning = false;
}

// Lắng nghe sự kiện nhấn ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { // Nếu phím được nhấn là ESC
        stopScanner(); // Gọi hàm để tắt camera và ẩn overlay
    }
});

// Lắng nghe sự kiện click vào overlay (đảm bảo phần tử overlay tồn tại trong DOM)

document.getElementById('overlay').addEventListener('click', function () {
    stopScanner(); // Tắt camera khi overlay được click
});

// Gắn sự kiện cho nút bắt đầu quét
document.getElementById('start-scan').addEventListener('click', toggleCamera);
