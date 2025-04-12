<?php
// Thiết lập các thông tin cơ bản cho trang
$pageTitle = "Đánh Giá - theCleaner";
$currentPage = "testimonials";

// Thống kê đánh giá
$testimonialStats = [
    ["value" => "4.9/5", "label" => "Điểm đánh giá trung bình"],
    ["value" => "98%", "label" => "Khách hàng hài lòng"],
    ["value" => "1200+", "label" => "Đánh giá tích cực"],
    ["value" => "85%", "label" => "Khách hàng quay lại"]
];

// Phân tích đánh giá
$ratingBreakdown = [
    ["stars" => "5 Sao", "percentage" => 85],
    ["stars" => "4 Sao", "percentage" => 10],
    ["stars" => "3 Sao", "percentage" => 3],
    ["stars" => "2 Sao", "percentage" => 1],
    ["stars" => "1 Sao", "percentage" => 1]
];

// Đánh giá nổi bật (Từ bảng testimonials)
$featuredTestimonials = [
    [
        "image" => "images/client1.jpg",
        "name" => "Nguyễn Văn A",
        "location" => "Khách hàng tại Hà Nội",
        "rating" => 5,
        "text" => "Tôi rất hài lòng với dịch vụ vệ sinh của theCleaner. Nhân viên chuyên nghiệp, làm việc tỉ mỉ và hiệu quả. Ngôi nhà của tôi giờ đây sạch sẽ và thoáng mát hơn bao giờ hết. Chắc chắn tôi sẽ tiếp tục sử dụng dịch vụ này.",
        "service" => "Vệ sinh nhà ở"
    ],
    [
        "image" => "images/client2.jpg",
        "name" => "Trần Thị B",
        "location" => "Giám đốc công ty XYZ",
        "rating" => 5,
        "text" => "Dịch vụ vệ sinh văn phòng của theCleaner thực sự đáng tiền. Đội ngũ nhân viên làm việc nhanh chóng, chuyên nghiệp và hiệu quả. Không gian văn phòng sạch sẽ giúp nhân viên của tôi làm việc hiệu quả hơn. Cảm ơn theCleaner!",
        "service" => "Vệ sinh văn phòng"
    ],
    [
        "image" => "images/client3.jpg",
        "name" => "Lê Văn C",
        "location" => "Khách hàng tại TP.HCM",
        "rating" => 5,
        "text" => "Tôi đã sử dụng dịch vụ vệ sinh sofa của theCleaner và kết quả thật đáng kinh ngạc. Bộ sofa đã bị bẩn và có mùi hôi, nhưng sau khi được vệ sinh, nó trông như mới. Dịch vụ chuyên nghiệp và giá cả phải chăng.",
        "service" => "Vệ sinh nhà ở"
    ],
    [
        "image" => "images/client4.jpg",
        "name" => "Phạm Thị D",
        "location" => "Khách hàng tại Hà Nội",
        "rating" => 4.5,
        "text" => "Dịch vụ vệ sinh sau xây dựng của theCleaner thực sự ấn tượng. Sau khi hoàn thành việc sửa chữa nhà, mọi thứ bụi bẩn và lộn xộn. Đội ngũ theCleaner đã biến ngôi nhà trở nên sạch sẽ, sẵn sàng để ở trong thời gian rất ngắn.",
        "service" => "Vệ sinh nhà ở"
    ],
    [
        "image" => "images/client5.jpg",
        "name" => "Trương Văn E",
        "location" => "Quản lý nhà hàng ABC",
        "rating" => 5,
        "text" => "Chúng tôi đã sử dụng dịch vụ khử trùng của theCleaner cho nhà hàng. Họ làm việc rất chuyên nghiệp, sử dụng các sản phẩm an toàn và hiệu quả. Khách hàng của chúng tôi cảm thấy an tâm hơn khi biết nhà hàng được khử trùng thường xuyên.",
        "service" => "Vệ sinh văn phòng"
    ],
    [
        "image" => "images/client6.jpg",
        "name" => "Hoàng Thị F",
        "location" => "Khách hàng tại Đà Nẵng",
        "rating" => 5,
        "text" => "Dịch vụ vệ sinh kính cửa sổ của theCleaner thực sự xuất sắc. Tôi sống trong một căn hộ cao cấp với nhiều cửa kính lớn, và họ đã làm sạch tất cả một cách an toàn và hiệu quả. Cửa kính giờ đây trong suốt và sáng bóng.",
        "service" => "Vệ sinh nhà ở"
    ]
];

// Ảnh trước và sau
$beforeAfterItems = [
    [
        "before" => "images/before1.jpg",
        "after" => "images/after1.jpg",
        "title" => "Vệ Sinh Sofa"
    ],
    [
        "before" => "images/before2.jpg",
        "after" => "images/after2.jpg",
        "title" => "Vệ Sinh Phòng Tắm"
    ],
    [
        "before" => "images/before3.jpg",
        "after" => "images/after3.jpg",
        "title" => "Vệ Sinh Thảm"
    ]
];

