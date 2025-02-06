function addToCart(product) {
    const tableBody = document.querySelector('#product-table tbody');

    // Kiểm tra xem sản phẩm đã tồn tại trong bảng chưa (trừ dòng giảm giá)
    const existingRow = Array.from(tableBody.rows).find(row => {
        const barcode = row.querySelector('input.product-quantity')?.dataset.barcode;
        // Không dò tìm cho dòng giảm giá
        if (row.classList.contains('discount-row')) {
            return false;
        }
        return barcode === product.barcode;
    });

    if (existingRow) {
        // Nếu sản phẩm đã tồn tại, tăng số lượng
        const quantityInput = existingRow.querySelector('input.product-quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    
        const priceCell = existingRow.querySelector('.price');
        const unitPrice = parseFloat(product.price);
        priceCell.textContent = `${(unitPrice * parseInt(quantityInput.value)).toFixed(2)}¥`;
        updateProductPrice(quantityInput, unitPrice);
        existingRow.classList.add('highlight');
        setTimeout(() => {
            existingRow.classList.remove('highlight');
        }, 1500);
    } else {
        // Nếu sản phẩm chưa tồn tại, thêm hàng mới cho sản phẩm chính
        const row = document.createElement('tr');
        row.classList.add('product-row'); // Thêm class
        row.innerHTML = `
            <td class="stt"></td> <!-- Cột STT -->
            <td>${product.pname}</td>
            <td class="num">
                <input 
                    type="number" 
                    class="product-quantity" 
                    value="1" 
                    min="1" 
                    data-barcode="${product.barcode}" 
                    data-discounted-price="${product.discounted_price || product.price}" 
                    onchange="updateProductPrice(this, ${product.price})">
            </td>
            <td>${parseFloat(product.price).toFixed(2)}¥</td>
            <td class="price">${parseFloat(product.price).toFixed(2)}¥</td>
            <td>
                <button class="delete-btn" title="Xóa">X</button>
            </td>
        `;
    
        // Thêm sản phẩm vào cuối bảng
        tableBody.appendChild(row); 
    
        updateSerialNumbers(); // Cập nhật lại số thứ tự
    
        // Nếu có giảm giá, thêm dòng giảm giá ngay dưới sản phẩm chính
            if (product.discounted_price) {
                const discountRow = document.createElement('tr');
                discountRow.classList.add('discount-row'); // Thêm class
                discountRow.innerHTML = `
                    <td></td> <!-- Không hiển thị STT cho dòng giảm giá -->
                    <td colspan="2" style="color: red; text-align: center;">
                        割引: 
                    </td>
                    <td class="one-product-discounted">-${(product.price - product.discounted_price).toFixed(2)}¥</td>
                    <td class="price">-${(product.price - product.discounted_price).toFixed(2)}¥</td>
                    <td></td> <!-- Không hiển thị nút xóa cho dòng giảm giá -->
                `;
                // Thêm dòng giảm giá vào cuối bảng
                tableBody.appendChild(discountRow);  
                console.log("oke");
            }
    
        // Thêm sự kiện xóa hàng
        row.querySelector('.delete-btn').addEventListener('click', () => {
            row.classList.add('fade-out'); // Thêm lớp hiệu ứng làm mờ dần
        
            // Kiểm tra nếu dòng tiếp theo là dòng giảm giá
            const nextRow = row.nextElementSibling; // Lấy dòng sau dòng sản phẩm
            if (nextRow && nextRow.classList.contains('discount-row')) {
                nextRow.classList.add('fade-out'); // Thêm hiệu ứng cho dòng giảm giá
        
                // Chờ 300ms trước khi xóa dòng giảm giá
                setTimeout(() => {
                    nextRow.remove();
                }, 300);
            }
        
            // Chờ hiệu ứng hoàn tất (300ms) rồi xóa hàng sản phẩm
            setTimeout(() => {
                row.remove(); // Xóa dòng sản phẩm
                updateSerialNumbers(); // Cập nhật lại STT
                updateTotal(); // Cập nhật tổng tiền
            }, 300);
        });
    
        // Thêm hiệu ứng làm nổi bật dòng mới
        row.classList.add('highlight');
        setTimeout(() => {
            row.classList.remove('highlight');
        }, 1500);
    }
    
    // Cập nhật giá trị sản phẩm sau khi thay đổi số lượng
    // updateProductPrice(quantityInput, product.price);
    
    // Cập nhật tổng tiền mỗi lần thêm sản phẩm
    updateTotal();
    
}





function updateSerialNumbers() {
    const productRows = document.querySelectorAll('#product-table tbody tr.product-row');

    productRows.forEach((row, index) => {
        const sttCell = row.querySelector('.stt');
        sttCell.textContent = index + 1; // Gán STT bắt đầu từ 1
    });
}



//tổng tiền từng món hàng
function updateProductPrice(input, unitPrice) {
    const quantity = parseInt(input.value);

    // Kiểm tra nếu số lượng <= 0, tự động xóa hàng
    if (isNaN(quantity) || quantity <= 0) {
        const row = input.closest('tr'); // Lấy hàng chứa ô nhập liệu
        const discountRow = row.nextElementSibling; // Hàng giảm giá có thể là hàng kế tiếp

        if (discountRow && discountRow.classList.contains('discount-row')) {
            discountRow.remove(); // Xóa hàng giảm giá nếu có
        }

        row.remove(); // Xóa hàng sản phẩm
        updateSerialNumbers(); // Cập nhật lại số thứ tự (STT)
        updateTotal(); // Cập nhật lại tổng tiền
        return; // Kết thúc hàm để không tiếp tục tính toán
    }

    // Tính toán lại giá thành tiền khi số lượng hợp lệ
    const row = input.closest('tr'); // Lấy hàng chứa ô nhập liệu
    const priceCell = row.querySelector('.price'); // Tìm ô giá của hàng
    priceCell.textContent = `${(unitPrice * quantity).toFixed(2)}¥`; // Cập nhật giá tiền

    // Cập nhật giá trị của hàng giảm giá (nếu có)
    const discountRow = row.nextElementSibling; // Hàng giảm giá có thể là hàng kế tiếp
    if (discountRow && discountRow.classList.contains('discount-row')) {
        const discountedPriceCell = discountRow.querySelector('.one-product-discounted'); // Ô hiển thị giảm giá
        const discountPriceCell = discountRow.querySelector('.price'); // Ô hiển thị tổng giá giảm

        // Tính toán giá trị giảm giá dựa trên số lượng
        const discountPerProduct = parseFloat(unitPrice - input.dataset.discountedPrice); // Giá trị giảm trên 1 sản phẩm
        const totalDiscount = discountPerProduct * quantity;


        /////
        const expires = new Date();
        expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000)); // 1日後の有効期限
        
        document.cookie = `totalDiscount=${totalDiscount}; expires=${expires.toUTCString()}; path=/`;
        console.log(document.cookie);
//////
        // discountedPriceCell.textContent = `-${totalDiscount.toFixed(2)}¥`; // Cập nhật giá trị giảm giá
        discountPriceCell.textContent = `-${totalDiscount.toFixed(2)}¥`; // Cập nhật tổng giá giảm
    }

    // Cập nhật tổng tiền
    updateTotal();
}

//tổng tiền
function updateTotal() {
    const rows = document.querySelectorAll('#product-table tbody tr');
    let total = 0;

    rows.forEach(row => {
        const priceCell = row.querySelector('.price2') || row.querySelector('.price');
        if (priceCell) {
            const price = parseFloat(priceCell.textContent.replace('¥', '').replace('-', '')) || 0;

            // Trừ tiền nếu là dòng giảm giá
            if (row.classList.contains('discount-row')) {
                total -= price;
            } else {
                total += price;
            }
        }
    });

    document.getElementById('total-price').textContent = `¥${total.toFixed(2)}`;
}

function calculateChange() {
    const totalElement = document.getElementById('total-price');
    const total = parseFloat(totalElement.textContent.replace('¥', '')) || 0;

    const receivedAmountInput = document.getElementById('received-amount');
    const receivedAmount = parseFloat(receivedAmountInput.value) || 0;

    const change = receivedAmount - total;

    // Hiển thị tiền thừa là số nguyên
    document.getElementById('change-amount').textContent = `${Math.floor(change)}¥`;
}
