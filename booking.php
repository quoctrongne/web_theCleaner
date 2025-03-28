<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Đặt Lịch - theCleaner";
$currentPage = "booking";

// Khởi tạo session
session_start();

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

// Kiểm tra lỗi từ process_booking.php
$bookingError = "";
if (isset($_SESSION['booking_error'])) {
    $bookingError = $_SESSION['booking_error'];
    unset($_SESSION['booking_error']);
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
                <?php if (!empty($bookingError)): ?>
                <div class="alert alert-error">
                    <p><?php echo $bookingError; ?></p>
                </div>
                <?php endif; ?>
                
                <!-- QUAN TRỌNG: Form đặt lịch với method="post" và action="process_booking.php" -->
                <form id="bookingForm" class="booking-form" method="post" action="process_booking.php">
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
                            <input type="text" id="bookAddress" name="bookAddress" class="form-control" placeholder="Nhập địa chỉ đầy đủ" required>
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
                    <div class="form-pricing">
                        <div class="pricing-note">
                            <p><i class="fas fa-info-circle"></i> Giá dịch vụ được tính dựa trên loại dịch vụ và diện tích. Chi tiết giá sẽ được hiển thị ở trang thanh toán.</p>
                        </div>
                    </div>
                    <input type="hidden" name="formattedAddress" id="formattedAddress" value="">
                    <input type="hidden" name="latitude" id="latitude" value="">
                    <input type="hidden" name="longitude" id="longitude" value="">
                    <!-- Nút đặt lịch - Submit form -->
                    <button type="submit" class="btn btn-primary">Đặt Lịch Ngay</button>
                </form>
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
    <script>
    // JavaScript cho trang đặt lịch
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded for booking page");
        
        // Thiết lập ngày tối thiểu cho đặt lịch (ngày mai)
        const dateInput = document.getElementById('bookDate');
        if (dateInput) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            dateInput.setAttribute('min', tomorrowStr);
        }
        
        // Xác thực form trước khi gửi
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                // Kiểm tra các trường bắt buộc
                const requiredFields = [
                    { id: 'bookName', message: 'Vui lòng nhập họ tên' },
                    { id: 'bookEmail', message: 'Vui lòng nhập email' },
                    { id: 'bookPhone', message: 'Vui lòng nhập số điện thoại' },
                    { id: 'bookAddress', message: 'Vui lòng nhập địa chỉ' },
                    { id: 'bookService', message: 'Vui lòng chọn dịch vụ' },
                    { id: 'bookDate', message: 'Vui lòng chọn ngày' },
                    { id: 'bookTime', message: 'Vui lòng chọn thời gian' },
                    { id: 'bookArea', message: 'Vui lòng nhập diện tích' }
                ];
                
                for (const field of requiredFields) {
                    const element = document.getElementById(field.id);
                    if (!element.value) {
                        e.preventDefault();
                        alert(field.message);
                        element.focus();
                        return;
                    }
                }
                
                // Kiểm tra định dạng email
                const email = document.getElementById('bookEmail').value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Vui lòng nhập đúng định dạng email');
                    document.getElementById('bookEmail').focus();
                    return;
                }
                
                // Kiểm tra định dạng số điện thoại (Việt Nam)
                const phone = document.getElementById('bookPhone').value;
                const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
                if (!phoneRegex.test(phone)) {
                    e.preventDefault();
                    alert('Vui lòng nhập đúng định dạng số điện thoại Việt Nam');
                    document.getElementById('bookPhone').focus();
                    return;
                }
                
                // Gán địa chỉ đã định dạng nếu chưa có
                if (!document.getElementById('formattedAddress').value) {
                    document.getElementById('formattedAddress').value = document.getElementById('bookAddress').value;
                }
                
                // Tiếp tục submit form nếu tất cả điều kiện đều hợp lệ
                console.log('Form submitted successfully');
            });
        }
    });
    </script>
</body>
</html>