// Danh sách dịch vụ cho form gửi đánh giá
$serviceOptions = [
    ["value" => "home", "label" => "Vệ sinh nhà ở"],
    ["value" => "office", "label" => "Vệ sinh văn phòng"]
];

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Hàm tạo sao đánh giá
function generateStarRating($rating) {
    $output = '';
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5;
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            $output .= '<i class="fas fa-star"></i>';
        } elseif ($i == $fullStars + 1 && $halfStar) {
            $output .= '<i class="fas fa-star-half-alt"></i>';
        } else {
            $output .= '<i class="far fa-star"></i>';
        }
    }
    
    return $output;
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
    <link rel="stylesheet" href="styles/testimonials.css">
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
            <h1>Đánh Giá Từ Khách Hàng</h1>
            <p>Những trải nghiệm thực tế từ khách hàng đã sử dụng dịch vụ</p>
        </div>
    </section>

    <!-- Testimonial Highlights -->
    <section class="testimonial-highlights">
        <div class="container">
            <div class="testimonial-stats">
                <?php foreach ($testimonialStats as $stat): ?>
                <div class="stat">
                    <h3><?php echo $stat['value']; ?></h3>
                    <p><?php echo $stat['label']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="rating-breakdown">
                <h3>Phân Tích Đánh Giá</h3>
                <?php foreach ($ratingBreakdown as $rating): ?>
                <div class="rating-bar">
                    <span class="rating-label"><?php echo $rating['stars']; ?></span>
                    <div class="progress-bar">
                        <div class="progress" style="width: <?php echo $rating['percentage']; ?>%"></div>
                    </div>
                    <span class="rating-percent"><?php echo $rating['percentage']; ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Testimonials -->
    <section class="featured-testimonials">
        <div class="container">
            <div class="section-title">
                <h2>Đánh Giá Nổi Bật</h2>
                <p>Những phản hồi chân thực từ khách hàng</p>
            </div>
            <div class="testimonials-grid">
                <?php foreach ($featuredTestimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <img src="<?php echo $testimonial['image']; ?>" alt="<?php echo $testimonial['name']; ?>">
                        <div>
                            <h3><?php echo $testimonial['name']; ?></h3>
                            <p><?php echo $testimonial['location']; ?></p>
                            <div class="rating">
                                <?php echo generateStarRating($testimonial['rating']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-content">
                        <p>"<?php echo $testimonial['text']; ?>"</p>
                    </div>
                    <div class="testimonial-service">
                        <span>Dịch vụ: <?php echo $testimonial['service']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Before & After -->
    <section class="before-after">
        <div class="container">
            <div class="section-title">
                <h2>Trước & Sau</h2>
                <p>Hiệu quả thực tế của dịch vụ vệ sinh</p>
            </div>
            <div class="before-after-grid">
                <?php foreach ($beforeAfterItems as $item): ?>
                <div class="before-after-item">
                    <div class="before-image">
                        <img src="<?php echo $item['before']; ?>" alt="Trước khi vệ sinh">
                        <span class="label">Trước</span>
                    </div>
                    <div class="after-image">
                        <img src="<?php echo $item['after']; ?>" alt="Sau khi vệ sinh">
                        <span class="label">Sau</span>
                    </div>
                    <div class="before-after-caption">
                        <h3><?php echo $item['title']; ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Submit Testimonial -->
    <section class="submit-testimonial">
        <div class="container">
            <div class="section-title">
                <h2>Chia Sẻ Trải Nghiệm Của Bạn</h2>
                <p>Hãy cho chúng tôi biết trải nghiệm của bạn với dịch vụ của theCleaner</p>
            </div>
            <div class="testimonial-form-container">
                <form id="testimonialForm" class="testimonial-form" method="post" action="process_testimonial.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <select name="service" class="form-control" required>
                            <option value="" disabled selected>Chọn dịch vụ đã sử dụng</option>
                            <?php foreach ($serviceOptions as $option): ?>
                            <option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="rating-select">
                            <p>Đánh giá của bạn:</p>
                            <div class="star-rating">
                                <i class="far fa-star" data-rating="1"></i>
                                <i class="far fa-star" data-rating="2"></i>
                                <i class="far fa-star" data-rating="3"></i>
                                <i class="far fa-star" data-rating="4"></i>
                                <i class="far fa-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="testimonial" class="form-control" placeholder="Chia sẻ trải nghiệm của bạn" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label class="file-upload">
                            <input type="file" name="photo" accept="image/*">
                            <span><i class="fas fa-upload"></i> Tải lên hình ảnh (nếu có)</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi Đánh Giá</button>
                </form>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Hãy Trải Nghiệm Dịch Vụ Của Chúng Tôi</h2>
            <p>Đừng chỉ đọc về chúng tôi - hãy trải nghiệm dịch vụ vệ sinh chuyên nghiệp và chia sẻ câu chuyện của bạn.</p>
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
            // Xử lý Star Rating
            const stars = document.querySelectorAll('.star-rating i');
            const ratingInput = document.getElementById('ratingValue');
            
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const rating = parseInt(star.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    
                    stars.forEach(s => {
                        const sRating = parseInt(s.getAttribute('data-rating'));
                        
                        if (sRating <= rating) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });
            
            // Form Submission
            const testimonialForm = document.getElementById('testimonialForm');
            
            if (testimonialForm) {
                testimonialForm.addEventListener('submit', function(e) {
                    if (!ratingInput.value) {
                        e.preventDefault();
                        alert('Vui lòng chọn đánh giá sao cho dịch vụ.');
                    }
                });
            }

            // Thêm hiệu ứng chạy số cho các thống kê
            // Hàm chạy số từ 0 đến giá trị đích
            function animateCounter(element, targetValue, duration) {
                // Xác định giá trị đích và định dạng
                let target = targetValue;
                let isPercentage = false;
                let hasPlus = false;
                let isDecimal = false;
                
                // Kiểm tra chuỗi có dấu % không
                if (typeof targetValue === 'string' && targetValue.includes('%')) {
                    target = parseFloat(targetValue);
                    isPercentage = true;
                }
                
                // Kiểm tra chuỗi có dấu + không
                if (typeof targetValue === 'string' && targetValue.includes('+')) {
                    target = parseFloat(targetValue);
                    hasPlus = true;
                }
                
                // Kiểm tra có phải dạng thập phân (như 4.9/5)
                if (typeof targetValue === 'string' && targetValue.includes('.')) {
                    isDecimal = true;
                    // Nếu là format x.x/y
                    if (targetValue.includes('/')) {
                        let parts = targetValue.split('/');
                        target = parseFloat(parts[0]);
                    } else {
                        target = parseFloat(targetValue);
                    }
                }
                
                // Chuyển đổi thành số
                if (typeof target !== 'number') {
                    target = parseInt(target);
                }
                
                // Đặt giá trị ban đầu là 0
                let startValue = 0;
                let startTime = null;
                
                // Hàm cập nhật số
                function updateNumber(timestamp) {
                    if (!startTime) startTime = timestamp;
                    
                    // Tính toán thời gian đã trôi qua
                    const progress = Math.min((timestamp - startTime) / duration, 1);
                    
                    // Tính giá trị hiện tại dựa trên thời gian
                    let currentValue = startValue + (target - startValue) * progress;
                    
                    // Định dạng giá trị hiển thị
                    let displayValue;
                    
                    if (isDecimal) {
                        // Làm tròn số thập phân đến 1 chữ số
                        currentValue = Math.round(currentValue * 10) / 10;
                        
                        // Nếu là format x.x/y
                        if (typeof targetValue === 'string' && targetValue.includes('/')) {
                            let parts = targetValue.split('/');
                            displayValue = currentValue.toFixed(1) + '/' + parts[1];
                        } else {
                            displayValue = currentValue.toFixed(1);
                        }
                    } else {
                        // Làm tròn số nguyên
                        currentValue = Math.floor(currentValue);
                        displayValue = currentValue.toString();
                    }
                    
                    // Thêm dấu % nếu là phần trăm
                    if (isPercentage) {
                        displayValue += '%';
                    }
                    
                    // Thêm dấu + nếu cần
                    if (hasPlus) {
                        displayValue += '+';
                    }
                    
                    // Cập nhật giá trị cho phần tử
                    element.innerHTML = displayValue;
                    
                    // Tiếp tục animation nếu chưa hoàn thành
                    if (progress < 1) {
                        requestAnimationFrame(updateNumber);
                    }
                }
                
                // Bắt đầu animation
                requestAnimationFrame(updateNumber);
            }
            
            // Bắt đầu animation khi phần tử hiển thị trong viewport
            function handleIntersection(entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Lấy giá trị đích từ phần tử
                        const targetValue = entry.target.getAttribute('data-target');
                        // Bắt đầu animation với thời gian 2 giây
                        animateCounter(entry.target, targetValue, 2000);
                        // Ngừng theo dõi phần tử đã được xử lý
                        observer.unobserve(entry.target);
                    }
                });
            }
            
            // Khởi tạo Intersection Observer
            const options = {
                threshold: 0.1
            };
            
            const observer = new IntersectionObserver(handleIntersection, options);
            
            // Chọn tất cả phần tử cần animation
            const statElements = document.querySelectorAll('.stat h3');
            
            // Thêm data-target và bắt đầu theo dõi
            statElements.forEach(element => {
                const originalValue = element.textContent;
                element.setAttribute('data-target', originalValue);
                element.textContent = '0';
                observer.observe(element);
            });
        });
    </script>
</body>
</html>