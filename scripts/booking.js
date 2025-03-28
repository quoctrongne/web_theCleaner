// JavaScript cho trang đặt lịch
// Phiên bản không sử dụng Google Maps API

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM đã tải xong cho trang đặt lịch");
    
    // Thiết lập ngày tối thiểu cho đặt lịch (ngày mai)
    const dateInput = document.getElementById('bookDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }
    
    // Xác thực và xử lý submit form đặt lịch
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            // Xác thực cơ bản
            const name = document.getElementById('bookName').value;
            const email = document.getElementById('bookEmail').value;
            const phone = document.getElementById('bookPhone').value;
            const address = document.getElementById('bookAddress').value;
            const service = document.getElementById('bookService').value;
            const date = document.getElementById('bookDate').value;
            const time = document.getElementById('bookTime').value;
            const area = document.getElementById('bookArea').value;
            
            // Kiểm tra xem tất cả các trường bắt buộc đã được điền chưa
            if (!name || !email || !phone || !address || !service || !date || !time || !area) {
                alert('Vui lòng điền đầy đủ thông tin.');
                e.preventDefault();
                return;
            }
            
            // Xác thực định dạng email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Vui lòng nhập đúng định dạng email.');
                e.preventDefault();
                return;
            }
            
            // Xác thực số điện thoại (xác thực đơn giản cho số điện thoại Việt Nam)
            const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                alert('Vui lòng nhập đúng số điện thoại Việt Nam.');
                e.preventDefault();
                return;
            }
            
            // Xác thực ngày (phải là ngày trong tương lai)
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Vui lòng chọn ngày trong tương lai.');
                e.preventDefault();
                return;
            }
            
            // Xác thực diện tích (phải là số dương)
            if (parseInt(area) <= 0) {
                alert('Diện tích phải là số dương.');
                e.preventDefault();
                return;
            }
            
            // Ghi log thông tin đặt lịch để debug
            console.log({
                name,
                email,
                phone,
                address,
                service,
                date,
                time,
                area,
                note: document.getElementById('bookNote').value
            });
            
            // Nếu mọi thứ hợp lệ, form sẽ được gửi bình thường
        });
    }
    
    // Xử lý chọn dịch vụ để tính toán giá ước tính
    const serviceSelect = document.getElementById('bookService');
    const areaInput = document.getElementById('bookArea');
    
    if (serviceSelect && areaInput) {
        // Hàm cập nhật ước tính giá
        const updatePriceEstimate = function() {
            const service = serviceSelect.value;
            const area = parseInt(areaInput.value) || 0;
            
            // Đây chỉ là minh họa, bạn sẽ triển khai logic tính giá thực tế ở đây
            if (service && area > 0) {
                console.log(`Đang tính giá cho dịch vụ ${service} với diện tích ${area}m²`);
                // Bạn có thể hiển thị ước tính giá trên trang nếu cần
                // const priceElement = document.getElementById('estimatedPrice');
                // if (priceElement) {
                //     priceElement.textContent = calculatePrice(service, area) + ' đ';
                // }
            }
        };
        
        serviceSelect.addEventListener('change', updatePriceEstimate);
        areaInput.addEventListener('input', updatePriceEstimate);
    }
    
    // Xử lý reset form (nếu cần)
    const resetButton = document.querySelector('.booking-form button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            // Dọn dẹp bất kỳ trạng thái hoặc phần tử UI tùy chỉnh nào khi form được reset
            console.log("Form đã được reset");
        });
    }
});

// Tùy chọn: Hàm tính giá dựa trên dịch vụ và diện tích
function calculatePrice(service, area) {
    let basePrice = 0;
    
    // Logic tính giá đơn giản (nên được thay thế bằng cấu trúc tính giá thực tế của bạn)
    if (service === 'home') {
        // Giá vệ sinh nhà ở
        if (area < 50) {
            basePrice = 500000;
        } else if (area < 100) {
            basePrice = 800000;
        } else {
            basePrice = 1000000 + (area - 100) * 8000;
        }
    } else if (service === 'office') {
        // Giá vệ sinh văn phòng
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