<?php
// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra xem có thông tin đặt lịch không
if (!isset($_SESSION['booking_info']) || empty($_SESSION['booking_info'])) {
    // Chuyển hướng về trang đặt lịch nếu không có thông tin
    header("Location: booking.php");
    exit;
}

// Lấy thông tin đặt lịch từ session
$bookingInfo = $_SESSION['booking_info'];
$name = $bookingInfo['name'] ?? 'N/A';
$email = $bookingInfo['email'] ?? 'N/A';
$phone = $bookingInfo['phone'] ?? 'N/A';
$address = $bookingInfo['address'] ?? 'N/A';
$service = $bookingInfo['service'] ?? 'N/A';
$date = $bookingInfo['date'] ?? 'N/A';
$time = $bookingInfo['time'] ?? 'N/A';
$area = $bookingInfo['area'] ?? 'N/A';
$bookingId = $bookingInfo['bookingId'] ?? 'N/A';

// Định dạng lại ngày tháng
$formatDate = date("d/m/Y", strtotime($date));

// Tính toán giá tiền dựa trên dịch vụ và diện tích
$price = 0;
$pricePerSqm = 0;

if ($service == 'home' || $service == 'Vệ sinh nhà ở') {
    if ($area <= 50) {
        $pricePerSqm = 10000; // 10.000đ/m²
    } else if ($area <= 100) {
        $pricePerSqm = 9000; // 9.000đ/m²
    } else {
        $pricePerSqm = 8000; // 8.000đ/m²
    }
    $serviceLabel = 'Vệ sinh nhà ở';
} else if ($service == 'office' || $service == 'Vệ sinh văn phòng') {
    if ($area <= 100) {
        $pricePerSqm = 15000; // 15.000đ/m²
    } else if ($area <= 300) {
        $pricePerSqm = 13000; // 13.000đ/m²
    } else {
        $pricePerSqm = 11000; // 11.000đ/m²
    }
    $serviceLabel = 'Vệ sinh văn phòng';
} else {
    $pricePerSqm = 10000; // Giá mặc định
    $serviceLabel = $service;
}

// Tính tổng tiền
$price = $pricePerSqm * $area;

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Xử lý khi hoàn thành thanh toán
$paymentCompleted = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
    
    // Lưu thông tin thanh toán vào session
    $_SESSION['payment_info'] = [
        'bookingId' => $bookingId,
        'method' => $paymentMethod,
        'amount' => $price,
        'status' => 'completed',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Chuyển hướng tới trang cảm ơn
    header("Location: booking_success.php");
    exit;
}

// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Thanh Toán - theCleaner";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/booking.css">
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
                    <li><a href="booking.php" class="active btn btn-primary">Đặt Lịch</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1>Thanh Toán</h1>
            <p>Hoàn tất đặt lịch dịch vụ của bạn</p>
        </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section">
        <div class="container">
            <div class="payment-container">
                <div class="order-summary">
                    <h2>Thông Tin Đặt Lịch</h2>
                    <div class="booking-details">
                        <div class="detail-row">
                            <div class="detail-label">Mã đặt lịch:</div>
                            <div class="detail-value"><?php echo $bookingId; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Họ tên:</div>
                            <div class="detail-value"><?php echo $name; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Dịch vụ:</div>
                            <div class="detail-value"><?php echo $serviceLabel; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Ngày thực hiện:</div>
                            <div class="detail-value"><?php echo $formatDate; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Thời gian:</div>
                            <div class="detail-value"><?php echo $time; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Địa chỉ:</div>
                            <div class="detail-value"><?php echo $address; ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Diện tích:</div>
                            <div class="detail-value"><?php echo $area; ?> m²</div>
                        </div>
                    </div>

                    <div class="price-calculation">
                        <h3>Chi Tiết Giá</h3>
                        <div class="price-row">
                            <div class="price-label">Đơn giá:</div>
                            <div class="price-value"><?php echo number_format($pricePerSqm, 0, ',', '.'); ?> đ/m²</div>
                        </div>
                        <div class="price-row">
                            <div class="price-label">Diện tích:</div>
                            <div class="price-value"><?php echo $area; ?> m²</div>
                        </div>
                        <div class="price-row total">
                            <div class="price-label">Tổng thanh toán:</div>
                            <div class="price-value"><?php echo number_format($price, 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                </div>

                <div class="payment-methods">
                    <h2>Phương Thức Thanh Toán</h2>
                    <form id="paymentForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="momo" name="payment_method" value="momo" checked>
                                <label for="momo">
                                    <div class="payment-icon momo-icon">
                                        <img src="images/momo-logo.png" alt="MoMo">
                                    </div>
                                    <div class="payment-info">
                                        <h3>Ví MoMo</h3>
                                        <p>Quét mã QR hoặc chuyển khoản đến số điện thoại: <strong>0326097576</strong></p>
                                    </div>
                                </label>
                                <div class="payment-details" id="momo-details">
                                    <div class="qr-code">
                                        <img src="images/momo-qr.png" alt="MoMo QR Code">
                                        <p>Quét mã QR để thanh toán</p>
                                    </div>
                                    <div class="momo-info">
                                        <p><strong>Thông tin thanh toán:</strong></p>
                                        <ul>
                                            <li>Số điện thoại: <span id="momo-phone">0326097576</span> 
                                                <button type="button" class="copy-info" data-copy="0326097576">
                                                    <i class="fas fa-copy"></i> Sao chép
                                                </button>
                                            </li>
                                            <li>Tên người nhận: theCleaner</li>
                                            <li>Số tiền: <?php echo number_format($price, 0, ',', '.'); ?> đ</li>
                                            <li>Nội dung chuyển khoản: <span id="momo-content"><?php echo $bookingId; ?></span>
                                                <button type="button" class="copy-info" data-copy="<?php echo $bookingId; ?>">
                                                    <i class="fas fa-copy"></i> Sao chép
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="bank-transfer" name="payment_method" value="bank-transfer">
                                <label for="bank-transfer">
                                    <div class="payment-icon bank-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h3>Chuyển khoản ngân hàng</h3>
                                        <p>Chuyển khoản trực tiếp đến tài khoản ngân hàng của chúng tôi</p>
                                    </div>
                                </label>
                                <div class="payment-details" id="bank-details">
                                    <div class="bank-info">
                                        <p><strong>Thông tin tài khoản:</strong></p>
                                        <ul>
                                            <li>Ngân hàng: Vietcombank</li>
                                            <li>Số tài khoản: <span id="bank-account">1234567890</span>
                                                <button type="button" class="copy-info" data-copy="1234567890">
                                                    <i class="fas fa-copy"></i> Sao chép
                                                </button>
                                            </li>
                                            <li>Chủ tài khoản: CÔNG TY TNHH THE CLEANER</li>
                                            <li>Số tiền: <?php echo number_format($price, 0, ',', '.'); ?> đ</li>
                                            <li>Nội dung chuyển khoản: <span id="bank-content"><?php echo $bookingId; ?></span>
                                                <button type="button" class="copy-info" data-copy="<?php echo $bookingId; ?>">
                                                    <i class="fas fa-copy"></i> Sao chép
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="cash" name="payment_method" value="cash">
                                <label for="cash">
                                    <div class="payment-icon cash-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h3>Thanh toán khi hoàn thành</h3>
                                        <p>Thanh toán bằng tiền mặt sau khi dịch vụ được thực hiện</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="payment-actions">
                            <a href="booking.php" class="btn btn-outline">Quay Lại</a>
                            <button type="submit" class="btn btn-primary">Xác Nhận Thanh Toán</button>
                        </div>
                    </form>
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
    <script src="scripts/payment.js"></script>
</body>
</html>