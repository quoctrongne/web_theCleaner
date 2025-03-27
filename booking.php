<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Đặt Lịch - theCleaner";
$currentPage = "booking";

// Danh sách dịch vụ
$serviceOptions = [
    ["value" => "home", "label" => "Vệ sinh nhà ở"],
    ["value" => "office", "label" => "Vệ sinh văn phòng"]
];

// Danh sách thời gian
$timeSlots = [
    ["value" => "8-10", "label" => "8:00 - 10:00"],
    ["value" => "10-12", "label" => "10:00 - 12:00"],
    ["value" => "13-15", "label" => "13:00 - 15:00"],
    ["value" => "15-17", "label" => "15:00 - 17:00"]
];

// Quy trình đặt lịch
$bookingProcess = [
    ["number" => 1, "title" => "Đặt Lịch", "description" => "Điền form đặt lịch trên website hoặc gọi điện trực tiếp cho chúng tôi"],
    ["number" => 2, "title" => "Xác Nhận", "description" => "Chúng tôi sẽ liên hệ xác nhận thông tin và thời gian thực hiện"],
    ["number" => 3, "title" => "Thực Hiện", "description" => "Nhân viên sẽ đến đúng hẹn và thực hiện dịch vụ theo yêu cầu"],
    ["number" => 4, "title" => "Thanh Toán", "description" => "Thanh toán sau khi hoàn thành và hài lòng với dịch vụ"]
];

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Lấy ngày mai cho giá trị mặc định trong form
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Xử lý form nếu được gửi
$formSubmitted = false;
$formError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_submit'])) {
    // Validate form
    if (
        !empty($_POST['bookName']) && 
        !empty($_POST['bookEmail']) && 
        !empty($_POST['bookPhone']) && 
        !empty($_POST['bookAddress']) && 
        !empty($_POST['bookService']) && 
        !empty($_POST['bookDate']) && 
        !empty($_POST['bookTime']) && 
        !empty($_POST['bookArea'])
    ) {
        // Form đã được gửi thành công
        $formSubmitted = true;
        
        // Trong một ứng dụng thực tế, bạn sẽ xử lý dữ liệu form ở đây
        // Ví dụ: gửi email, lưu vào cơ sở dữ liệu, v.v.
    } else {
        // Form có lỗi
        $formError = true;
    }
}
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
                    <li><a href="about.php">Về Chúng Tôi</a></li>  <!-- Đã sửa -->
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
            <h1>Đặt Lịch Dịch Vụ</h1>
            <p>Đặt lịch dịch vụ vệ sinh chuyên nghiệp chỉ trong vài phút</p>
        </div>
    </section>

    <!-- Book Service Section -->
    <section class="book-service">
        <div class="container">
            <div class="section-title">
                <h2>Đặt Lịch Dịch Vụ</h2>
                <p>Điền thông tin bên dưới để đặt lịch dịch vụ vệ sinh</p>
            </div>
            <div class="booking-form-container">
                <?php if ($formSubmitted): ?>
                <div class="form-success">
                    <i class="fas fa-check-circle"></i>
                    <h3>Đặt lịch thành công!</h3>
                    <p>Cảm ơn bạn đã đặt lịch dịch vụ. Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>
                    <p>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ số điện thoại: <a href="tel:+84123456789">+84 123 456 789</a></p>
                    <a href="services.php" class="btn btn-primary">Xem Thêm Dịch Vụ</a>
                </div>
                <?php else: ?>
                
                <?php if ($formError): ?>
                <div class="form-error">
                    <p>Vui lòng điền đầy đủ thông tin trong form.</p>
                </div>
                <?php endif; ?>
                
                <form id="bookingForm" class="booking-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bookName">Họ và tên</label>
                            <input type="text" id="bookName" name="bookName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bookEmail">Email</label>
                            <input type="email" id="bookEmail" name="bookEmail" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bookPhone">Số điện thoại</label>
                            <input type="tel" id="bookPhone" name="bookPhone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bookAddress">Địa chỉ</label>
                            <input type="text" id="bookAddress" name="bookAddress" class="form-control" placeholder="Nhập địa chỉ" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bookService">Loại dịch vụ</label>
                            <select id="bookService" name="bookService" class="form-control" required>
                                <option value="" disabled selected>Chọn dịch vụ</option>
                                <?php foreach ($serviceOptions as $option): ?>
                                <option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bookDate">Ngày thực hiện</label>
                            <input type="date" id="bookDate" name="bookDate" class="form-control" min="<?php echo $tomorrow; ?>" value="<?php echo $tomorrow; ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bookTime">Thời gian</label>
                            <select id="bookTime" name="bookTime" class="form-control" required>
                                <option value="" disabled selected>Chọn thời gian</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?php echo $slot['value']; ?>"><?php echo $slot['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bookArea">Diện tích (m²)</label>
                            <input type="number" id="bookArea" name="bookArea" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bookNote">Ghi chú thêm</label>
                        <textarea id="bookNote" name="bookNote" class="form-control" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="booking_submit" value="1">
                    <button type="submit" class="btn btn-primary">Đặt Lịch Ngay</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Booking Process Section -->
    <section class="booking-process">
        <div class="container">
            <div class="section-title">
                <h2>Quy Trình Đặt Lịch</h2>
                <p>Quy trình đơn giản để sử dụng dịch vụ của chúng tôi</p>
            </div>
            <div class="process-container">
                <?php foreach ($bookingProcess as $step): ?>
                <div class="process-step">
                    <div class="step-number"><?php echo $step['number']; ?></div>
                    <h3><?php echo $step['title']; ?></h3>
                    <p><?php echo $step['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Cần Hỗ Trợ Thêm?</h2>
            <p>Nếu bạn cần tư vấn hoặc hỗ trợ về dịch vụ, hãy liên hệ trực tiếp với chúng tôi qua số điện thoại hoặc email.</p>
            <div class="cta-buttons">
                <a href="tel:+84123456789" class="btn"><i class="fas fa-phone"></i> Gọi Ngay</a>
                <a href="contact.php" class="btn btn-outline">Liên Hệ</a>
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
    
    <!-- Custom Scripts -->
    <script src="scripts/script.js"></script>
    <script src="scripts/booking.js"></script>


</body>
</html>