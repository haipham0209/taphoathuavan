// document.addEventListener('DOMContentLoaded', function() {
//     // Lấy các phần tử mắt và ô nhập liệu mật khẩu
//     const leftEye = document.getElementById("leftEye");
//     const rightEye = document.getElementById("rightEye");
//     const passwordInput = document.getElementById("password");
//     const character = document.querySelector(".character");

//     // Kiểm tra xem các phần tử có tồn tại trước khi tiếp tục
//     if (!leftEye || !rightEye || !passwordInput || !character) {
//         console.error("Không tìm thấy một hoặc nhiều phần tử cần thiết trong DOM.");
//         return;
//     }

//     // Các giới hạn chuyển động của mắt
//     const maxMove = 19; // Khoảng cách tối đa mắt có thể di chuyển
//     const eyeRadius = 5.5; // Bán kính khu vực hốc mắt
//     const moveUp = 4.5; // Khoảng cách mắt di chuyển lên khi nhập mật khẩu
//     let isPasswordFocused = false; // Trạng thái để biết mắt có đang nhìn lên khi nhập mật khẩu

//     // Hàm tính toán vị trí mắt dựa trên vị trí chuột
//     function moveEyes(event) {
//         if (isPasswordFocused) return; // Nếu đang nhập mật khẩu, không di chuyển mắt theo chuột

//         const rect = character.getBoundingClientRect();
//         const centerX = rect.left + rect.width / 2;
//         const centerY = rect.top + rect.height / 2;
//         const mouseX = event.clientX;
//         const mouseY = event.clientY;

//         // Tính toán khoảng cách di chuyển của mắt
//         let deltaX = (mouseX - centerX) / (rect.width / 2) * maxMove;
//         let deltaY = (mouseY - centerY) / (rect.height / 2) * maxMove;

//         // Giới hạn di chuyển mắt trong hốc mắt
//         const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
//         if (distance > eyeRadius) {
//             const ratio = eyeRadius / distance;
//             deltaX *= ratio;
//             deltaY *= ratio;
//         }

//         leftEye.style.transform = `translate(${deltaX}px, ${deltaY + 1}px)`;
//         rightEye.style.transform = `translate(${deltaX}px, ${deltaY + 1}px)`;
//     }

//     // Sự kiện di chuyển chuột
//     document.addEventListener("mousemove", moveEyes);

//     // Sự kiện khi chuột rời khỏi trang
//     document.addEventListener("mouseleave", () => {
//         leftEye.style.transform = "translate(0px, 0px)";
//         rightEye.style.transform = "translate(0px, 0px)";
//     });

//     // Sự kiện khi tập trung vào ô mật khẩu
//     passwordInput.addEventListener("focus", () => {
//         isPasswordFocused = true;
//         leftEye.style.transform = `translate(0px, -${moveUp}px)`;
//         rightEye.style.transform = `translate(0px, -${moveUp}px)`;
//     });

//     // Sự kiện khi rời khỏi ô mật khẩu
//     passwordInput.addEventListener("blur", () => {
//         isPasswordFocused = false;
//         leftEye.style.transform = "translate(0px, 0px)";
//         rightEye.style.transform = "translate(0px, 0px)";
//     });
// });
