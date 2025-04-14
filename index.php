<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "theCleaner - Dịch Vụ Vệ Sinh Chuyên Nghiệp";
$currentPage = "home";

// Mảng các dịch vụ nổi bật
$featuredServices = [
    [
        "icon" => "fas fa-home",
        "title" => "Vệ Sinh Nhà Ở",
        "description" => "Dịch vụ vệ sinh toàn diện cho ngôi nhà của bạn, từ phòng khách, phòng ngủ đến nhà bếp và phòng tắm."
    ],
    [
        "icon" => "fas fa-building",
        "title" => "Vệ Sinh Văn Phòng",
        "description" => "Dịch vụ vệ sinh chuyên nghiệp cho văn phòng, tạo môi trường làm việc sạch sẽ và thoải mái."
    ]
];

// Mảng các lý do chọn dịch vụ
$whyChooseUs = [
    [
        "icon" => "fas fa-medal",
        "title" => "Chất Lượng Cao",
        "description" => "Cam kết mang đến dịch vụ vệ sinh chất lượng cao, đáp ứng mọi yêu cầu khắt khe."
    ],
    [
        "icon" => "fas fa-user-tie",
        "title" => "Nhân Viên Chuyên Nghiệp",
        "description" => "Đội ngũ nhân viên được đào tạo bài bản, giàu kinh nghiệm và có trách nhiệm cao."
    ],
    [
        "icon" => "fas fa-tools",
        "title" => "Trang Thiết Bị Hiện Đại",
        "description" => "Sử dụng máy móc, thiết bị hiện đại nhất trong ngành vệ sinh chuyên nghiệp."
    ],
    [
        "icon" => "fas fa-leaf",
        "title" => "Thân Thiện Môi Trường",
        "description" => "Sử dụng các sản phẩm vệ sinh thân thiện với môi trường và an toàn cho sức khỏe."
    ]
];

// Mảng thống kê
$statistics = [
    ["value" => "10+", "label" => "Năm Kinh Nghiệm"],
    ["value" => "5000+", "label" => "Khách Hàng"],
    ["value" => "50+", "label" => "Nhân Viên"],
    ["value" => "98%", "label" => "Khách Hàng Hài Lòng"]
];

// Đánh giá nổi bật
$featuredTestimonial = [
    "text" => "Tôi rất hài lòng với dịch vụ vệ sinh của theCleaner. Nhân viên chuyên nghiệp, làm việc tỉ mỉ và hiệu quả. Ngôi nhà của tôi giờ đây sạch sẽ và thoáng mát hơn bao giờ hết. Chắc chắn tôi sẽ tiếp tục sử dụng dịch vụ này.",
    "name" => "Nguyễn Văn A",
    "location" => "Khách hàng tại Hà Nội",
    "image" => "images/client1.jpg"
];

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
    <link rel="stylesheet" href="styles/index.css">
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

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Dịch Vụ Vệ Sinh Chuyên Nghiệp</h1>
                <p>Chúng tôi mang đến cho bạn không gian sống và làm việc sạch sẽ, thoáng mát với đội ngũ chuyên nghiệp và trang thiết bị hiện đại.</p>
                <div>
                    <a href="contact.php" class="btn btn-primary">Liên Hệ Ngay</a>
                    <a href="services.php" class="btn btn-outline">Tìm Hiểu Thêm</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Services Preview -->
    <section class="services-preview">
        <div class="container">
            <div class="section-title">
                <h2>Dịch Vụ Nổi Bật</h2>
                <p>Một số dịch vụ tiêu biểu của chúng tôi</p>
            </div>
            <div class="services-container">
                <?php foreach ($featuredServices as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $service['title']; ?></h3>
                    <p><?php echo $service['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-50">
                <a href="services.php" class="btn btn-primary">Xem Dịch Vụ</a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-us">
        <div class="container">
            <div class="section-title">
                <h2>Tại Sao Chọn Chúng Tôi</h2>
                <p>Những lý do khiến khách hàng tin tưởng lựa chọn theCleaner</p>
            </div>
            <div class="why-us-container">
                <?php foreach ($whyChooseUs as $reason): ?>
                <div class="why-us-card">
                    <i class="<?php echo $reason['icon']; ?>"></i>
                    <h3><?php echo $reason['title']; ?></h3>
                    <p><?php echo $reason['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-container">
                <?php foreach ($statistics as $stat): ?>
                <div class="stat-item">
                    <h3><?php echo $stat['value']; ?></h3>
                    <p><?php echo $stat['label']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Testimonial -->
    <section class="testimonial-preview">
        <div class="container">
            <div class="section-title">
                <h2>Khách Hàng Nói Gì</h2>
                <p>Đánh giá từ khách hàng đã sử dụng dịch vụ của chúng tôi</p>
            </div>
            <div class="featured-testimonial">
                <div class="testimonial">
                    <p class="testimonial-text">"<?php echo $featuredTestimonial['text']; ?>"</p>
                    <div class="client-info">
                        <img src="<?php echo $featuredTestimonial['image']; ?>" alt="<?php echo $featuredTestimonial['name']; ?>">
                        <h4><?php echo $featuredTestimonial['name']; ?></h4>
                        <p><?php echo $featuredTestimonial['location']; ?></p>
                    </div>
                </div>
                <a href="testimonials.php" class="btn btn-outline">Xem Tất Cả Đánh Giá</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Bạn Cần Dịch Vụ Vệ Sinh?</h2>
            <p>Hãy liên hệ với chúng tôi ngay hôm nay để nhận báo giá miễn phí và tư vấn về dịch vụ vệ sinh phù hợp với nhu cầu của bạn.</p>
            <a href="contact.php" class="btn">Liên Hệ Ngay</a>
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
                <ul class="footer-links">
        <!-- Nơi thêm liên kết Administrator -->
        <li>
            <!-- Khi nhấn sẽ sang trang quantri/login.php -->
            <a href="quantri/login.php">Administrator</a>
        </li>
    </ul>

            </div>
            <div class="footer-col">
                <h4>Dịch Vụ</h4>
                <ul class="footer-links">
                    <li><a href="services.php">Vệ sinh nhà ở</a></li>
                    <li><a href="services.php">Vệ sinh văn phòng</a></li>
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
</body>
</html>