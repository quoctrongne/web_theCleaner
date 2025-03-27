<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Liên Hệ - theCleaner";
$currentPage = "contact";

// Thông tin liên hệ
$contactInfo = [
    [
        "icon" => "fas fa-map-marker-alt",
        "title" => "Địa Chỉ",
        "content" => ["123 Đường ABC, Quận XYZ", "Thành phố Hà Nội, Việt Nam"]
    ],
    [
        "icon" => "fas fa-phone-alt",
        "title" => "Điện Thoại",
        "content" => ["+84 123 456 789", "+84 987 654 321"]
    ],
    [
        "icon" => "fas fa-envelope",
        "title" => "Email",
        "content" => ["info@thecleaner.com", "support@thecleaner.com"]
    ],
    [
        "icon" => "fas fa-clock",
        "title" => "Giờ Làm Việc",
        "content" => ["Thứ Hai - Thứ Bảy: 8:00 - 18:00", "Chủ Nhật: Nghỉ"]
    ]
];

// Danh sách dịch vụ cho form liên hệ
$serviceOptions = [
    ["value" => "home", "label" => "Vệ sinh nhà ở"],
    ["value" => "office", "label" => "Vệ sinh văn phòng"]
];

// Danh sách câu hỏi thường gặp
$faqs = [
    [
        "question" => "Tôi có thể đặt lịch dịch vụ như thế nào?",
        "answer" => "Bạn có thể đặt lịch dịch vụ thông qua trang đặt lịch trên website, gọi điện thoại hoặc gửi email cho chúng tôi. Chúng tôi sẽ liên hệ xác nhận trong vòng 24 giờ sau khi nhận được yêu cầu."
    ],
    [
        "question" => "Thời gian thực hiện dịch vụ là bao lâu?",
        "answer" => "Thời gian thực hiện dịch vụ phụ thuộc vào loại dịch vụ và diện tích cần vệ sinh. Thông thường, dịch vụ vệ sinh nhà ở có diện tích trung bình sẽ mất khoảng 3-5 giờ, vệ sinh văn phòng có thể mất từ 2-8 giờ tùy quy mô."
    ],
    [
        "question" => "Tôi có thể hủy hoặc thay đổi lịch đã đặt không?",
        "answer" => "Có, bạn có thể hủy hoặc thay đổi lịch đã đặt ít nhất 24 giờ trước thời gian đã hẹn. Vui lòng liên hệ với chúng tôi qua điện thoại hoặc email để thực hiện việc thay đổi."
    ],
    [
        "question" => "Các phương thức thanh toán được chấp nhận?",
        "answer" => "Chúng tôi chấp nhận nhiều phương thức thanh toán khác nhau bao gồm tiền mặt, chuyển khoản ngân hàng, thẻ tín dụng/ghi nợ và ví điện tử (MoMo, ZaloPay, VNPay)."
    ]
];

