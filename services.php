<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Dịch Vụ - theCleaner";
$currentPage = "services";

// Mảng dịch vụ chính
$mainServices = [
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

// Dịch vụ vệ sinh nhà ở chi tiết
$homeServiceFeatures = [
    [
        "title" => "Vệ Sinh Phòng Khách",
        "description" => "Hút bụi và lau sàn nhà, lau chùi bàn ghế, tủ kệ, đồ trang trí, vệ sinh cửa sổ, rèm cửa và làm sạch các vật dụng khác."
    ],
    [
        "title" => "Vệ Sinh Phòng Ngủ",
        "description" => "Hút bụi giường, nệm, làm sạch nội thất phòng ngủ, thay ga trải giường, vệ sinh cửa sổ và rèm cửa."
    ],
    [
        "title" => "Vệ Sinh Nhà Bếp",
        "description" => "Làm sạch bề mặt bếp, tủ kệ, thiết bị gia dụng, vệ sinh lò vi sóng, lò nướng, tủ lạnh và các thiết bị khác."
    ],
    [
        "title" => "Vệ Sinh Phòng Tắm",
        "description" => "Làm sạch bồn tắm, bồn rửa, toilet, gương, vách kính, sàn nhà và các vật dụng trong phòng tắm."
    ]
];

// Bảng giá tham khảo nhà ở
$homePricing = [
    ["service" => "Vệ sinh nhà ở", "area" => "Dưới 50m²", "price" => "20.000đ/m²"],
    ["service" => "Vệ sinh nhà ở", "area" => "50m² - 100m²", "price" => "16.000đ/m²"],
    ["service" => "Vệ sinh nhà ở", "area" => "Trên 100m²", "price" => "14.000đ/m²"]
];

// Dịch vụ vệ sinh văn phòng chi tiết
$officeServiceFeatures = [
    [
        "title" => "Vệ Sinh Hàng Ngày",
        "description" => "Lau sàn, hút bụi thảm, vệ sinh bề mặt làm việc, làm sạch khu vực lễ tân, hành lang và khu vực chung."
    ],
    [
        "title" => "Vệ Sinh Khu Vực Làm Việc",
        "description" => "Lau chùi bàn ghế, tủ kệ, thiết bị văn phòng, màn hình máy tính, bàn phím và điện thoại."
    ],
    [
        "title" => "Vệ Sinh Khu Vực Nhà Bếp/Pantry",
        "description" => "Làm sạch bề mặt bếp, tủ kệ, thiết bị, lò vi sóng, tủ lạnh và khu vực ăn uống."
    ],
    [
        "title" => "Vệ Sinh Nhà Vệ Sinh",
        "description" => "Làm sạch bồn rửa, toilet, gương, sàn nhà, bổ sung giấy vệ sinh, xà phòng và các vật dụng cần thiết."
    ]
];

// Bảng giá tham khảo văn phòng
$officePricing = [
    ["service" => "Vệ sinh văn phòng", "area" => "Dưới 100m²", "price" => "25.000đ/m²"],
    ["service" => "Vệ sinh văn phòng", "area" => "100m² - 300m²", "price" => "22.000đ/m²"],
    ["service" => "Vệ sinh văn phòng", "area" => "Trên 300m²", "price" => "20.000đ/m²"],
    ["service" => "Vệ sinh văn phòng", "area" => "Tất cả diện tích", "price" => "18.000đ/m²"]
];

// Quy trình dịch vụ
$serviceProcess = [
    ["number" => 1, "title" => "Tiếp Nhận Yêu Cầu", "description" => "Chúng tôi tiếp nhận thông tin và yêu cầu của khách hàng qua điện thoại, email hoặc website."],
    ["number" => 2, "title" => "Khảo Sát & Báo Giá", "description" => "Nhân viên sẽ khảo sát thực tế và cung cấp báo giá chi tiết, phù hợp với nhu cầu."],
    ["number" => 3, "title" => "Ký Kết Hợp Đồng", "description" => "Xác nhận dịch vụ và ký kết hợp đồng, cam kết chất lượng và thời gian."],
    ["number" => 4, "title" => "Thực Hiện Dịch Vụ", "description" => "Đội ngũ nhân viên chuyên nghiệp thực hiện dịch vụ vệ sinh theo quy trình chuẩn."],
    ["number" => 5, "title" => "Kiểm Tra & Nghiệm Thu", "description" => "Kiểm tra chất lượng dịch vụ và nhận phản hồi từ khách hàng."],
    ["number" => 6, "title" => "Chăm Sóc Sau Dịch Vụ", "description" => "Tiếp tục hỗ trợ và chăm sóc khách hàng sau khi hoàn thành dịch vụ."]
];

// FAQs
$faqs = [
    [
        "question" => "Chi phí dịch vụ vệ sinh được tính như thế nào?",
        "answer" => "Chi phí dịch vụ vệ sinh được tính dựa trên nhiều yếu tố như diện tích, loại hình dịch vụ, mức độ bẩn, tần suất vệ sinh và các yêu cầu đặc biệt. Chúng tôi sẽ khảo sát và cung cấp báo giá chi tiết, minh bạch."
    ],
    [
        "question" => "Các hóa chất vệ sinh có an toàn không?",
        "answer" => "Chúng tôi sử dụng các sản phẩm và hóa chất vệ sinh thân thiện với môi trường, an toàn cho sức khỏe con người và vật nuôi. Đối với khách hàng có yêu cầu đặc biệt, chúng tôi có thể sử dụng sản phẩm vệ sinh theo yêu cầu."
    ],
    [
        "question" => "Thời gian thực hiện dịch vụ vệ sinh mất bao lâu?",
        "answer" => "Thời gian thực hiện dịch vụ vệ sinh phụ thuộc vào diện tích, loại hình dịch vụ và mức độ bẩn. Thông thường, dịch vụ vệ sinh nhà ở có diện tích trung bình sẽ mất khoảng 3-5 giờ, vệ sinh văn phòng có thể mất từ 2-8 giờ tùy quy mô."
    ],
    [
        "question" => "Có cần chuẩn bị gì trước khi dịch vụ vệ sinh đến không?",
        "answer" => "Để tối ưu hóa hiệu quả vệ sinh, khách hàng nên dọn dẹp đồ đạc cá nhân, tài liệu quan trọng và các vật dụng có giá trị. Điều này giúp đội ngũ vệ sinh tập trung vào công việc chính và tránh làm xáo trộn đồ đạc cá nhân của khách hàng."
    ],
    [
        "question" => "Có bảo hành sau khi vệ sinh không?",
        "answer" => "Có, chúng tôi cam kết chất lượng dịch vụ và có chính sách bảo hành. Nếu khách hàng không hài lòng với kết quả vệ sinh, chúng tôi sẽ quay lại thực hiện lại mà không tính thêm phí trong vòng 24-48 giờ sau khi nhận được phản hồi."
    ]
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
    <link rel="stylesheet" href="styles/services.css">
    <style>
        /* CSS để ẩn thanh màu xanh gây ra vấn đề */
        .service-tabs, 
        div[id^="service-tabs"],
        .tab-container,
        .blue-bar,
        .pricing-header,
        .service-nav {
            display: none !important;
        }
        
        /* Đảm bảo không có khoảng trống thừa nếu thanh được ẩn */
        body {
            padding-top: 0 !important;
        }
        
        /* Thêm padding cho trang để tránh bị che bởi header */
        .page-banner {
            padding-top: 150px !important;
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
            <h1>Dịch Vụ Của Chúng Tôi</h1>
            <p>Cung cấp dịch vụ vệ sinh chuyên nghiệp cho nhà ở và văn phòng</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Các Dịch Vụ Chính</h2>
                <p>Chúng tôi cung cấp dịch vụ vệ sinh chuyên nghiệp, đáp ứng mọi nhu cầu của khách hàng.</p>
            </div>
            <div class="services-container">
                <?php foreach ($mainServices as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $service['title']; ?></h3>
                    <p><?php echo $service['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Home Cleaning Service -->
    <section class="service-detail">
        <div class="container">
            <div class="section-title">
                <h2>Dịch Vụ Vệ Sinh Nhà Ở</h2>
                <p>Giải pháp vệ sinh toàn diện cho không gian sống</p>
            </div>
            
            <div class="service-description">
                <p>Dịch vụ vệ sinh nhà ở của theCleaner mang đến giải pháp toàn diện giúp không gian sống của bạn luôn sạch sẽ, thoáng mát và thoải mái. Chúng tôi hiểu rằng ngôi nhà là nơi để bạn thư giãn và tái tạo năng lượng, vì vậy, chúng tôi cam kết mang đến dịch vụ chất lượng cao nhất.</p>
            </div>
            
            <div class="service-features">
                <h3>Dịch Vụ Bao Gồm</h3>
                <div class="features-grid">
                    <?php foreach ($homeServiceFeatures as $feature): ?>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $feature['title']; ?></h4>
                            <p><?php echo $feature['description']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="service-pricing">
                <h3>Bảng Giá Tham Khảo</h3>
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Gói Dịch Vụ</th>
                            <th>Diện Tích</th>
                            <th>Giá Tham Khảo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($homePricing as $price): ?>
                        <tr>
                            <td><?php echo $price['service']; ?></td>
                            <td><?php echo $price['area']; ?></td>
                            <td><?php echo $price['price']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="pricing-note">* Giá trên chỉ mang tính chất tham khảo. Vui lòng liên hệ để nhận báo giá chính xác.</p>
            </div>
            
            <div class="service-cta">
                <a href="booking.php" class="btn btn-primary">Đặt Lịch Ngay</a>
            </div>
        </div>
    </section>
    
    <!-- Office Cleaning Service -->
    <section class="service-detail bg-light">
        <div class="container">
            <div class="section-title">
                <h2>Dịch Vụ Vệ Sinh Văn Phòng</h2>
                <p>Giải pháp vệ sinh chuyên nghiệp cho không gian làm việc</p>
            </div>
            
            <div class="service-description">
                <p>Dịch vụ vệ sinh văn phòng chuyên nghiệp của theCleaner giúp duy trì môi trường làm việc sạch sẽ, thoáng mát và chuyên nghiệp. Một không gian làm việc sạch sẽ không chỉ tạo ấn tượng tốt với đối tác, khách hàng mà còn nâng cao năng suất làm việc của nhân viên.</p>
            </div>
            
            <div class="service-features">
                <h3>Dịch Vụ Bao Gồm</h3>
                <div class="features-grid">
                    <?php foreach ($officeServiceFeatures as $feature): ?>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4><?php echo $feature['title']; ?></h4>
                            <p><?php echo $feature['description']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="service-pricing">
                <h3>Bảng Giá Tham Khảo</h3>
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Gói Dịch Vụ</th>
                            <th>Diện Tích</th>
                            <th>Giá Tham Khảo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($officePricing as $price): ?>
                        <tr>
                            <td><?php echo $price['service']; ?></td>
                            <td><?php echo $price['area']; ?></td>
                            <td><?php echo $price['price']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="pricing-note">* Giá trên chỉ mang tính chất tham khảo. Vui lòng liên hệ để nhận báo giá chính xác.</p>
            </div>
            
            <div class="service-cta">
                <a href="booking.php" class="btn btn-primary">Đặt Lịch Ngay</a>
            </div>
        </div>
    </section>

    <!-- Service Process -->
    <section class="process">
        <div class="container">
            <div class="section-title">
                <h2>Quy Trình Dịch Vụ</h2>
                <p>Các bước thực hiện dịch vụ vệ sinh của chúng tôi</p>
            </div>
            <div class="process-steps">
                <?php foreach ($serviceProcess as $step): ?>
                <div class="step">
                    <div class="step-number"><?php echo $step['number']; ?></div>
                    <h3><?php echo $step['title']; ?></h3>
                    <p><?php echo $step['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="faqs">
        <div class="container">
            <div class="section-title">
                <h2>Câu Hỏi Thường Gặp</h2>
                <p>Giải đáp những thắc mắc phổ biến về dịch vụ vệ sinh</p>
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

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Sẵn Sàng Trải Nghiệm Dịch Vụ Vệ Sinh Chuyên Nghiệp?</h2>
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
                    
                    <!-- Thêm nút đăng nhập -->
                    <a href="quantri/login.php" class="btn btn-primary mt-2">Đăng Nhập</a> <!-- Nút đăng nhập -->
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
        });
    </script>
</body>
</html>