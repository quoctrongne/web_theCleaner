/**
 * File: payment-success.js
 * Xử lý logic hiển thị thông báo thanh toán thành công
 */

document.addEventListener('DOMContentLoaded', function() {
    const confirmButton = document.getElementById('confirmPaymentBtn');
    const paymentForm = document.getElementById('paymentForm');
    const paymentSpinner = document.getElementById('paymentSpinner');
    const successOverlay = document.getElementById('paymentSuccessOverlay');
    const qrCodeBox = document.getElementById('qrCodeBox');
    
    // Xử lý nút xác nhận thanh toán
    if (confirmButton && paymentForm) {
        confirmButton.addEventListener('click', function() {
            // Hiển thị spinner và vô hiệu hóa nút
            confirmButton.classList.add('btn-processing');
            if (paymentSpinner) {
                paymentSpinner.style.display = 'inline-block';
            }
            confirmButton.disabled = true;
            
            // Highlight QR code để biểu thị đang kiểm tra
            if (qrCodeBox) {
                qrCodeBox.classList.add('checking');
            }
            
            // Mô phỏng quá trình kiểm tra thanh toán (3 giây)
            setTimeout(function() {
                // Chuyển từ trạng thái kiểm tra sang thành công
                if (qrCodeBox) {
                    qrCodeBox.classList.remove('checking');
                    qrCodeBox.classList.add('success');
                }
                
                // Hiển thị overlay thành công
                showSuccessOverlay();
                
                // Chuyển đến trang xác nhận sau 3 giây
                setTimeout(function() {
                    paymentForm.submit();
                }, 3000);
            }, 3000);
        });
    }
    
    // Nếu đã thanh toán thành công (từ biến PHP)
    if (typeof paymentSuccess !== 'undefined' && paymentSuccess) {
        // Hiển thị thông báo thành công và chuyển hướng
        setTimeout(function() {
            showSuccessOverlay();
            setTimeout(function() {
                window.location.href = 'payment_confirmation.php';
            }, 3000);
        }, 500);
    }
    
    // Hàm hiển thị overlay thông báo thành công
    function showSuccessOverlay() {
        if (successOverlay) {
            // Sử dụng cả class và inline styles để đảm bảo hiển thị
            successOverlay.classList.add('active');
            successOverlay.style.display = 'flex';
            successOverlay.style.opacity = '1';
            successOverlay.style.visibility = 'visible';
        }
    }
    
    // Tự động làm mới QR code mỗi 5 phút để đảm bảo tính hợp lệ
    setTimeout(function() {
        if (!document.hidden) {
            location.reload();
        }
    }, 300000); // 5 phút = 300,000 ms
});