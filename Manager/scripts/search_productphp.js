document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('barcode-input');
    const suggestionList = document.getElementById('barcode-suggestions');

    searchBox.addEventListener('input', function() {
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

                            // Tìm phần tử sản phẩm dựa trên productid
                            const productElement = document.querySelector(`[data-product-id="${product.productid}"]`);
                            if (productElement) {
                                // Cuộn đến phần tử sản phẩm và đảm bảo nó nằm ở giữa màn hình
                                productElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
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

    document.addEventListener('click', function(e) {
        if (!suggestionList.contains(e.target) && e.target !== searchBox) {
            suggestionList.innerHTML = '';
            suggestionList.style.display = 'none';
        }
    });
});
