<?php
/**
 * File này xử lý form gửi đánh giá từ khách hàng
 */

// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra phương thức gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate các trường bắt buộc
    if (
        !empty($_POST['name']) && 
        !empty($_POST['email']) &&
        !empty($_POST['service']) &&
        !empty($_POST['rating']) &&
        !empty($_POST['testimonial'])
    ) {
        // Lọc dữ liệu đầu vào
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $service = filter_var($_POST['service'], FILTER_SANITIZE_STRING);
        $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);
        $testimonial = filter_var($_POST['testimonial'], FILTER_SANITIZE_STRING);
        
        // Xử lý tệp ảnh nếu có
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $targetDir = "uploads/testimonials/";
            
            // Kiểm tra và tạo thư mục nếu chưa tồn tại
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Tạo tên file duy nhất
            $fileExtension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
            $targetFile = $targetDir . $uniqueName;
            
            // Danh sách các loại file ảnh được phép
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            // Kiểm tra loại file
            if (in_array($_FILES["photo"]["type"], $allowedTypes)) {
                // Di chuyển file tạm thời đến thư mục đích
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                    $photoPath = $targetFile;
                }
            }
        }
        
        // Trong ứng dụng thực tế, bạn sẽ lưu đánh giá vào cơ sở dữ liệu
        // Ví dụ: saveTestimonialToDatabase($name, $email, $service, $rating, $testimonial, $photoPath);
        
        // Có thể gửi email thông báo cho admin
        // Ví dụ: sendTestimonialNotification($name, $email, $service, $rating, $testimonial);
        
        // Tạo thông báo thành công
        $_SESSION['testimonial_success'] = true;
        $_SESSION['testimonial_message'] = "Cảm ơn bạn đã chia sẻ đánh giá. Đánh giá của bạn sẽ được hiển thị sau khi xét duyệt.";
    } else {
        // Tạo thông báo lỗi
        $_SESSION['testimonial_success'] = false;
        $_SESSION['testimonial_message'] = "Có lỗi xảy ra. Vui lòng kiểm tra lại thông tin của bạn.";
    }
    
    // Chuyển hướng về trang đánh giá
    header("Location: testimonials.php");
    exit;
} else {
    // Nếu không phải phương thức POST, chuyển hướng về trang chủ
    header("Location: index.php");
    exit;
}

/**
 * Hàm lưu đánh giá vào cơ sở dữ liệu (giả định)
 */
function saveTestimonialToDatabase($name, $email, $service, $rating, $testimonial, $photoPath = null) {
    // Kết nối đến cơ sở dữ liệu
    try {
        $host = 'localhost';
        $db = 'thecleaner';
        $user = 'username';
        $pass = 'password';
        
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Thêm đánh giá mới vào cơ sở dữ liệu
        $stmt = $pdo->prepare("
            INSERT INTO testimonials (name, email, service, rating, testimonial, photo_path, status, created_at) 
            VALUES (:name, :email, :service, :rating, :testimonial, :photo_path, 'pending', NOW())
        ");
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'service' => $service,
            'rating' => $rating,
            'testimonial' => $testimonial,
            'photo_path' => $photoPath
        ]);
        
        return true;
    } catch (PDOException $e) {
        // Xử lý lỗi cơ sở dữ liệu
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm gửi thông báo đánh giá mới cho admin (giả định)
 */
function sendTestimonialNotification($name, $email, $service, $rating, $testimonial) {
    $to = "admin@thecleaner.com";
    $subject = "Đánh giá mới từ khách hàng - theCleaner";
    
    $serviceLabels = [
        'home' => 'Vệ sinh nhà ở',
        'office' => 'Vệ sinh văn phòng'
    ];
    
    $serviceLabel = isset($serviceLabels[$service]) ? $serviceLabels[$service] : $service;
    
    $message = "
    <html>
    <head>
        <title>Đánh giá mới từ khách hàng</title>
    </head>
    <body>
        <h2>Đánh giá mới đã được gửi</h2>
        <p><strong>Người gửi:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Dịch vụ đã sử dụng:</strong> {$serviceLabel}</p>
        <p><strong>Đánh giá:</strong> {$rating}/5</p>
        <p><strong>Nội dung đánh giá:</strong></p>
        <p>{$testimonial}</p>
        <p><a href='https://thecleaner.com/admin/testimonials.php'>Đăng nhập vào trang quản trị</a> để xem và phê duyệt đánh giá này.</p>
    </body>
    </html>
    ";
    
    // Các headers cần thiết để gửi email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: theCleaner Website <no-reply@thecleaner.com>" . "\r\n";
    
    // Gửi email
    return mail($to, $subject, $message, $headers);
}
?>