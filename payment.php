<?php
// Hiển thị lỗi - chỉ sử dụng trong môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Khởi tạo session
session_start();

// Kiểm tra xem thông tin đặt lịch có trong session không
if (!isset($_SESSION['booking_info']) || !isset($_SESSION['estimated_price'])) {
    // Debug - Hiển thị thông tin session
    // echo "<pre>Session: "; print_r($_SESSION); echo "</pre>"; exit;
    
    // Chuyển hướng về trang đặt lịch nếu không có thông tin
    header("Location: booking.php");
    exit;
}

// Lấy thông tin đặt lịch từ session
$bookingInfo = $_SESSION['booking_info'];
$estimatedPrice = $_SESSION['estimated_price'];

// Tạo mã giao dịch
$transactionId = isset($bookingInfo['bookingId']) ? $bookingInfo['bookingId'] : 'TR'.time();

// Thông tin người nhận thanh toán - CẬP NHẬT THÔNG TIN CỦA BẠN Ở ĐÂY
$merchantInfo = [
    "name" => "theCleaner", // Thay bằng tên của bạn
    "phone" => "0326097576", // Thay bằng số điện thoại MoMo của bạn
    "accountName" => "CÔNG TY TNHH DỊCH VỤ VỆ SINH THE CLEANER" // Thay bằng tên tài khoản của bạn
];

// Lấy thông tin dịch vụ
$serviceNames = [
    'home' => 'Vệ sinh nhà ở',
    'office' => 'Vệ sinh văn phòng'
];

$serviceName = isset($serviceNames[$bookingInfo['service']]) ? $serviceNames[$bookingInfo['service']] : $bookingInfo['service'];

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Chuyển đổi định dạng ngày
$formattedDate = date('d/m/Y', strtotime($bookingInfo['date']));

// Định dạng giá tiền
$formattedPrice = number_format($estimatedPrice, 0, ',', '.') . ' đ';

// Mô tả thanh toán
$paymentDescription = "TT DV " . strtoupper($bookingInfo['service']) . " " . $bookingInfo['bookingId'];

// Tạo dữ liệu QR MoMo
// Trong thực tế, bạn sẽ cần tích hợp với API chính thức của MoMo
// Đây là một mô phỏng đơn giản
$qrData = [
    "merchantName" => $merchantInfo['name'],
    "merchantPhone" => $merchantInfo['phone'],
    "amount" => $estimatedPrice,
    "description" => $paymentDescription,
    "transactionId" => $transactionId
];

// Chuyển thành chuỗi JSON để sử dụng với API
$qrDataJson = json_encode($qrData);

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
    // Trong thực tế, bạn sẽ kiểm tra trạng thái thanh toán với API của MoMo
    // Tại đây chỉ mô phỏng xác nhận thanh toán thành công
    $_SESSION['transaction_info']['status'] = 'completed';
    $paymentSuccess = true;
    
    // Chuyển hướng tới trang xác nhận
    header("Location: payment_confirmation.php");
    exit;
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
</head>
<body>
    <!-- Header -->
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

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1>Thanh Toán</h1>
            <p>Hoàn tất thanh toán để xác nhận đặt lịch dịch vụ</p>
        </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section">
        <div class="container">
            <div class="payment-container">
                <!-- Booking Summary -->
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
                
                <!-- Payment Methods -->
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
                            <div class="qr-code">
                                <!-- Sử dụng QR generator của momo_qr_generator.php -->
                                <img src="momo_qr_generator.php?phone=<?php echo urlencode($merchantInfo['phone']); ?>&amount=<?php echo urlencode($estimatedPrice); ?>&description=<?php echo urlencode($paymentDescription); ?>&name=<?php echo urlencode($merchantInfo['name']); ?>" alt="MoMo QR Code">
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
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="confirm_payment" value="1">
                            <button type="submit" class="btn btn-primary">Xác Nhận Đã Thanh Toán</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-col">
                    <h4>Về theCleaner</h4>
                    <p>theCleaner là công ty chuyên cung cấp dịch vụ vệ sinh chuyên nghiệp, với đội ngũ nhân viên chuyên nghiệp và trang thiết bị hiện đại.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Dịch Vụ</h4>
                    <ul class="footer-links">
                        <li><a href="services.php">Vệ sinh nhà ở</a></li>
                        <li><a href="services.php">Vệ sinh văn phòng</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Liên Kết Nhanh</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Trang chủ</a></li>
                        <li><a href="about.php">Về chúng tôi</a></li>
                        <li><a href="services.php">Dịch vụ</a></li>
                        <li><a href="testimonials.php">Đánh giá</a></li>
                        <li><a href="contact.php">Liên hệ</a></li>
                        <li><a href="booking.php">Đặt lịch</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Bản Tin</h4>
                    <p>Đăng ký nhận thông tin khuyến mãi và dịch vụ mới nhất từ chúng tôi.</p>
                    <form method="post" action="process_newsletter.php">
                        <div class="form-group">
                            <input type="email" name="subscribe_email" class="form-control" placeholder="Email của bạn" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Đăng Ký</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo $currentYear; ?> theCleaner. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="scripts/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tự động làm mới QR code mỗi 5 phút để đảm bảo tính hợp lệ
            setTimeout(function() {
                if (!document.hidden) {
                    location.reload();
                }
            }, 300000); // 5 phút = 300,000 ms
        });
    </script>
</body>
</html>