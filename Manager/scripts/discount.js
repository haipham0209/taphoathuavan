document.addEventListener('DOMContentLoaded', function () {
    // 商品カードをクリックしたときのイベントを設定
    const productCards = document.querySelectorAll('.product-card');
    const dialog = document.getElementById('product-dialog');
    const dialogContent = document.querySelector('.dialog-content');
    const productNameElem = document.getElementById('dialog-product-name');
    const productPriceElem = document.getElementById('dialog-product-price');
    const discountRateInput = document.getElementById('discount-rate');
    const discountedPriceElem = document.getElementById('discounted-price');
    const applyDiscountButton = document.getElementById('apply-discount');
    const cancelDiscountButton = document.getElementById('cancel-discount-btn')
    const cancelButton = document.getElementById('cancel-discount');

    let currentProduct = null; // 現在操作中の商品を保存

    productCards.forEach(card => {
        card.addEventListener('click', function () {
            // 商品名と値段を取得
            const productName = card.querySelector('.product-info p:nth-child(1)').textContent.replace('名前：', '').trim();
            const productPriceText = card.querySelector('.product-info p:nth-child(3)').textContent.replace('元値段：', '').trim();
            const productPrice = parseFloat(productPriceText);

            // 商品価格が有効かどうかチェック
            if (isNaN(productPrice)) {
                console.error('商品価格が無効です:', productPriceText);
                alert('商品の価格が正しく取得できませんでした');
                return;
            }

            // 現在の商品情報を保存
            currentProduct = {
                productId: card.getAttribute('data-product-id'), // 商品ID（data属性から取得）
                productName: productName,
                productPrice: productPrice
            };

            // ダイアログに情報を表示
            productNameElem.textContent = productName;
            productPriceElem.textContent = productPrice.toFixed(2);
            discountedPriceElem.textContent = ''; // 割引後の価格をリセット
            discountRateInput.value = ''; // 割引率の入力をリセット

            // ダイアログを表示
            dialog.style.display = 'flex';
        });
    });

    // 割引率が入力されたときに割引後の価格を計算
    discountRateInput.addEventListener('input', function () {
        const rate = parseFloat(this.value);
        if (!isNaN(rate) && rate >= 0 && rate <= 100) {
            const discountedPrice = currentProduct.productPrice * (1 - rate / 100);
            discountedPriceElem.textContent = discountedPrice.toFixed(2);
        } else {
            discountedPriceElem.textContent = '無効な割引率';
        }
    });

    // 割引を適用する処理
    applyDiscountButton.addEventListener('click', function () {
        const discountRate = parseFloat(discountRateInput.value);
        if (isNaN(discountRate) || discountRate < 0 || discountRate > 100) {
            alert('割引率を正しく入力してください');
            return;
        }

        const discountedPrice = currentProduct.productPrice * (1 - discountRate / 100);

        // サーバーにリクエストを送信
        fetch('./php/apply_discount.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                productId: currentProduct.productId,
                storeId: selectedStoreId,
                discountRate: discountRate,
                discountedPrice: discountedPrice.toFixed(2)
            })
        })
            .then(response => response.text())
            .then(data => {
                console.log('Raw Response:', data);
                try {
                    const jsonData = JSON.parse(data); // JSON にパース
                    if (jsonData.success) {
                        alert('割引が適用されました！');
                        closeDialog();
                        location.reload();
                    } else {
                        alert('割引の適用に失敗しました: ' + jsonData.message);
                    }
                } catch (error) {
                    console.error('JSON パースエラー:', error);
                    console.error('サーバーレスポンス:', data);
                    alert('サーバーレスポンスが無効です');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('割引の適用中にエラーが発生しました');
            });
    });

    let selectedProductId = null; // グローバル変数として初期化

    // 商品カードクリック時にダイアログを表示し、selectedProductIdを設定
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function () {
            selectedProductId = this.getAttribute('data-product-id'); // 商品IDを取得
            selectedStoreId = this.getAttribute('data-store-id'); // 店舗IDを取得
            const productName = this.querySelector('.product-info strong').textContent;
            const productPrice = this.querySelector('.product-info .product-price').textContent;

            // ダイアログ内に商品情報をセット
            document.getElementById('dialog-product-name').textContent = productName;
            document.getElementById('dialog-product-price').textContent = productPrice;

            // ダイアログを表示
            document.getElementById('product-dialog').style.display = 'block';
        });
    });

    function updateProductUI(productId) {
        // 該当する商品カードを検索
        const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
        if (!productCard) {
            console.warn(`商品ID ${productId} に対応するカードが見つかりません`);
            return;
        }

        // UIを更新（例: 割引情報をリセット）
        const productPriceElement = productCard.querySelector('.product-price');
        if (productPriceElement) {
            // 割引が適用されていない元の値段を表示（ここでは仮に"元値"とする）
            const originalPrice = productCard.getAttribute('data-original-price');
            if (originalPrice) {
                productPriceElement.textContent = `¥${originalPrice}`;
            } else {
                console.warn(`商品ID ${productId} の元値が設定されていません`);
            }
        }
    }


    document.getElementById('cancel-discount-btn').addEventListener('click', function () {
        if (!selectedProductId) {
            alert('商品が選択されていません');
            return;
        }

        if (!confirm('本当に割引をキャンセルしますか？')) {
            return;
        }

        fetch('./php/cancel_discount.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productId: selectedProductId,
                storeId: selectedStoreId,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('割引がキャンセルされました');
                    document.getElementById('discount-rate').value = '';
                    document.getElementById('discounted-price').textContent = '';
                    updateProductUI(selectedProductId);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('割引のキャンセル中にエラーが発生しました');
            });
    });



    // キャンセルボタンまたはダイアログの外側をクリックしたときにダイアログを閉じる処理
    window.closeDialog = function () {
        dialog.style.display = 'none';
        currentProduct = null; // 現在の商品情報をクリア
    };

    // キャンセルボタン
    cancelButton.addEventListener('click', closeDialog);

    // ダイアログの外側をクリックして閉じる
    dialog.addEventListener('click', function (event) {
        if (!dialogContent.contains(event.target)) {
            closeDialog();
        }
    });
});
