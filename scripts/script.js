document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menuBtn');
    const navMenu = document.getElementById('navMenu');
    
    // Mobile Menu Toggle
    if (menuBtn && navMenu) {
        menuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Xử lý form đặt lịch
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

            // Xóa thông báo lỗi cũ
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
            }
            // Khi không có lỗi, form sẽ submit đến process_booking.php
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

    // Thiết lập ngày tối thiểu cho đặt lịch
    const dateInput = document.getElementById('bookDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }

    // Xử lý dynamically tính giá
    const serviceSelect = document.getElementById('bookService');
    const areaInput = document.getElementById('bookArea');

    if (serviceSelect && areaInput) {
        const updatePriceEstimate = function() {
            const service = serviceSelect.value;
            const area = parseInt(areaInput.value) || 0;

            if (service && area > 0) {
                const priceEstimate = calculatePrice(service, area);
                const priceNote = document.querySelector('.pricing-note p');
                if (priceNote) {
                    priceNote.innerHTML = `<i class="fas fa-info-circle"></i> Ước tính giá dịch vụ: <strong>${priceEstimate} đ</strong>. Chi tiết giá sẽ được hiển thị ở trang thanh toán.`;
                }
            }
        };

        serviceSelect.addEventListener('change', updatePriceEstimate);
        areaInput.addEventListener('input', updatePriceEstimate);
    }
    
    // Xử lý FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            if (question) {
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    // Đóng tất cả các item khác
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });
                    // Toggle active class cho item hiện tại
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            }
        });
    }
});

// Hàm tính giá dựa trên dịch vụ và diện tích
function calculatePrice(service, area) {
    let basePrice = 0;

    // Sử dụng dữ liệu từ biến toàn cục servicePricing nếu có
    if (typeof window.servicePricing !== 'undefined') {
        // Lấy mức giá từ dữ liệu động
        if (service === 'home' && window.servicePricing.home) {
            for (const tier of window.servicePricing.home) {
                if ((area >= tier.min_area) && (tier.max_area === null || area < tier.max_area)) {
                    basePrice = area * tier.price;
                    break;
                }
            }
        } else if (service === 'office' && window.servicePricing.office) {
            for (const tier of window.servicePricing.office) {
                if ((area >= tier.min_area) && (tier.max_area === null || area < tier.max_area)) {
                    basePrice = area * tier.price;
                    break;
                }
            }
        }
    }
    
    // Fallback nếu không tìm thấy dữ liệu hoặc chưa được khởi tạo
    if (basePrice === 0) {
        if (service === 'home') { // Vệ sinh nhà ở
            if (area < 50) {
                basePrice = area * 20000;  // 20.000đ/m²
            } else if (area <= 100) {
                basePrice = area * 16000;  // 16.000đ/m²
            } else {
                basePrice = area * 14000;  // 14.000đ/m²
            }
        } else if (service === 'office') { // Vệ sinh văn phòng
            if (area < 100) {
                basePrice = area * 25000;  // 25.000đ/m²
            } else if (area <= 300) {
                basePrice = area * 22000;  // 22.000đ/m²
            } else {
                basePrice = area * 20000;  // 20.000đ/m²
            }
        }
    }
    
    return basePrice.toLocaleString('vi-VN');
}