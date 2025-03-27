<?php
// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra xem có thông tin đặt lịch thành công không
if (!isset($_SESSION['booking_success']) || $_SESSION['booking_success'] !== true) {
    // Chuyển hướng về trang đặt lịch nếu không có thông tin
    header("Location: booking.php");
    exit;
}

// Lấy thông tin đặt lịch từ session
$bookingId = $_SESSION['booking_id'] ?? 'N/A';
$name = $_SESSION['booking_name'] ?? 'N/A';
$service = $_SESSION['booking_service'] ?? 'N/A';
$date = $_SESSION['booking_date'] ?? 'N/A';
$time = $_SESSION['booking_time'] ?? 'N/A';
$address = $_SESSION['booking_address'] ?? 'N/A';
$area = $_SESSION['booking_area'] ?? 'N/A';

// Định dạng lại ngày tháng
$formatDate = date("d/m/Y", strtotime($date));

// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Đặt Lịch Thành Công - theCleaner";

// Lấy năm hiện tại cho footer
$currentYear = date("Y");
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
    <style>
        .success-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 80px;
            color: #4cd137;
            margin-bottom: 20px;
        }
        
        .booking-details {
            margin: 30px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: left;
        }
        
        .booking-details h3 {
            margin-bottom: 20px;
            color: var(--primary-color);
            text-align: center;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            width: 150px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .detail-value {
            flex: 1;
        }
        
        .action-buttons {
            margin-top: 30px;
        }
        
        .action-buttons .btn {
            margin: 0 10px;
        }
        
        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
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
            <h1>Đặt Lịch Thành Công</h1>
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi</p>
        </div>
    </section>

    <!-- Success Section -->
    <section class="book-service">
        <div class="container">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Đặt Lịch Thành Công!</h2>
                <p>Cảm ơn bạn, <strong><?php echo $name; ?></strong>, đã đặt lịch dịch vụ với theCleaner.</p>
                <p>Chúng tôi đã gửi email xác nhận đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến (và thư rác nếu cần).</p>
                
                <div class="booking-details">
                    <h3>Chi Tiết Đặt Lịch</h3>
                    
                    <div class="detail-row">
                        <div class="detail-label">Mã đặt lịch:</div>
                        <div class="detail-value"><?php echo $bookingId; ?></div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Dịch vụ:</div>
                        <div class="detail-value"><?php echo $service; ?></div>
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
                        <div class="detail-label">Diện tích:</div>
                        <div class="detail-value"><?php echo $area; ?> m²</div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">Địa chỉ:</div>
                        <div class="detail-value"><?php echo $address; ?></div>
                    </div>
                </div>
                
                <p>Chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ để xác nhận lịch.</p>
                <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi theo số điện thoại <strong>+84 123 456 789</strong>.</p>
                
                <div class="action-buttons">
                    <a href="index.php" class="btn btn-outline">Về Trang Chủ</a>
                    <a href="services.php" class="btn btn-primary">Xem Dịch Vụ Khác</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Chia Sẻ Trải Nghiệm Của Bạn</h2>
            <p>Sau khi sử dụng dịch vụ của chúng tôi, hãy đánh giá và chia sẻ trải nghiệm của bạn để giúp chúng tôi cải thiện chất lượng dịch vụ.</p>
            <a href="testimonials.php" class="btn">Đánh Giá Ngay</a>
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

    <script src="../scripts/script.js"></script>
    <script>
        // Xóa thông tin đặt lịch khỏi session sau khi trang được tải
        window.onload = function() {
            // Sử dụng AJAX để xóa session
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'clear_booking_session.php', true);
            xhr.send();
        };
    </script>
</body>
</html>