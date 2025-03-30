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

// Thông tin nhân viên sẽ làm dịch vụ - Trong thực tế, bạn sẽ lấy thông tin này từ cơ sở dữ liệu
// Ở đây chúng ta sẽ giả định thông tin
$staffMembers = [
    [
        "id" => 1,
        "name" => "Nguyễn Văn A",
        "gender" => "Nam",
        "age" => 28,
        "rating" => 4.9,
        "avatar" => "images/team1.jpg",
        "experience" => "5 năm",
        "specialization" => "Vệ sinh nhà ở",
        "phone" => "0912345678"
    ],
    [
        "id" => 2,
        "name" => "Trần Thị B",
        "gender" => "Nữ",
        "age" => 32,
        "rating" => 4.8,
        "avatar" => "images/team2.jpg",
        "experience" => "7 năm",
        "specialization" => "Vệ sinh văn phòng",
        "phone" => "0923456789"
    ],
    [
        "id" => 3,
        "name" => "Lê Văn C",
        "gender" => "Nam",
        "age" => 25,
        "rating" => 4.7,
        "avatar" => "images/team3.jpg",
        "experience" => "3 năm",
        "specialization" => "Vệ sinh nhà ở",
        "phone" => "0934567890"
    ]
];

// Chọn nhân viên dựa trên loại dịch vụ
// Trong thực tế, bạn sẽ có thuật toán phức tạp hơn để chọn nhân viên phù hợp
$selectedStaff = null;
foreach ($staffMembers as $staff) {
    if ($staff["specialization"] == $serviceName) {
        $selectedStaff = $staff;
        break;
    }
}

// Nếu không tìm thấy nhân viên phù hợp, chọn người đầu tiên
if ($selectedStaff === null && count($staffMembers) > 0) {
    $selectedStaff = $staffMembers[0];
}

// Xác định domain website để dùng cho đường dẫn hình ảnh trong email
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'thecleaner.com';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$baseUrl = $protocol . "://" . $domain;

