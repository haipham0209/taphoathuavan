//nav===========================================================================

document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const overlay = document.querySelector('.overlay');
    const menuIcon = document.querySelector('.menu-icon');

    // Mở/đóng menu khi nhấp vào nút
    menuToggle.addEventListener('click', function () {
        navMenu.classList.toggle('open');
        menuIcon.classList.toggle('open'); // Thay đổi biểu tượng
    });

    // Đóng menu khi nhấp vào overlay
    overlay.addEventListener('click', function () {
        navMenu.classList.remove('open');
        menuIcon.classList.remove('open'); // Đặt lại biểu tượng về ban đầu
    });
});
//nav-avatar
document.addEventListener('DOMContentLoaded', function () {
    const accToggle = document.querySelector('.account-toggle');
    const navMypage = document.querySelector('.nav-myPage');
    const overlayAva = document.querySelector('.overlay-avatar');
    const avatar = document.querySelector('.avatar');

   
    accToggle.addEventListener('click', function () {
        navMypage.classList.toggle('open');
        avatar.classList.toggle('open'); 
    });


    overlayAva.addEventListener('click', function () {
        navMypage.classList.remove('open');
        avatar.classList.remove('open'); 
    });
});

//showmore btn=======================================================================
// Lấy tất cả các nút "Show More"
const showMoreBtns = document.querySelectorAll('.show-more-btn');


showMoreBtns.forEach(button => {
    button.addEventListener('click', () => {
        // Lấy nhóm sản phẩm mà nút này thuộc về
        const group = button.getAttribute('data-group');
        const productShowcase = document.querySelector(`#${group} .product-showcase`);

        
        productShowcase.classList.toggle('open');

        
        if (productShowcase.classList.contains('open')) {
            button.textContent = 'Show Less';
        } else {
            button.textContent = 'Show More';
        }
    });
});

//filter button===========================================================================
    
const filterButtons = document.querySelectorAll('.filter-button');
const groups = document.querySelectorAll('.group');

// Hàm kiểm tra và tự động kích hoạt 'All' nếu không có nút nào active
function checkAndActivateAll() {
    const hasActive = Array.from(filterButtons).some(
        btn => btn.classList.contains('active') && btn.getAttribute('data-target') !== 'all'
    );

    if (!hasActive) {
        const allButton = document.querySelector('[data-target="all"]');
        allButton.classList.add('active');
        groups.forEach(group => group.style.display = 'block');
    }
}

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        const targetGroup = button.getAttribute('data-target');

        if (targetGroup === 'all') {
            // Bỏ 'active' khỏi tất cả và kích hoạt lại 'All'
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            groups.forEach(group => group.style.display = 'block');
        } else {
            // Bỏ 'active' khỏi 'All' và toggle cho nút bấm hiện tại
            document.querySelector('[data-target="all"]').classList.remove('active');
            button.classList.toggle('active');

            // Ẩn tất cả nhóm trước khi hiển thị những nhóm active
            groups.forEach(group => group.style.display = 'none');

            const activeButtons = Array.from(filterButtons).filter(btn =>
                btn.classList.contains('active')
            );

            if (activeButtons.length > 0) {
                activeButtons.forEach(btn => {
                    const targetId = btn.getAttribute('data-target');
                    document.getElementById(targetId).style.display = 'block';
                });
            }

            // Kiểm tra nếu không còn nút nào active, kích hoạt 'All'
            checkAndActivateAll();
        }
    });
});


//////////////////////////////////////////////////////////show more button/////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
    const groups = document.querySelectorAll(".group");

    groups.forEach((group) => {
        const productShowcase = group.querySelector(".product-showcase");
        const productContents = group.querySelectorAll(".product-content");
        const showMoreButton = group.querySelector(".show-more-btn");

        // Ẩn nút "Show More" nếu số sản phẩm <= 3
        if (productContents.length <= 3) {
            showMoreButton.style.display = "none";
        } else {
            // Chỉ hiển thị 3 sản phẩm ban đầu
            productContents.forEach((product, index) => {
                if (index >= 4) product.style.display = "none";
            });

            let isExpanded = false; // Trạng thái mở rộng

            showMoreButton.addEventListener("click", () => {
                if (isExpanded) {
                    // Thu gọn: Loại bỏ class 'open'
                    productShowcase.classList.remove("open");
                    setTimeout(() => {
                        // Ẩn các sản phẩm sau 3 sản phẩm sau khi hiệu ứng chạy xong
                        productContents.forEach((product, index) => {
                            if (index >= 4) product.style.display = "none";
                        });
                    }, 600); // Đợi 0.6s cho hiệu ứng chạy xong
                    showMoreButton.textContent = "Show More";
                } else {
                    // Mở rộng: Hiển thị tất cả sản phẩm và thêm class 'open'
                    productContents.forEach((product) => (product.style.display = "block"));
                    productShowcase.classList.add("open");
                    showMoreButton.textContent = "Show Less";
                }
                isExpanded = !isExpanded;
            });
        }
    });
});




