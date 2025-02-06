let scannerRunning = false; // Trạng thái của camera

// カメラのスキャン機能の開始・停止を切り替える
function toggleScanner() {
    if (scannerRunning) {
        stopScanner(); // カメラを停止
    } else {
        startScanner(); // カメラを開始
    }
}

// Khởi động quét mã
function startScanner() {
    if (scannerRunning) return; // Nếu đã chạy thì không khởi động lại

    scannerRunning = true;
    const cameraDiv = document.getElementById('camera');
    cameraDiv.style.display = 'block'; // Hiện camera

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
// Khi phát hiện mã, phát sự kiện 'barcodeDetected' với mã vạch
let isProcessing = false; // Biến kiểm soát trạng thái quét mã

// Khi phát hiện mã, kiểm tra và xử lý
Quagga.onDetected((data) => {
    if (isProcessing) return; // Nếu đang xử lý, bỏ qua mã quét mới

    isProcessing = true; // Đặt trạng thái đang xử lý
    const code = data.codeResult.code;

    console.log(`Barcode detected: ${code}`); // Log mã để kiểm tra

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
            // Thêm sản phẩm vào giỏ hàng
            addToCart(product);
        } else {
            alert('Product not found!');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    })
    .finally(() => {
        // Sau 3 giây, cho phép nhận mã quét mới
        setTimeout(() => {
            isProcessing = false; // Reset trạng thái để nhận mã mới
        }, 1000);
    });
});



}

// Dừng quét mã và tắt camera
function stopScanner() {
    if (!scannerRunning) return; // Nếu camera chưa bật thì bỏ qua

    Quagga.stop();
    document.getElementById('camera').style.display = 'none'; // Ẩn camera
    scannerRunning = false;
}

// Gắn sự kiện cho nút bắt đầu quét
document.getElementById('start-scan').addEventListener('click', toggleScanner);










////////////////////////////////coppy///////////////////////////////////////////////////







// function addToCart(product) {
//     const tableBody = document.querySelector('#product-table tbody');
//     const existingRow = Array.from(tableBody.rows).find(row => {
//         const barcode = row.querySelector('input.product-quantity').dataset.barcode;
//         return barcode === product.barcode;
//     });

//     if (existingRow) {
//         const quantityInput = existingRow.querySelector('input.product-quantity');
//         quantityInput.value = parseInt(quantityInput.value) + 1;

//         const priceCell = existingRow.querySelector('.price');
//         const unitPrice = parseFloat(product.price);
//         priceCell.textContent = `${(unitPrice * parseInt(quantityInput.value)).toFixed(2)}¥`;

//         existingRow.classList.add('highlight');
//         setTimeout(() => {
//             existingRow.classList.remove('highlight');
//         }, 1500);
//     } else {
//         const row = document.createElement('tr');
//         row.innerHTML = `
//             <td>${product.pname}</td>
//             <td class="num">
//                 <input 
//                     type="number" 
//                     class="product-quantity" 
//                     value="1" 
//                     min="1" 
//                     data-barcode="${product.barcode}" 
//                     onchange="updateProductPrice(this, ${product.price})">
//             </td>
//             <td>${parseFloat(product.price).toFixed(2)}¥</td>
//             <td class="price">${parseFloat(product.price).toFixed(2)}¥</td>
//         `;
//         tableBody.appendChild(row);

//         row.classList.add('highlight');
//         setTimeout(() => {
//             row.classList.remove('highlight');
//         }, 1500);
//     }

//     // updateTotal();
// }

// function updateTotal() {
//     const tableRows = document.querySelectorAll('#product-table tbody tr');
//     let total = 0;

//     tableRows.forEach(row => {
//         const quantityInput = row.querySelector('.product-quantity');
//         const priceCell = row.querySelector('.price');
//         const quantity = parseInt(quantityInput.value);
//         const price = parseFloat(priceCell.textContent.replace('¥', ''));

//         total += quantity * price;
//     });

//     document.querySelector('#total-price').textContent = `${total.toFixed(2)}￥`;
// }