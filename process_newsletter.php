<?php
/**
 * File này xử lý form đăng ký nhận bản tin
 */

// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra phương thức gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate email
    if (!empty($_POST['subscribe_email']) && filter_var($_POST['subscribe_email'], FILTER_VALIDATE_EMAIL)) {
        
        $email = filter_var($_POST['subscribe_email'], FILTER_SANITIZE_EMAIL);
        
        // Trong ứng dụng thực tế, bạn sẽ lưu email vào cơ sở dữ liệu
        // Ví dụ: saveSubscriberToDatabase($email);
        
        // Có thể gửi email xác nhận đăng ký
        // Ví dụ: sendConfirmationEmail($email);
        
        // Tạo thông báo thành công
        $_SESSION['newsletter_success'] = true;
        $_SESSION['newsletter_message'] = "Cảm ơn bạn đã đăng ký nhận bản tin. Chúng tôi sẽ gửi thông tin cập nhật và khuyến mãi mới nhất đến email của bạn.";
    } else {
        // Tạo thông báo lỗi
        $_SESSION['newsletter_success'] = false;
        $_SESSION['newsletter_message'] = "Có lỗi xảy ra. Vui lòng kiểm tra lại địa chỉ email của bạn.";
    }
    
    // Xác định trang trước đó để chuyển hướng về
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    
    // Chuyển hướng về trang trước đó
    header("Location: $referer");
    exit;
} else {
    // Nếu không phải phương thức POST, chuyển hướng về trang chủ
    header("Location: index.php");
    exit;
}

/**
 * Hàm lưu email vào cơ sở dữ liệu (giả định)
 */
function saveSubscriberToDatabase($email) {
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
        
        // Kiểm tra email đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM subscribers WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if ($stmt->rowCount() == 0) {
            // Thêm email mới vào cơ sở dữ liệu
            $stmt = $pdo->prepare("INSERT INTO subscribers (email, subscribe_date) VALUES (:email, NOW())");
            $stmt->execute(['email' => $email]);
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // Xử lý lỗi cơ sở dữ liệu
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm gửi email xác nhận (giả định)
 */
function sendConfirmationEmail($email) {
    $subject = "Đăng ký nhận bản tin thành công - theCleaner";
    
    $message = "
    <html>
    <head>
        <title>Đăng ký nhận bản tin thành công</title>
    </head>
    <body>
        <h2>Cảm ơn bạn đã đăng ký nhận bản tin từ theCleaner!</h2>
        <p>Chúng tôi sẽ gửi cho bạn các thông tin cập nhật, khuyến mãi và dịch vụ mới nhất.</p>
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email <a href='mailto:info@thecleaner.com'>info@thecleaner.com</a> hoặc gọi số điện thoại <a href='tel:+84123456789'>+84 123 456 789</a>.</p>
        <p>Trân trọng,<br>Đội ngũ theCleaner</p>
    </body>
    </html>
    ";
    
    // Các headers cần thiết để gửi email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: theCleaner <info@thecleaner.com>" . "\r\n";
    
    // Gửi email
    return mail($email, $subject, $message, $headers);
}
?>