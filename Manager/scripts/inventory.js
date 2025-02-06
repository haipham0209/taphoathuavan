
function searchProducts() {
    // Lấy giá trị từ ô tìm kiếm và chuyển thành chữ thường để so sánh không phân biệt chữ hoa/thường
    const searchValue = document.getElementById("search").value.toLowerCase();

    // Lấy tất cả các hàng trong bảng (ngoại trừ hàng tiêu đề)
    const tableRows = document.querySelectorAll("#productTable tbody tr");

    // Lặp qua từng hàng trong bảng
    tableRows.forEach(row => {
        // Lấy nội dung của các cột trong mỗi hàng
        const cells = row.getElementsByTagName("td");
        let matchFound = false;

        // Kiểm tra từng cột xem có khớp với giá trị tìm kiếm không
        for (let cell of cells) {
            if (cell.textContent.toLowerCase().includes(searchValue)) {
                matchFound = true;
                break;
            }
        }

        // Ẩn hoặc hiển thị hàng dựa trên kết quả tìm kiếm
        if (matchFound) {
            row.style.display = ""; // Hiển thị hàng nếu có sự trùng khớp
        } else {
            row.style.display = "none"; // Ẩn hàng nếu không có sự trùng khớp
        }
    });
}

