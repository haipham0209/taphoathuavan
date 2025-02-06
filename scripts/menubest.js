const productGrid = document.querySelector('.product-grid');
const products = document.querySelectorAll('.product');
const leftArrow = document.querySelector('.arrow.left');
const rightArrow = document.querySelector('.arrow.right');
let currentIndex = 0;

function showProduct(index) {
    productGrid.style.transform = `translateX(-${index * 100}%)`;
}

function nextProduct() {
    currentIndex = (currentIndex + 1) % products.length;
    showProduct(currentIndex);
}

function previousProduct() {
    currentIndex = (currentIndex - 1 + products.length) % products.length;
    showProduct(currentIndex);
}

rightArrow.addEventListener('click', nextProduct);
leftArrow.addEventListener('click', previousProduct);

// Tự động chuyển đổi sản phẩm sau mỗi 2 giây
let autoSlide = setInterval(nextProduct, 3000);

// Dừng tự động chuyển đổi khi người dùng nhấn vào các nút
leftArrow.addEventListener('click', () => {
    clearInterval(autoSlide);
    autoSlide = setInterval(nextProduct, 2000);
});
rightArrow.addEventListener('click', () => {
    clearInterval(autoSlide);
    autoSlide = setInterval(nextProduct, 2000);
});