// Danh sách chi nhánh
$branches = [
    [
        "name" => "Chi Nhánh Hà Nội",
        "address" => "123 Đường ABC, Quận XYZ, Hà Nội",
        "phone" => "+84 123 456 789",
        "email" => "hanoi@thecleaner.com"
    ],
    [
        "name" => "Chi Nhánh TP.HCM",
        "address" => "456 Đường DEF, Quận UVW, TP.HCM",
        "phone" => "+84 987 654 321",
        "email" => "hcm@thecleaner.com"
    ],
    [
        "name" => "Chi Nhánh Đà Nẵng",
        "address" => "789 Đường GHI, Quận JKL, Đà Nẵng",
        "phone" => "+84 456 789 123",
        "email" => "danang@thecleaner.com"
    ]
];

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Xử lý form nếu được gửi
$formSubmitted = false;
$formError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {
    // Validate form
    if (
        !empty($_POST['name']) && 
        !empty($_POST['email']) && 
        !empty($_POST['phone']) && 
        !empty($_POST['service']) && 
        !empty($_POST['message'])
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
    <link rel="stylesheet" href="styles/contact.css">
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
            <h1>Liên Hệ Với Chúng Tôi</h1>
            <p>Hãy để lại thông tin, chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất</p>
        </div>
    </section>

    <!-- Contact Info -->
    <section class="contact-info-section">
        <div class="container">
            <div class="contact-info-container">
                <?php foreach ($contactInfo as $info): ?>
                <div class="contact-info-card">
                    <div class="icon">
                        <i class="<?php echo $info['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $info['title']; ?></h3>
                    <?php foreach ($info['content'] as $line): ?>
                    <p><?php echo $line; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Contact Form & Map -->
    <section class="contact-main">
        <div class="container">
            <div class="contact-container">
                <div class="contact-form">
                    <div class="section-title">
                        <h2>Gửi Tin Nhắn</h2>
                        <p>Hãy để lại thông tin, chúng tôi sẽ liên hệ lại trong thời gian sớm nhất</p>
                    </div>
                    
                    <?php if ($formSubmitted): ?>
                    <div class="form-success">
                        <i class="fas fa-check-circle"></i>
                        <h3>Cảm ơn bạn đã liên hệ!</h3>
                        <p>Chúng tôi đã nhận được thông tin của bạn và sẽ phản hồi trong thời gian sớm nhất.</p>
                    </div>
                    <?php else: ?>
                    
                    <?php if ($formError): ?>
                    <div class="form-error">
                        <p>Vui lòng điền đầy đủ thông tin trong form.</p>
                    </div>
                    <?php endif; ?>
                    
                    <form id="contactForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Họ và tên</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="service">Dịch vụ quan tâm</label>
                                <select id="service" name="service" class="form-control" required>
                                    <option value="" disabled selected>Chọn dịch vụ</option>
                                    <?php foreach ($serviceOptions as $option): ?>
                                    <option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Tin nhắn</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <input type="hidden" name="contact_submit" value="1">
                        <button type="submit" class="btn btn-primary">Gửi Tin Nhắn</button>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="contact-map">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0966870111126!2d105.78009817597951!3d21.028806487780458!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4cd0c66f05%3A0xea31563511af2e54!2zMTIzIMSQw6BvIFThuqVuLCBUcnVuZyBI4budYSwgQ-G6p3UgR2nhuqV5LCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1608123792329!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="contact-faq">
        <div class="container">
            <div class="section-title">
                <h2>Câu Hỏi Thường Gặp</h2>
                <p>Những thắc mắc phổ biến về dịch vụ của chúng tôi</p>
            </div>
            <div class="faq-container">
                <?php foreach ($faqs as $index => $faq): ?>
                <div class="faq-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                    <div class="faq-question">
                        <h3><?php echo $faq['question']; ?></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?php echo $faq['answer']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Branch Locations -->
    <section class="branches">
        <div class="container">
            <div class="section-title">
                <h2>Chi Nhánh Của Chúng Tôi</h2>
                <p>Hệ thống chi nhánh trên toàn quốc</p>
            </div>
            <div class="branches-container">
                <?php foreach ($branches as $branch): ?>
                <div class="branch">
                    <h3><?php echo $branch['name']; ?></h3>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $branch['address']; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo $branch['phone']; ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo $branch['email']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Hãy Liên Hệ Ngay Hôm Nay</h2>
            <p>Đội ngũ chăm sóc khách hàng của chúng tôi luôn sẵn sàng hỗ trợ và tư vấn cho bạn về dịch vụ vệ sinh phù hợp nhất.</p>
            <a href="tel:+84123456789" class="btn"><i class="fas fa-phone"></i> Gọi Ngay</a>
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
                        <li><a href="services.php">Vệ sinh nội thất</a></li>
                        <li><a href="services.php">Vệ sinh kính</a></li>
                        <li><a href="services.php">Vệ sinh sau xây dựng</a></li>
                        <li><a href="services.php">Khử trùng & diệt khuẩn</a></li>
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
            // Xử lý FAQ Accordion
            const faqItems = document.querySelectorAll('.faq-item');
            
            if (faqItems.length > 0) {
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question');
                    
                    question.addEventListener('click', () => {
                        const isActive = item.classList.contains('active');
                        
                        // Đóng tất cả các item khác
                        faqItems.forEach(otherItem => {
                            otherItem.classList.remove('active');
                        });
                        
                        // Toggle active class cho item hiện tại
                        if (!isActive) {
                            item.classList.add('active');
                        }
                    });
                });
            }
            
            // Form Validation
            const contactForm = document.getElementById('contactForm');
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    const phone = document.getElementById('phone').value;
                    const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
                    
                    if (!phoneRegex.test(phone)) {
                        e.preventDefault();
                        alert('Vui lòng nhập đúng định dạng số điện thoại Việt Nam.');
                    }
                });
            }
        });
    </script>
</body>
</html>