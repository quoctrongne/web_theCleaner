document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    const addressInput = document.getElementById('bookAddress');

    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const name = document.getElementById('bookName').value.trim();
            const email = document.getElementById('bookEmail').value.trim();
            const phone = document.getElementById('bookPhone').value.trim();
            const address = addressInput.value.trim();
            const service = document.getElementById('bookService').value;
            const date = document.getElementById('bookDate').value;
            const time = document.getElementById('bookTime').value;
            const area = document.getElementById('bookArea').value;

            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(el => el.remove());

            let hasError = false;

            if (name === '') {
                displayError(document.getElementById('bookName'), 'Vui lòng nhập họ và tên');
                hasError = true;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '' || !emailRegex.test(email)) {
                displayError(document.getElementById('bookEmail'), 'Email không hợp lệ');
                hasError = true;
            }

            const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
            if (phone === '' || !phoneRegex.test(phone)) {
                displayError(document.getElementById('bookPhone'), 'Số điện thoại không hợp lệ');
                hasError = true;
            }

            if (address === '') {
                displayError(addressInput, 'Vui lòng nhập địa chỉ');
                hasError = true;
            }

            if (service === '') {
                displayError(document.getElementById('bookService'), 'Vui lòng chọn dịch vụ');
                hasError = true;
            }

            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (date === '' || selectedDate < today) {
                displayError(document.getElementById('bookDate'), 'Ngày không hợp lệ');
                hasError = true;
            }

            if (time === '') {
                displayError(document.getElementById('bookTime'), 'Vui lòng chọn thời gian');
                hasError = true;
            }

            const areaValue = parseInt(area);
            if (isNaN(areaValue) || areaValue <= 0) {
                displayError(document.getElementById('bookArea'), 'Diện tích không hợp lệ');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
            } else {
                // Điều hướng sang trang thanh toán nếu không có lỗi
                window.location.href = 'payment.php'; // Điều hướng sang trang thanh toán
            }
        });
    }

    function displayError(inputElement, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-danger';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.8em';
        errorDiv.innerText = message;

        inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
    }

    const dateInput = document.getElementById('bookDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }

    const serviceSelect = document.getElementById('bookService');
    const areaInput = document.getElementById('bookArea');

    if (serviceSelect && areaInput) {
        const updatePriceEstimate = function() {
            const service = serviceSelect.value;
            const area = parseInt(areaInput.value) || 0;

            if (service && area > 0) {
                console.log(`Đang tính giá cho dịch vụ ${service} với diện tích ${area}m²`);
            }
        };

        serviceSelect.addEventListener('change', updatePriceEstimate);
        areaInput.addEventListener('input', updatePriceEstimate);
    }

    const resetButton = document.querySelector('.booking-form button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            console.log("Form đã được reset");
        });
    }
});

function calculatePrice(service, area) {
    let basePrice = 0;

    if (service === 'home') {
        if (area < 50) {
            basePrice = 500000;
        } else if (area < 100) {
            basePrice = 800000;
        } else {
            basePrice = 1000000 + (area - 100) * 8000;
        }
    } else if (service === 'office') {
        if (area < 100) {
            basePrice = area * 15000;
        } else if (area < 300) {
            basePrice = area * 13000;
        } else {
            basePrice = area * 11000;
        }
    }

    return basePrice.toLocaleString('vi-VN');
}