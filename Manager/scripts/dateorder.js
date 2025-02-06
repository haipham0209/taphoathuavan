const prevDateButton = document.getElementById('prev-date');
const nextDateButton = document.getElementById('next-date');
const datePicker = document.getElementById('date-picker');

const orderListBody = document.getElementById('order-list-body');

let currentDate = new Date();
updateDateDisplay(currentDate);

function updateDateDisplay(date) {
    const formattedDate = date.toISOString().split('T')[0];
    datePicker.value = formattedDate;
    loadOrderDetails(formattedDate);
}

nextDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDateDisplay(currentDate);
});

prevDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDateDisplay(currentDate);
});

datePicker.addEventListener('change', (event) => {
    const selectedDate = new Date(event.target.value);
    if (!isNaN(selectedDate)) {
        currentDate = selectedDate;
        updateDateDisplay(currentDate);
    }
});

// 注文データを表示
function loadOrderDetails(date) {
    fetch(`./php/fetch_orders.php?date=${date}`)
        .then(response => response.json())
        .then(orders => {
            orderListBody.innerHTML = '';
            const noDataMessage = document.getElementById('no-data-message');
            const orderListTable = document.querySelector('.order-list');

            if (orders.length === 0) {
                noDataMessage.style.display = 'block';
                orderListTable.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                orderListTable.style.display = 'table';
                let index = 1;
                orders.forEach(order => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index++}</td>
                        <td>${order.order_number}</td>
                        <td>${order.total_quantity}</td>
                        <td>${order.total_price}</td>
                        <td class="toggle-icon">▼</td>
                    `;
                    tr.querySelector('.toggle-icon').addEventListener('click', () => {
                        toggleOrderDetail(order.order_number, tr);
                    });
                    orderListBody.appendChild(tr);
                });
            }
        })
        .catch(error => console.error('注文データの取得に失敗しました:', error));
}


// 注文詳細情報を取得
function fetchOrderDetail(orderNumber, orderRow) {
    return fetch(`./php/fetch_order_details.php?order_number=${orderNumber}`)
        .then(response => response.json())
        .then(orderDetails => {
            const detailsRow = document.createElement('tr');
            detailsRow.classList.add('order-details-row');

            // 親の行の幅を取得
            const parentWidth = orderRow.offsetWidth;

            let detailsHTML = `<td colspan="5"><div class="order-details-container" style="width:${parentWidth}px;"><table><thead><tr><th style="text-align: center;">商品名</th><th style="text-align: center;">数量</th><th style="text-align: center;">単価</th><th style="text-align: center;">小計</th></tr></thead><tbody>`;

            orderDetails.forEach(detail => {
                detailsHTML += `
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">${detail.pname}</td>
                        <td style="text-align: center; vertical-align: middle;">${detail.quantity}</td>
                        <td style="text-align: center; vertical-align: middle;">${detail.item_price}</td>
                        <td style="text-align: center; vertical-align: middle;">${detail.order_price}</td>
                    </tr>
                `;
            });

            // 割引行を追加
            if (orderDetails[0].discount > 0) {
                detailsHTML += `
                    <tr>
                        <td colspan="2" style="text-align: center; font-weight: bold; color: red;">割引合計</td>
                        <td colspan="2" style="text-align: center; color: red;">-${orderDetails[0].discount}</td>
                    </tr>
                `;
            }

            // 合計と預かりを二行目に追加
            detailsHTML += `
                <tr>
                    <td colspan="2" style="text-align: center; font-weight: bold;">合計</td>
                    <td colspan="2" style="text-align: center;">${orderDetails[0].total_price}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; font-weight: bold;">お預かり</td>
                    <td colspan="2" style="text-align: center;">${orderDetails[0].received_amount}</td>
                </tr>
            `;

            detailsHTML += `</tbody></table></div></td>`;
            detailsRow.innerHTML = detailsHTML;

            return detailsRow;
        })
        .catch(error => console.error('注文詳細の取得に失敗しました:', error));
}

// 詳細情報のトグル表示
function toggleOrderDetail(orderNumber, orderRow) {
    const toggleIcon = orderRow.querySelector('.toggle-icon');
    const existingDetailRow = orderRow.nextElementSibling;

    if (existingDetailRow && existingDetailRow.classList.contains('order-details-row')) {
        if (existingDetailRow.style.display === 'none') {
            existingDetailRow.style.display = '';
            toggleIcon.textContent = '▲';
        } else {
            existingDetailRow.style.display = 'none';
            toggleIcon.textContent = '▼';
        }
    } else {
        fetchOrderDetail(orderNumber, orderRow).then(detailsRow => {
            orderListBody.insertBefore(detailsRow, orderRow.nextSibling);
            toggleIcon.textContent = '▲';
        });
    }
}
