// JavaScript cho trang thanh toán (payment.php)

document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const paymentDetails = document.querySelectorAll('.payment-details');
    
    function showSelectedPaymentDetails() {
        // Hide all payment details
        paymentDetails.forEach(detail => {
            detail.style.display = 'none';
        });
        
        // Show selected payment method details
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const selectedDetails = document.getElementById(selectedMethod + '-details');
        
        if (selectedDetails) {
            selectedDetails.style.display = 'block';
        }
    }
    
    // Initial display
    showSelectedPaymentDetails();
    
    // Update on change
    paymentOptions.forEach(option => {
        option.addEventListener('change', showSelectedPaymentDetails);
    });

    // Validate payment form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            // For MoMo or bank transfer, show a confirmation dialog
            if (selectedMethod === 'momo' || selectedMethod === 'bank-transfer') {
                const isConfirmed = confirm('Bạn đã hoàn thành chuyển khoản? Chúng tôi sẽ xác nhận giao dịch của bạn.');
                if (!isConfirmed) {
                    e.preventDefault();
                    return;
                }
            }
            
            // Add a loading indicator when form is submitted
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            }
        });
    }

    // Copy payment information to clipboard
    const copyButtons = document.querySelectorAll('.copy-info');
    if (copyButtons.length > 0) {
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                if (textToCopy) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Show success message
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i> Đã sao chép';
                        
                        // Reset text after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    }).catch(err => {
                        console.error('Không thể sao chép: ', err);
                    });
                }
            });
        });
    }
});