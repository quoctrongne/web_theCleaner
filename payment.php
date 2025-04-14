<?php
// Hiển thị lỗi - chỉ sử dụng trong môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối đến cấu hình
require_once 'config.php';

// Khởi tạo session
session_start();
// Kiểm tra phiên đăng nhập nếu cần
if (!isset($_SESSION['booking_info'])) {
    header("Location: booking.php");
    exit();
}

// Lấy thông tin đặt lịch từ session
$bookingInfo = $_SESSION['booking_info'];
$estimatedPrice = $_SESSION['estimated_price'];

// Tạo mã giao dịch duy nhất
$transactionId = 'TR' . time() . mt_rand(1000, 9999); // Mã giao dịch duy nhất

// Thông tin người nhận thanh toán - Lấy từ cấu hình
$merchantInfo = [
    "name" => get_config('company_name', 'theCleaner'),
    "phone" => get_config('momo_phone', '0326097576'),
    "accountName" => get_config('momo_account_name', 'CÔNG TY TNHH DỊCH VỤ VỆ SINH THE CLEANER')
];

// Lấy thông tin dịch vụ từ cấu hình thay vì database
$serviceName = '';
if ($bookingInfo['service'] == 'home') {
    $serviceName = 'Vệ sinh nhà ở';
} elseif ($bookingInfo['service'] == 'office') {
    $serviceName = 'Vệ sinh văn phòng';
} else {
    $serviceName = $bookingInfo['service'];
}

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Chuyển đổi định dạng ngày
$formattedDate = date('d/m/Y', strtotime($bookingInfo['date']));

// Định dạng giá tiền
$formattedPrice = number_format($estimatedPrice, 0, ',', '.') . ' đ';

// Mô tả thanh toán
$paymentDescription = "TT DV " . strtoupper($bookingInfo['service']) . " " . $bookingInfo['bookingId'];

// Lưu thông tin giao dịch vào session để thanh toán thành công
$_SESSION['transaction_info'] = [
    'transaction_id' => $transactionId,
    'amount' => $estimatedPrice,
    'payment_method' => 'momo',
    'status' => 'pending'
];

