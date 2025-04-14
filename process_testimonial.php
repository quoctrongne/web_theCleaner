<?php
/**
 * File này xử lý form gửi đánh giá từ khách hàng
 */
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Kết nối đến database
require_once 'database_connection.php';

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
        $rating = filter_var($_POST['rating'], FILTER_VALIDATE_FLOAT);
        $testimonial = filter_var($_POST['testimonial'], FILTER_SANITIZE_STRING);
        
        // Xác định vị trí
        $location = "Khách hàng tại Việt Nam"; // Giá trị mặc định
        
        // Kiểm tra xem người dùng đã tồn tại chưa
        $user = db_get_row("SELECT id FROM users WHERE email = :email", ['email' => $email]);
        $user_id = null;
        
        if ($user) {
            $user_id = $user['id'];
        }
        
        // Xử lý tệp ảnh nếu có
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $targetDir = "uploads/testimonials/";
            
            // Kiểm tra và tạo thư mục nếu chưa tồn tại
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Tạo tên file duy nhất
            $fileExtension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Kiểm tra loại file
            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['testimonial_success'] = false;
                $_SESSION['testimonial_message'] = "Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif).";
                header("Location: testimonials.php");
                exit();
            }
            $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
            $targetFile = $targetDir . $uniqueName;
        }
        
        // Thêm đánh giá vào database
        $testimonial_data = [
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email,
            'service' => $service,
            'rating' => $rating,
            'testimonial' => $testimonial,
            'photo_path' => $photoPath,
            'location' => $location,
            'status' => 'pending' // Mặc định là chờ phê duyệt
        ];
        
        $testimonial_id = db_insert('testimonials', $testimonial_data);
        
        if ($testimonial_id) {
            // Tạo thông báo thành công
            $_SESSION['testimonial_success'] = true;
            $_SESSION['testimonial_message'] = "Cảm ơn bạn đã chia sẻ đánh giá. Đánh giá của bạn sẽ được hiển thị sau khi xét duyệt.";
            
            // Có thể gửi email thông báo cho admin
            // sendTestimonialNotification($name, $email, $service, $rating, $testimonial);
        } else {
            // Tạo thông báo lỗi
            $_SESSION['testimonial_success'] = false;
            $_SESSION['testimonial_message'] = "Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại sau.";
        }
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
 * Hàm gửi thông báo đánh giá mới cho admin (tùy chọn)
 * Đây là hàm mẫu để triển khai sau này
 */
function sendTestimonialNotification($name, $email, $service, $rating, $testimonial) {
    // Import PHPMailer
    require 'vendor/autoload.php';
    // Lấy email admin từ database configuration
    $admin_email = get_config('admin_email', 'admin@thecleaner.com');
    
    $serviceLabels = [
        'home' => 'Vệ sinh nhà ở',
        'office' => 'Vệ sinh văn phòng'
    ];
    
    $serviceLabel = isset($serviceLabels[$service]) ? $serviceLabels[$service] : $service;
    
    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@gmail.com';
        $mail->Password   = 'your_app_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Thông tin người gửi và người nhận
        $mail->setFrom('your_email@gmail.com', 'theCleaner Website');
        $mail->addAddress($admin_email);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = "Đánh giá mới từ khách hàng - theCleaner";
        
        $mail->Body = "
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
        
        $mail->AltBody = "Đánh giá mới từ {$name} ({$email}). Dịch vụ: {$serviceLabel}. Đánh giá: {$rating}/5. Nội dung: {$testimonial}";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Không thể gửi email thông báo đánh giá: {$mail->ErrorInfo}");
        return false;
    }
}