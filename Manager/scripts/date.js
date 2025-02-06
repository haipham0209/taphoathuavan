// 取得相關 DOM 元素
const prevDateButton = document.getElementById('prev-date');
const nextDateButton = document.getElementById('next-date');
const datePicker = document.getElementById('date-picker');
const salesElement = document.getElementById('sales');
const profitElement = document.getElementById('profit');

// 初始化日期為今天
let currentDate = new Date();
updateDateDisplay(currentDate);

// 更新日期選擇器的值
function updateDateDisplay(date) {
    const formattedDate = date.toISOString().split('T')[0]; // yyyy-mm-dd 格式
    datePicker.value = formattedDate;
    // Gửi yêu cầu AJAX để lấy dữ liệu doanh thu và lợi nhuận
    fetchRevenueData(formattedDate);
}

// 日期加一天
nextDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() + 1);
    updateDateDisplay(currentDate);
});

// 日期減一天
prevDateButton.addEventListener('click', () => {
    currentDate.setDate(currentDate.getDate() - 1);
    updateDateDisplay(currentDate);
});

// 當使用者在日期選擇器中選擇日期時更新顯示
datePicker.addEventListener('change', (event) => {
    const selectedDate = new Date(event.target.value);
    if (!isNaN(selectedDate)) {
        currentDate = selectedDate;
        updateDateDisplay(currentDate);
    }
});

// Hàm gửi yêu cầu AJAX để lấy dữ liệu doanh thu và lợi nhuận
function fetchRevenueData(date) {
    fetch(`./php/get_revenue.php?date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                salesElement.textContent = data.total_revenue ;
                profitElement.textContent = data.total_profit ;
            } else {
                salesElement.textContent = '_';
                profitElement.textContent = '_';
            }
        })
        .catch(error => {
            console.error('Error fetching revenue data:', error);
            salesElement.textContent = '';
            profitElement.textContent = '';
        });
}
