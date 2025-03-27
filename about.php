<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Về Chúng Tôi - theCleaner";
$currentPage = "about";

// Các tính năng chính
$mainFeatures = [
    "Nhân viên chuyên nghiệp, được đào tạo bài bản",
    "Sử dụng các sản phẩm thân thiện với môi trường",
    "Trang thiết bị, máy móc hiện đại",
    "Giá cả hợp lý, minh bạch",
    "Cam kết hoàn tiền nếu không hài lòng"
];

// Giá trị cốt lõi
$coreValues = [
    [
        "icon" => "fas fa-hand-sparkles",
        "title" => "Chất Lượng",
        "description" => "Cam kết mang đến dịch vụ chất lượng cao nhất, không chỉ đáp ứng mà còn vượt trên sự mong đợi của khách hàng."
    ],
    [
        "icon" => "fas fa-user-tie",
        "title" => "Chuyên Nghiệp",
        "description" => "Duy trì sự chuyên nghiệp trong mọi khía cạnh từ giao tiếp, thái độ đến quy trình thực hiện dịch vụ."
    ],
    [
        "icon" => "fas fa-seedling",
        "title" => "Bền Vững",
        "description" => "Ưu tiên sử dụng các sản phẩm, phương pháp thân thiện với môi trường, đảm bảo sự bền vững cho tương lai."
    ],
    [
        "icon" => "fas fa-handshake",
        "title" => "Tin Cậy",
        "description" => "Xây dựng niềm tin với khách hàng thông qua sự minh bạch, trung thực và đáng tin cậy trong mọi hoạt động."
    ]
];

// Đội ngũ lãnh đạo
$teamMembers = [
    [
        "image" => "images/team1.jpg",
        "name" => "Nguyễn Văn A",
        "position" => "Giám Đốc Điều Hành",
        "bio" => "Với hơn 15 năm kinh nghiệm trong ngành dịch vụ vệ sinh, anh A đã xây dựng và phát triển theCleaner từ một doanh nghiệp nhỏ thành công ty hàng đầu trong lĩnh vực."
    ],
    [
        "image" => "images/team2.jpg",
        "name" => "Trần Thị B",
        "position" => "Giám Đốc Vận Hành",
        "bio" => "Chị B quản lý đội ngũ và quy trình vận hành, đảm bảo chất lượng dịch vụ luôn ở mức cao nhất. Với kinh nghiệm quản lý hơn 10 năm, chị đã xây dựng quy trình vận hành hiệu quả."
    ],
    [
        "image" => "images/team3.jpg",
        "name" => "Lê Văn C",
        "position" => "Trưởng Phòng Chăm Sóc Khách Hàng",
        "bio" => "Anh C chịu trách nhiệm đảm bảo sự hài lòng của khách hàng, xử lý phản hồi và không ngừng cải thiện chất lượng dịch vụ dựa trên ý kiến của khách hàng."
    ]
];