// Tạo mẫu email hóa đơn
$emailTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đặt Lịch - theCleaner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #00a8ff;
            padding: 20px;
            text-align: center;
            color: white;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .booking-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .booking-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .booking-details th, .booking-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .staff-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        .staff-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .staff-details {
            flex: 1;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00a8ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .highlight {
            font-weight: bold;
            color: #00a8ff;
        }
        .rating {
            color: #fbc531;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Xác Nhận Đặt Lịch</h1>
            <p>Cảm ơn bạn đã đặt lịch dịch vụ tại theCleaner!</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>' . htmlspecialchars($bookingInfo['name']) . '</strong>,</p>
            <p>Chúng tôi xác nhận đã nhận được đặt lịch dịch vụ vệ sinh của bạn. Dưới đây là chi tiết đặt lịch:</p>
            
            <div class="booking-details">
                <h2>Chi Tiết Đặt Lịch</h2>
                <table>
                    <tr>
                        <th>Mã đặt lịch:</th>
                        <td class="highlight">' . $bookingCode . '</td>
                    </tr>
                    <tr>
                        <th>Dịch vụ:</th>
                        <td>' . htmlspecialchars($serviceName) . '</td>
                    </tr>
                    <tr>
                        <th>Ngày thực hiện:</th>
                        <td>' . htmlspecialchars($formattedDate) . '</td>
                    </tr>
                    <tr>
                        <th>Thời gian:</th>
                        <td>' . htmlspecialchars($bookingInfo['time']) . '</td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td>' . htmlspecialchars($bookingInfo['address']) . '</td>
                    </tr>
                    <tr>
                        <th>Diện tích:</th>
                        <td>' . htmlspecialchars($bookingInfo['area']) . ' m²</td>
                    </tr>
                    <tr>
                        <th>Tổng thanh toán:</th>
                        <td class="highlight">' . $formattedPrice . '</td>
                    </tr>
                    <tr>
                        <th>Trạng thái thanh toán:</th>
                        <td>Đã thanh toán</td>
                    </tr>
                </table>
            </div>
            
            <div class="staff-info">
                <img src="' . $baseUrl . '/' . $selectedStaff['avatar'] . '" alt="' . htmlspecialchars($selectedStaff['name']) . '" class="staff-avatar">
                <div class="staff-details">
                    <h2>Nhân Viên Thực Hiện</h2>
                    <p><strong>Họ tên:</strong> ' . htmlspecialchars($selectedStaff['name']) . '</p>
                    <p><strong>Giới tính:</strong> ' . htmlspecialchars($selectedStaff['gender']) . '</p>
                    <p><strong>Đánh giá:</strong> <span class="rating">' . $selectedStaff['rating'] . '/5</span></p>
                    <p><strong>Kinh nghiệm:</strong> ' . htmlspecialchars($selectedStaff['experience']) . '</p>
                    <p><strong>Liên hệ:</strong> ' . htmlspecialchars($selectedStaff['phone']) . '</p>
                </div>
            </div>
            
            <p>Nhân viên sẽ liên hệ với bạn qua số điện thoại trước khi đến để xác nhận lại. Vui lòng để ý điện thoại trong thời gian này.</p>
            
            <p>Nếu bạn cần thay đổi lịch hoặc có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua số điện thoại <strong>0326097576</strong> hoặc email <strong>support@thecleaner.com</strong>.</p>
            
            <p>Cảm ơn bạn đã sử dụng dịch vụ của theCleaner!</p>
            
            <a href="' . $baseUrl . '" class="button">Truy cập website</a>
        </div>
        <div class="footer">
            <p>&copy; ' . $currentYear . ' theCleaner. Tất cả các quyền được bảo lưu.</p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, Thành phố Hà Nội, Việt Nam</p>
        </div>
    </div>
</body>
</html>';

// Gọi file xử lý gửi email
require_once 'send_email.php';

// Gửi email thật
$emailSent = false;
try {
    $emailSent = sendBookingConfirmationEmail($bookingInfo, $emailTemplate, $bookingCode);
} catch (Exception $e) {
    error_log("Lỗi gửi email: " . $e->getMessage());
}

// Lưu trạng thái gửi email vào session để hiển thị thông báo
$_SESSION['email_sent'] = $emailSent;
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
    <link rel="stylesheet" href="styles/confirmation-styles.css">
</head>
<body>
    <!-- Thông báo gửi email -->
    <?php if (isset($_SESSION['email_sent'])): ?>
    <div class="email-status-notification" style="background: <?php echo $_SESSION['email_sent'] ? '#4caf50' : '#f44336'; ?>;">
        <?php if ($_SESSION['email_sent']): ?>
            <p><i class="fas fa-check-circle"></i> Email xác nhận đã được gửi thành công!</p>
        <?php else: ?>
            <p><i class="fas fa-exclamation-circle"></i> Không thể gửi email xác nhận. Vui lòng liên hệ hỗ trợ!</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Confetti Container for Animation -->
    <div class="confetti-container" id="confettiContainer"></div>
    
    <!-- Email Notification -->
    <div class="email-notification" id="emailNotification">
        <div class="email-header">
            <div class="email-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="email-title">
                <h4>Xác Nhận Đặt Lịch</h4>
                <p>support@thecleaner.com</p>
            </div>
        </div>
        <div class="email-content">
            <p>Email xác nhận đặt lịch và hóa đơn đã được gửi đến hộp thư của bạn.</p>
        </div>
        <div class="email-actions">
            <button id="closeEmailNotification">Đóng</button>
        </div>
    </div>

    <!-- Header
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
    </header> -->

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

                <!-- Assigned Staff Section -->
                <?php if ($selectedStaff): ?>
                <div class="assigned-staff">
                    <div class="section-title">
                        <h2>Nhân Viên Được Phân Công</h2>
                        <p>Nhân viên này sẽ liên hệ và đến thực hiện dịch vụ cho bạn</p>
                    </div>
                    <div class="staff-profile" id="staffProfile">
                        <div class="staff-avatar">
                            <img src="<?php echo $selectedStaff['avatar']; ?>" alt="<?php echo htmlspecialchars($selectedStaff['name']); ?>">
                            <div class="staff-rating">
                                <i class="fas fa-star"></i> <?php echo $selectedStaff['rating']; ?>
                            </div>
                        </div>
                        <div class="staff-details">
                            <h3><?php echo htmlspecialchars($selectedStaff['name']); ?></h3>
                            <div class="staff-info">
                                <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($selectedStaff['gender']); ?></p>
                                <p><strong>Tuổi:</strong> <?php echo $selectedStaff['age']; ?></p>
                                <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($selectedStaff['experience']); ?></p>
                                <p><strong>Chuyên môn:</strong> <?php echo htmlspecialchars($selectedStaff['specialization']); ?></p>
                                <p><strong>Liên hệ:</strong> <?php echo htmlspecialchars($selectedStaff['phone']); ?></p>
                            </div>
                            <div class="staff-note">
                                <p><i class="fas fa-info-circle"></i> Nhân viên sẽ liên hệ với bạn qua số điện thoại trước khi đến để xác nhận lại. Vui lòng để ý điện thoại trong thời gian này.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Next Steps -->
                <div class="next-steps">
                    <h3>Các Bước Tiếp Theo</h3>
                    <ul>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="step-content">
                                <h4>Xác Nhận Qua Email</h4>
                                <p>Nhân viên của chúng tôi sẽ gửi email để xác nhận thông tin và thời gian thực hiện dịch vụ.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="step-content">
                                <h4>Liên Hệ Trước Khi Đến</h4>
                                <p>Nhân viên sẽ gọi điện trước khi đến để xác nhận thời gian và địa chỉ chính xác.</p>
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
                    <a href="#" id="viewEmailBtn" class="btn btn-outline">Xem Email</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Email Preview Modal -->
    <div id="emailModal">
        <div>
            <button id="closeEmailModal">&times;</button>
            <h2 style="text-align: center; margin-bottom: 20px;">Email Hóa Đơn</h2>
            <div id="emailPreview">
                <?php echo $emailTemplate; ?>
            </div>
        </div>
    </div>

    <!-- Footer
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
    </footer> -->

    <script src="scripts/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation cho staff profile
            const staffProfile = document.getElementById('staffProfile');
            if (staffProfile) {
                setTimeout(() => {
                    staffProfile.classList.add('visible');
                }, 1000);
            }
            
            // Hiển thị notification email đã gửi
            const emailNotification = document.getElementById('emailNotification');
            if (emailNotification) {
                setTimeout(() => {
                    emailNotification.classList.add('show');
                }, 3000);
            }
            
            // Đóng notification email
            const closeEmailNotification = document.getElementById('closeEmailNotification');
            if (closeEmailNotification && emailNotification) {
                closeEmailNotification.addEventListener('click', function() {
                    emailNotification.classList.remove('show');
                });
            }
            
            // Hiển thị modal email preview
            const viewEmailBtn = document.getElementById('viewEmailBtn');
            const emailModal = document.getElementById('emailModal');
            const closeEmailModal = document.getElementById('closeEmailModal');
            
            if (viewEmailBtn && emailModal) {
                viewEmailBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    emailModal.style.display = 'block';
                });
            }
            
            if (closeEmailModal && emailModal) {
                closeEmailModal.addEventListener('click', function() {
                    emailModal.style.display = 'none';
                });
            }
            
            // Đóng modal khi click ngoài nội dung
            window.addEventListener('click', function(e) {
                if (emailModal && e.target === emailModal) {
                    emailModal.style.display = 'none';
                }
            });
            
            // Tạo hiệu ứng Confetti
            const confettiContainer = document.getElementById('confettiContainer');
            const colors = ['#f94144', '#f3722c', '#f8961e', '#f9c74f', '#90be6d', '#43aa8b', '#577590'];
            
            function createConfetti() {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.opacity = Math.random() * 0.5 + 0.5;
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
                
                confettiContainer.appendChild(confetti);
                
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
            
            // Tạo hiệu ứng confetti khi trang tải xong
            if (confettiContainer) {
                for (let i = 0; i < 100; i++) {
                    setTimeout(createConfetti, i * 100);
                }
            }
            
            // Xóa thông báo trạng thái email sau 5 giây
            const emailStatusNotification = document.querySelector('.email-status-notification');
            if (emailStatusNotification) {
                setTimeout(() => {
                    emailStatusNotification.style.opacity = '0';
                    setTimeout(() => {
                        emailStatusNotification.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>