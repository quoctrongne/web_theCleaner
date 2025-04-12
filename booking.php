<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Đặt Lịch - theCleaner";
$currentPage = "booking";

// Khởi tạo session
session_start();

// Kết nối đến cấu hình
require_once 'config.php';

// Lấy danh sách dịch vụ từ mảng tĩnh thay vì database
$serviceOptions = [
    ['value' => 'home', 'label' => 'Vệ sinh nhà ở'],
    ['value' => 'office', 'label' => 'Vệ sinh văn phòng']
];

// Lấy danh sách thời gian từ mảng tĩnh
$timeSlots = [
    ["value" => "8-10", "label" => "8:00 - 10:00"],
    ["value" => "10-12", "label" => "10:00 - 12:00"],
    ["value" => "13-15", "label" => "13:00 - 15:00"],
    ["value" => "15-17", "label" => "15:00 - 17:00"]
];

// Quy trình đặt lịch (có thể lưu vào database nếu muốn)
$bookingProcess = [
    ["number" => 1, "title" => "Đặt Lịch", "description" => "Điền form đặt lịch trên website hoặc gọi điện trực tiếp cho chúng tôi"],
    ["number" => 2, "title" => "Thanh Toán", "description" => "Thanh toán trước khi dịch vụ được bạn đặt lịch"],
    ["number" => 3, "title" => "Thực Hiện", "description" => "Nhân viên sẽ đến đúng hẹn và thực hiện dịch vụ theo yêu cầu"],
    ["number" => 4, "title" => "Đánh Giá", "description"=> "Bạn có thể đánh giá dịch vụ của chúng tôi trên website"]
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
                
                <!-- Form đặt lịch với method="post" và action="process_booking.php" -->
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
                    <input type="hidden" name="formattedAddress" id="formattedAddress" value=""/>
                    <input type="hidden" name="latitude" id="latitude" value=""/>
                    <input type="hidden" name="longitude" id="longitude" value=""/>
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
            </div>
        </div>
    </footer>

    <!-- Custom Scripts -->
    <script src="scripts/script.js"></script>
</body>
</html>