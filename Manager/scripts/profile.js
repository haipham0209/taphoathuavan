const avatarInput = document.getElementById('avatar-input');
const avatarPreview = document.getElementById('avatar-preview');

// 當使用者選擇檔案時，預覽新頭像
avatarInput.addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            avatarPreview.src = e.target.result; // 更新頭像為上傳的圖片
        };
        reader.readAsDataURL(file);
    }
});
function toggleEdit(fieldId) {
    const field = document.getElementById(fieldId);

    // Toggle the readonly attribute
    if (field.hasAttribute('readonly')) {
        field.removeAttribute('readonly'); // Make the field editable
        field.focus(); // Focus on the field to show the cursor
    } else {
        field.setAttribute('readonly', true); // Make the field readonly again
        field.blur(); // Remove focus to hide the cursor
    }
}

function copyLink() {
    // Get the link element
    const linkElement = document.getElementById("storeLink");
    
    // Get the href attribute of the link
    const linkURL = linkElement.href;
    
    // Use navigator.clipboard to copy the URL
    navigator.clipboard.writeText(linkURL).then(() => {
        alert("Link copied to clipboard!");
    }).catch((error) => {
        console.error("Failed to copy text: ", error);
    });
}

function openDialog() {
    document.getElementById("passwordDialog").style.display = "flex"; // Show the modal
    // Thêm required vào các trường trong dialog khi mở
    document.getElementById("old-password").setAttribute("required", true);
    document.getElementById("new-password").setAttribute("required", true);
    document.getElementById("confirm-password").setAttribute("required", true);
}

function closeDialog() {
    document.getElementById("passwordDialog").style.display = "none"; // Hide the modal
    // Bỏ required khi đóng dialog
    document.getElementById("old-password").removeAttribute("required");
    document.getElementById("new-password").removeAttribute("required");
    document.getElementById("confirm-password").removeAttribute("required");
}

document.querySelector('.edit-form').addEventListener('submit', function(event) {
    event.preventDefault();
    console.log('Form đang submit');
    document.getElementById('loading').style.display = 'flex';

    setTimeout(() => {
        this.submit();
    }, 1500);
});




