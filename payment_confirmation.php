<?php
// Khởi tạo session
session_start();

// Kiểm tra xem thông tin giao dịch có trong session không
if (!isset($_SESSION['transaction_info']) || !isset($_SESSION['booking_info'])) {
    // Chuyển hướng về trang đặt lịch nếu không có thông tin
    header("Location: booking.php");
    exit;
}

// Lấy thông tin giao dịch và đặt lịch từ session
$transactionInfo = $_SESSION['transaction_info'];
$bookingInfo = $_SESSION['booking_info'];
$estimatedPrice = $_SESSION['estimated_price'];

// Kiểm tra trạng thái thanh toán
$paymentStatus = isset($transactionInfo['status']) ? $transactionInfo['status'] : 'pending';

// Lấy thông tin dịch vụ
$serviceNames = [
    'home' => 'Vệ sinh nhà ở',
    'office' => 'Vệ sinh văn phòng'
];
$serviceName = isset($serviceNames[$bookingInfo['service']]) ? $serviceNames[$bookingInfo['service']] : $bookingInfo['service'];

// Chuyển đổi định dạng ngày
$formattedDate = date('d/m/Y', strtotime($bookingInfo['date']));

// Định dạng giá tiền
$formattedPrice = number_format($estimatedPrice, 0, ',', '.') . ' đ';

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Tạo mã đặt lịch ngắn gọn để hiển thị
$bookingCode = substr($bookingInfo['bookingId'], -8);

// Lấy thời gian hiện tại cho ngày xác nhận
$confirmationDate = date('d/m/Y H:i:s');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Thanh Toán - theCleaner</title>
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
            <h1>Xác Nhận Thanh Toán</h1>
            <p>Cảm ơn bạn đã sử dụng dịch vụ của theCleaner</p>
        </div>
    </section>

    <!-- Confirmation Section -->
    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <!-- Success Message -->
                <div class="confirmation-message">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2>Đặt Lịch Thành Công!</h2>
                    <p>Cảm ơn bạn đã đặt lịch và thanh toán dịch vụ vệ sinh của theCleaner.</p>
                    <p>Mã đặt lịch của bạn là <strong><?php echo $bookingCode; ?></strong></p>
                    <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận lịch thực hiện dịch vụ.</p>
                </div>

                <!-- Confirmation Details -->
                <div class="confirmation-details">
                    <h3>Chi Tiết Đặt Lịch</h3>
                    <div class="details-grid">
                        <div class="details-item">
                            <strong>Họ và tên</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['name']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Email</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['email']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Số điện thoại</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['phone']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Dịch vụ</strong>
                            <span><?php echo htmlspecialchars($serviceName); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Ngày thực hiện</strong>
                            <span><?php echo htmlspecialchars($formattedDate); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Thời gian</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['time']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Địa chỉ</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['address']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Diện tích</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['area']); ?> m²</span>
                        </div>
                        <div class="details-item">
                            <strong>Phương thức thanh toán</strong>
                            <span>MoMo</span>
                        </div>
                        <div class="details-item">
                            <strong>Trạng thái thanh toán</strong>
                            <span class="payment-status-completed">Đã thanh toán</span>
                        </div>
                        <div class="details-item">
                            <strong>Thời gian xác nhận</strong>
                            <span><?php echo $confirmationDate; ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Mã giao dịch</strong>
                            <span><?php echo htmlspecialchars($transactionInfo['transaction_id']); ?></span>
                        </div>
                        <div class="details-item total">
                            <strong>Tổng thanh toán</strong>
                            <span><?php echo $formattedPrice; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3>Các Bước Tiếp Theo</h3>
                    <ul>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="step-content">
                                <h4>Xác Nhận Qua Điện Thoại</h4>
                                <p>Nhân viên của chúng tôi sẽ gọi điện để xác nhận thông tin và thời gian thực hiện dịch vụ.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="step-content">
                                <h4>Thực Hiện Dịch Vụ</h4>
                                <p>Đội ngũ nhân viên sẽ đến đúng hẹn và thực hiện dịch vụ theo yêu cầu của bạn.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="step-content">
                                <h4>Kiểm Tra Và Nghiệm Thu</h4>
                                <p>Sau khi hoàn thành, chúng tôi sẽ cùng bạn kiểm tra chất lượng dịch vụ.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Confirmation Actions -->
                <div class="confirmation-actions">
                    <a href="index.php" class="btn btn-primary">Về Trang Chủ</a>
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
            // Thiết lập để in trang nếu cần
            const printButton = document.getElementById('printConfirmation');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    window.print();
                });
            }
        });
    </script>
</body>
</html>