// Thành tựu
$achievements = [
    [
        "icon" => "fas fa-trophy",
        "title" => "Doanh Nghiệp Dịch Vụ Xuất Sắc 2022",
        "description" => "Giải thưởng từ Hiệp hội Doanh nghiệp Dịch vụ Việt Nam, ghi nhận chất lượng dịch vụ xuất sắc."
    ],
    [
        "icon" => "fas fa-certificate",
        "title" => "Chứng Nhận ISO 9001:2015",
        "description" => "Chứng nhận hệ thống quản lý chất lượng đạt tiêu chuẩn quốc tế, đảm bảo quy trình dịch vụ chuyên nghiệp."
    ],
    [
        "icon" => "fas fa-award",
        "title" => "Top 10 Doanh Nghiệp Tiêu Biểu 2023",
        "description" => "Được bình chọn là một trong 10 doanh nghiệp tiêu biểu trong lĩnh vực dịch vụ vệ sinh tại Việt Nam."
    ],
    [
        "icon" => "fas fa-leaf",
        "title" => "Chứng Nhận Doanh Nghiệp Xanh",
        "description" => "Ghi nhận nỗ lực sử dụng các sản phẩm, phương pháp thân thiện với môi trường trong dịch vụ vệ sinh."
    ]
];

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Năm thành lập công ty
$foundingYear = 2015;
$yearsInBusiness = $currentYear - $foundingYear;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/about.css">
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
            <h1>Về Chúng Tôi</h1>
            <p>Tìm hiểu thêm về theCleaner và câu chuyện của chúng tôi</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-main">
        <div class="container">
            <div class="about-container">
                <div class="about-content">
                    <h2>Câu Chuyện Của Chúng Tôi</h2>
                    <p>theCleaner được thành lập vào năm <?php echo $foundingYear; ?> với sứ mệnh mang đến dịch vụ vệ sinh chuyên nghiệp, chất lượng cao cho khách hàng cá nhân và doanh nghiệp.</p>
                    <p>Từ một đội ngũ nhỏ chỉ 5 thành viên, đến nay chúng tôi đã phát triển thành công ty vệ sinh hàng đầu với hơn 50 nhân viên chuyên nghiệp, phục vụ hơn 5000 khách hàng trong suốt hơn <?php echo $yearsInBusiness; ?> năm qua.</p>
                    <p>Chúng tôi tự hào về đội ngũ nhân viên được đào tạo bài bản, trang thiết bị hiện đại và quy trình làm việc chuyên nghiệp. Cam kết mang đến cho khách hàng dịch vụ chất lượng cao với giá cả hợp lý.</p>
                    <div class="about-features">
                        <?php foreach ($mainFeatures as $feature): ?>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <p><?php echo $feature; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="about-image">
                    <img src="images/about-main.jpg" alt="Về chúng tôi">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision">
        <div class="container">
            <div class="mission-vision-container">
                <div class="mission-box">
                    <div class="icon-box">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Sứ Mệnh</h3>
                    <p>Mang đến không gian sống và làm việc sạch sẽ, an toàn, thân thiện với môi trường thông qua các dịch vụ vệ sinh chuyên nghiệp, góp phần nâng cao chất lượng cuộc sống và hiệu quả công việc.</p>
                </div>
                <div class="vision-box">
                    <div class="icon-box">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Tầm Nhìn</h3>
                    <p>Trở thành công ty hàng đầu trong lĩnh vực dịch vụ vệ sinh chuyên nghiệp tại Việt Nam, được khách hàng tin tưởng lựa chọn và là đối tác đáng tin cậy của các cá nhân, gia đình và doanh nghiệp.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="values">
        <div class="container">
            <div class="section-title">
                <h2>Giá Trị Cốt Lõi</h2>
                <p>Những giá trị định hướng mọi hoạt động của chúng tôi</p>
            </div>
            <div class="values-container">
                <?php foreach ($coreValues as $value): ?>
                <div class="value-item">
                    <div class="value-icon">
                        <i class="<?php echo $value['icon']; ?>"></i>
                    </div>
                    <h3><?php echo $value['title']; ?></h3>
                    <p><?php echo $value['description']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team">
        <div class="container">
            <div class="section-title">
                <h2>Đội Ngũ Của Chúng Tôi</h2>
                <p>Những con người tạo nên sự khác biệt cho theCleaner</p>
            </div>
            <div class="team-container">
                <?php foreach ($teamMembers as $member): ?>
                <div class="team-member">
                    <div class="member-img">
                        <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>">
                    </div>
                    <div class="member-info">
                        <h3><?php echo $member['name']; ?></h3>
                        <p class="position"><?php echo $member['position']; ?></p>
                        <p class="bio"><?php echo $member['bio']; ?></p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Achievements -->
    <section class="achievements">
        <div class="container">
            <div class="section-title">
                <h2>Thành Tựu & Chứng Nhận</h2>
                <p>Những dấu mốc quan trọng và chứng nhận chất lượng của chúng tôi</p>
            </div>
            <div class="achievements-container">
                <?php foreach ($achievements as $achievement): ?>
                <div class="achievement">
                    <div class="achievement-icon">
                        <i class="<?php echo $achievement['icon']; ?>"></i>
                    </div>
                    <div class="achievement-content">
                        <h3><?php echo $achievement['title']; ?></h3>
                        <p><?php echo $achievement['description']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Partners -->
    <section class="partners">
        <div class="container">
            <div class="section-title">
                <h2>Đối Tác Của Chúng Tôi</h2>
                <p>Những doanh nghiệp tin tưởng và lựa chọn theCleaner</p>
            </div>
            <div class="partners-logo">
                <?php
                // Hiển thị logo đối tác
                for ($i = 1; $i <= 6; $i++):
                ?>
                <img src="images/partner<?php echo $i; ?>.png" alt="Đối tác <?php echo $i; ?>">
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Hãy Trở Thành Đối Tác Của Chúng Tôi</h2>
            <p>Chúng tôi luôn sẵn sàng hợp tác với các doanh nghiệp và cá nhân để mang đến những giải pháp vệ sinh tốt nhất.</p>
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
</body>
</html>