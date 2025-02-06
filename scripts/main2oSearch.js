


console.log("main2oSearch");
function performSearchFromInput() {
    const searchInputs = document.querySelectorAll("#searchInput");

    const input = document.getElementById('searchInput');
    const query = input.value.trim(); // Lấy giá trị của input và loại bỏ khoảng trắng
    console.log(query);
    console.log(input);
    console.log("end");

    document.getElementById('search1').addEventListener('click', function() {
        console.log("here");
    
        // Lấy phần tử #searchInput bên trong #search1
        const search1 = document.getElementById('search1');  // Lấy phần tử có id 'search1'
        
        // Dùng querySelector để tìm #searchInput bên trong phần tử search1
        const input = search1.closest('.search-container').querySelector('#searchInput');
        
        // Lấy giá trị của input
        const inputValue = input.value;
        console.log("Giá trị ô input là:", inputValue);  // In giá trị ra console
        // window.location.href = `search.php?sname=${sname}&query=${encodeURIComponent(query)}`;
        const sname = getCookie("storename");
        window.location.href = `search.php?sname=${sname}&query=${encodeURIComponent(inputValue)}`;
    });

    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) {
            return parts.pop().split(";").shift();
        }
        return null;
    }
    

    // Lấy phần tử với id 'search1' và gán sự kiện click

    // performSearch(query); // Gọi hàm với giá trị lấy từ input

}

function performSearch(query) {
    if (query.length > 0) {
        // Chuyển hướng đến trang tìm kiếm với từ khóa trong URL
        window.location.href = `search.php?sname=<?= urlencode($sname) ?>&query=${encodeURIComponent(query)}`;
    } else {
        alert('検索キーワードを入力してください。2');
    }
}




//////main coppy past