// Nếu có POST request xác nhận đã thanh toán
$paymentSuccess = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    // Cập nhật trạng thái thanh toán
    $_SESSION['transaction_info']['status'] = 'completed';
    $paymentSuccess = true;

    // Ghi log để debug
    error_log("Đã cập nhật trạng thái thanh toán thành 'completed'");
    error_log("Transaction info sau cập nhật: " . print_r($_SESSION['transaction_info'], true));

    // Chuyển hướng đến trang xác nhận
     header("Location: payment_confirmation.php");
     exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - theCleaner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/payment.css">
    <link rel="stylesheet" href="styles/payment_styles.css">
    <link rel="stylesheet" href="styles/payment_success.css">
    <style>
      .loading-spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.7);
        border-top: 2px solid rgba(255, 255, 255, 0);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 5px;
        vertical-align: middle;
      }
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">the<span>Cleaner</span></a>
                <div class="menu-btn" id="menuBtn">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php">Trang Chủ</a></li>
                    <li><a href="services.php">Dịch Vụ</a></li>
                    <li><a href="about.php">Về Chúng Tôi</a></li>
                    <li><a href="testimonials.php">Đánh Giá</a></li>
                    <li><a href="contact.php">Liên Hệ</a></li>
                    <li><a href="booking.php" class="btn btn-primary">Đặt Lịch</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-banner">
        <div class="container">
            <h1>Thanh Toán</h1>
            <p>Hoàn tất thanh toán để xác nhận đặt lịch dịch vụ</p>
        </div>
    </section>

    <section class="payment-section">
        <div class="container">
            <div class="payment-container">
                <div class="booking-summary">
                    <h2>Thông Tin Đặt Lịch</h2>
                    <div class="summary-details">
                        <div class="summary-item">
                            <span class="label">Họ và tên:</span>
                            <span class="value"><?php echo isset($bookingInfo['name']) ? htmlspecialchars($bookingInfo['name']) : ''; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Dịch vụ:</span>
                            <span class="value"><?php echo htmlspecialchars($serviceName); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Ngày thực hiện:</span>
                            <span class="value"><?php echo htmlspecialchars($formattedDate); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Thời gian:</span>
                            <span class="value"><?php echo isset($bookingInfo['time']) ? htmlspecialchars($bookingInfo['time']) : ''; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Địa chỉ:</span>
                            <span class="value"><?php echo isset($bookingInfo['address']) ? htmlspecialchars($bookingInfo['address']) : ''; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="label">Diện tích:</span>
                            <span class="value"><?php echo isset($bookingInfo['area']) ? htmlspecialchars($bookingInfo['area']) : ''; ?> m²</span>
                        </div>
                        <?php if (!empty($bookingInfo['note'])): ?>
                        <div class="summary-item">
                            <span class="label">Ghi chú:</span>
                            <span class="value"><?php echo htmlspecialchars($bookingInfo['note']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-item total">
                            <span class="label">Tổng thanh toán:</span>
                            <span class="value"><?php echo $formattedPrice; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <h2>Phương Thức Thanh Toán</h2>
                    <div class="momo-container">
                        <div class="momo-info">
                            <div class="momo-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="momo-details">
                                <h3>Thanh toán qua MoMo</h3>
                                <p>Quét mã QR bằng ứng dụng MoMo để thanh toán</p>
                            </div>
                        </div>
                        
                        <div class="qr-container">
                            <div class="qr-code" id="qrCodeBox">
                                <img src="momo_qr_generator.php?phone=<?php echo urlencode($merchantInfo['phone']); ?>&amount=<?php echo urlencode($estimatedPrice); ?>&description=<?php echo urlencode($paymentDescription); ?>&name=<?php echo urlencode($merchantInfo['name']); ?>&transaction_id=<?php echo urlencode($transactionId); ?>" alt="MoMo QR Code">
                                <p class="qr-note">Quét mã bằng ứng dụng MoMo</p>
                            </div>
                            <div class="qr-instructions">
                                <h4>Hướng dẫn thanh toán:</h4>
                                <ol>
                                    <li>Mở ứng dụng MoMo trên điện thoại</li>
                                    <li>Chọn "Quét mã" hoặc biểu tượng QR</li>
                                    <li>Quét mã QR bên trái</li>
                                    <li>Xác nhận thông tin và số tiền thanh toán</li>
                                    <li>Hoàn tất thanh toán trên ứng dụng</li>
                                    <li>Sau khi thanh toán thành công, nhấn "Xác nhận đã thanh toán" bên dưới</li>
                                </ol>
                                
                                <div class="momo-payment-info">
                                    <p><strong>Thông tin người nhận:</strong> <?php echo htmlspecialchars($merchantInfo['accountName']); ?></p>
                                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($merchantInfo['phone']); ?></p>
                                    <p><strong>Số tiền:</strong> <?php echo $formattedPrice; ?></p>
                                    <p><strong>Nội dung chuyển khoản:</strong> <?php echo htmlspecialchars($paymentDescription); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-actions">
                        <a href="booking.php" class="btn btn-outline">Quay Lại</a>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="paymentForm">
                            <input type="hidden" name="confirm_payment" value="1">
                            <button type="submit" id="confirmPaymentBtn" class="btn btn-primary">
                                Xác Nhận Đã Thanh Toán
                                <span class="loading-spinner" id="paymentSpinner"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="payment-success-overlay" id="paymentSuccessOverlay" style="display: none;">
        <div class="payment-success-message">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2>Thanh Toán Thành Công!</h2>
            <p>Cảm ơn bạn đã thanh toán. Đơn đặt lịch của bạn đã được xác nhận.</p>
            <p>Bạn sẽ được chuyển đến trang xác nhận đặt lịch trong vài giây...</p>
        </div>
    </div>

    <script src="scripts/script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thêm hiệu ứng khi nhấn nút thanh toán
        document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
            document.getElementById('paymentSpinner').style.display = 'inline-block';
            document.getElementById('confirmPaymentBtn').disabled = true; // Disable button
            document.getElementById('paymentForm').submit(); // kích hoạt submit form
        });
        
        // Nếu form được submit thành công, hiển thị overlay
        if (<?php echo $paymentSuccess ? 'true' : 'false'; ?>) {
            document.getElementById('paymentSuccessOverlay').style.display = 'flex';
            setTimeout(function() {
                window.location.href = 'payment_confirmation.php';
            }, 3000);
        }
    });
    </script>
</body>
</html>