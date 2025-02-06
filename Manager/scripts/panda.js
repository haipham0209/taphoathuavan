// Lấy phần tử mắt
const leftEye = document.getElementById("leftEye");
const rightEye = document.getElementById("rightEye");

// Giới hạn chuyển động của mắt
const maxMove = 10; // Khoảng cách tối đa mắt có thể di chuyển
const eyeRadius = 3; // Bán kính khu vực hốc mắt (giới hạn di chuyển mắt)
const moveUp = 4.5; // Khoảng cách mắt di chuyển lên khi nhập mật khẩu

// Biến trạng thái để biết mắt có đang nhìn lên trên hay không
let isPasswordFocused = false;

// Hàm tính toán vị trí mắt dựa vào vị trí chuột
function moveEyes(event) {
    if (isPasswordFocused) return; // Nếu đang nhập mật khẩu thì không di chuyển mắt theo chuột

    const character = document.querySelector(".character");
    const rect = character.getBoundingClientRect();

    // Tính vị trí trung tâm của nhân vật (hốc mắt)
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;
    const mouseX = event.clientX;
    const mouseY = event.clientY;

    // Tính khoảng cách dịch chuyển mắt, giới hạn trong maxMove và trong phạm vi hốc mắt
    let deltaX = (mouseX - centerX) / (rect.width / 2) * maxMove;
    let deltaY = (mouseY - centerY) / (rect.height / 2) * maxMove;

    // Giới hạn chuyển động mắt trong phạm vi hốc mắt
    deltaX = Math.max(-eyeRadius, Math.min(deltaX, eyeRadius));
    deltaY = Math.max(-eyeRadius, Math.min(deltaY, eyeRadius));

    // Di chuyển mắt trái và phải
    leftEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    rightEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
}

// Lắng nghe sự kiện di chuyển chuột
document.addEventListener("mousemove", moveEyes);

// Lắng nghe sự kiện khi chuột ra khỏi trang
document.addEventListener("mouseleave", () => {
    // Đặt lại vị trí mắt về trung tâm
    leftEye.style.transform = "translate(0px, 0px)";
    rightEye.style.transform = "translate(0px, 0px)";
});

// Khi nhập liệu vào ô thì mắt sẽ nhìn theo con trỏ, trừ khi đang ở ô mật khẩu
const inputs = document.querySelectorAll(".login-input");

inputs.forEach(input => {
    input.addEventListener("input", () => {
        if (isPasswordFocused) return; // Nếu đang nhập mật khẩu thì không di chuyển mắt theo con trỏ

        const character = document.querySelector(".character");
        const rect = character.getBoundingClientRect();

        // Tính vị trí con trỏ trong ô nhập liệu
        const inputRect = input.getBoundingClientRect();
        const cursorX = inputRect.left + input.selectionStart * 10; // Mỗi ký tự có chiều rộng 10px
        const cursorY = inputRect.top + inputRect.height / 2;

        // Tính vị trí mới của mắt dựa trên vị trí con trỏ
        let deltaX = (cursorX - rect.left - rect.width / 2) / (rect.width / 2) * maxMove;
        let deltaY = (cursorY - rect.top - rect.height / 2) / (rect.height / 2) * maxMove;

        // Giới hạn chuyển động mắt trong hốc mắt
        deltaX = Math.max(-eyeRadius, Math.min(deltaX, eyeRadius));
        deltaY = Math.max(-eyeRadius, Math.min(deltaY, eyeRadius));

        leftEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
        rightEye.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    });
});

// Sự kiện khi nhập mật khẩu - mắt sẽ "né" lên trên
const passwordInput = document.getElementById("password");
passwordInput.addEventListener("focus", () => {
    isPasswordFocused = true;
    leftEye.style.transform = `translate(0px, -${moveUp}px)`;
    rightEye.style.transform = `translate(0px, -${moveUp}px)`;
});
passwordInput.addEventListener("blur", () => {
    isPasswordFocused = false;
    leftEye.style.transform = "translate(0px, 0px)";
    rightEye.style.transform = "translate(0px, 0px)";
});
