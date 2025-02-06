document.addEventListener('DOMContentLoaded', function () {
    const searchBox = document.getElementById('barcode-input'); // Ô nhập liệu
    const suggestionList = document.getElementById('barcode-suggestions'); // Danh sách gợi ý

    searchBox.addEventListener('input', function () {
        const keyword = searchBox.value.trim();
        if (keyword.length > 0) {
            fetch('./php/search_product.php?keyword=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    suggestionList.innerHTML = '';
                    suggestionList.style.display = 'block';

                    data.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';

                        const nameDiv = document.createElement('div');
                        nameDiv.textContent = `${product.pname}`;
                        div.appendChild(nameDiv);

                        const img = document.createElement('img');
                        img.src = product.productImage;
                        img.alt = product.pname;
                        img.style.width = '50px';
                        img.style.marginLeft = '10px';
                        div.appendChild(img);

                        div.dataset.id = product.productid;

                        div.addEventListener('click', () => {
                            searchBox.value = '';
                            suggestionList.innerHTML = '';
                            suggestionList.style.display = 'none';

                            addToCart(product);
                        });

                        suggestionList.appendChild(div);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });

    document.addEventListener('click', function (e) {
        if (!suggestionList.contains(e.target) && e.target !== searchBox) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });

    // function updateProductPrice(inputElement, unitPrice) {
    //     const quantity = parseInt(inputElement.value); // Lấy số lượng từ ô input
    //     const priceCell = inputElement.closest('tr').querySelector('.price'); // Lấy ô chứa giá tiền
    
    //     // Tính tổng tiền cho sản phẩm
    //     const totalPrice = (quantity * unitPrice).toFixed(2);
    
    //     // Cập nhật giá tiền vào ô
    //     priceCell.textContent = `${totalPrice}¥`;
    
    //     // Cập nhật tổng giỏ hàng nếu cần
    //     updateTotal();
    // }
    

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

    //     updateTotal();
    // }

    function updateTotal() {
        const tableRows = document.querySelectorAll('#product-table tbody tr');
        let total = 0;

        tableRows.forEach(row => {
            const quantityInput = row.querySelector('.product-quantity');
            const priceCell = row.querySelector('.price');
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(priceCell.textContent.replace('¥', ''));

            total += quantity * price;
        });

        document.querySelector('#total-price').textContent = `${total.toFixed(2)}￥`;
    }
    updateProductPrice();
});
