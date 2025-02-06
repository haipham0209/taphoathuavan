// document.addEventListener("DOMContentLoaded", () => {
//     const datePicker = document.getElementById("date-picker");
//     const orderListBody = document.getElementById("order-list-body");
//     const orderDetails = document.getElementById("order-details");
//     const orderDetailsBody = document.getElementById("order-details-body");
//     const orderSummary = document.getElementById("order-summary");

//     // Sự kiện thay đổi ngày
//     datePicker.addEventListener("change", () => {
//         const selectedDate = datePicker.value;
//         console.log(selectedDate);
//         fetch("./php/order_process.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/x-www-form-urlencoded" },
//             body: `selected_date=${selectedDate}`
//         })
//             .then(response => response.json())
//             .then(data => {
//                 orderListBody.innerHTML = "";
//                 data.forEach((order, index) => {
//                     const row = document.createElement("tr");
//                     row.innerHTML = `
//                         <td>${index + 1}</td>
//                         <td>${order.order_number}</td>
//                         <td>${order.total_quantity}</td>
//                         <td>${order.total_price}</td>
//                     `;
//                     row.addEventListener("click", () => loadOrderDetails(order.order_number));
//                     orderListBody.appendChild(row);
//                 });
//             });
//     });

//     // Load chi tiết đơn hàng
//     function loadOrderDetails(orderNumber) {
//         fetch("./php/order_process.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/x-www-form-urlencoded" },
//             body: `order_number=${orderNumber}`
//         })
//             .then(response => response.json())
//             .then(data => {
//                 orderDetails.style.display = "block";
//                 orderDetailsBody.innerHTML = "";
//                 let totalPrice = 0;

//                 data.forEach(detail => {
//                     const row = document.createElement("tr");
//                     row.innerHTML = `
//                         <td>${detail.pname}</td>
//                         <td>${detail.quantity}</td>
//                         <td>${detail.price}</td>
//                         <td>${detail.quantity * detail.price}</td>
//                     `;
//                     totalPrice += detail.quantity * detail.price;
//                     orderDetailsBody.appendChild(row);
//                 });

//                 orderSummary.innerHTML = `
//                     合計金額: ${totalPrice}<br>
//                     お預かり: ${data[0].received_amount}<br>
//                     お釣り: ${data[0].change_amount}<br>
//                     割引: ${data[0].discount}
//                 `;
//             });
//     }
// });
