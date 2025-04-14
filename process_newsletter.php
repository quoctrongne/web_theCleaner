<?php
/**
 * File này xử lý form đăng ký nhận bản tin
 */
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Import config và database connection
require_once 'config.php';
require_once 'database_connection.php';

// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra phương thức gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (!empty($_POST['subscribe_email']) && filter_var($_POST['subscribe_email'], FILTER_VALIDATE_EMAIL)) {
        $email = filter_var($_POST['subscribe_email'], FILTER_SANITIZE_EMAIL);
        
        // Xử lý đăng ký nhận bản tin
        $result = processNewsletterSubscription($email);
        
        // Hiển thị thông báo tương ứng
        $_SESSION['newsletter_success'] = $result['success'];
        $_SESSION['newsletter_message'] = $result['message'];
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
 * Hàm xử lý đăng ký nhận bản tin
 * 
 * @param string $email Email đăng ký
 * @return array Kết quả xử lý
 */
function processNewsletterSubscription($email) {
    // Kết quả mặc định
    $result = [
        'success' => false,
        'message' => 'Có lỗi xảy ra khi đăng ký.'
    ];
    
    try {
        // Bắt đầu transaction
        db_begin_transaction();
        
        // Kiểm tra xem email đã tồn tại trong database chưa
        $subscriber = db_get_row("SELECT id, status FROM newsletter_subscribers WHERE email = :email", ['email' => $email]);
        
        if (!$subscriber) {
            // Thêm email mới vào database
            $subscriber_id = db_insert('newsletter_subscribers', [
                'email' => $email,
                'status' => 'active'
            ]);
            
            if ($subscriber_id) {
                $result = [
                    'success' => true,
                    'message' => "Cảm ơn bạn đã đăng ký nhận bản tin. Chúng tôi sẽ gửi thông tin cập nhật và khuyến mãi mới nhất đến email của bạn.",
                    'type' => 'new' // Đăng ký mới
                ];
                
                // Gửi email xác nhận (tùy chọn)
                // sendSubscriptionConfirmationEmail($email);
            }
        } else {
            // Email đã tồn tại, kiểm tra trạng thái
            if ($subscriber['status'] == 'unsubscribed') {
                // Kích hoạt lại đăng ký
                $updated = db_update('newsletter_subscribers', 
                    ['status' => 'active'], 
                    'id = :id', 
                    ['id' => $subscriber['id']]
                );
                
                if ($updated) {
                    $result = [
                        'success' => true,
                        'message' => "Cảm ơn bạn đã đăng ký lại nhận bản tin. Chúng tôi sẽ tiếp tục gửi thông tin cập nhật mới nhất.",
                        'type' => 'reactivate' // Kích hoạt lại
                    ];
                }
            } else {
                // Email đã đăng ký và đang hoạt động
                $result = [
                    'success' => true,
                    'message' => "Email của bạn đã được đăng ký nhận bản tin trước đó. Chúng tôi sẽ tiếp tục gửi thông tin cập nhật.",
                    'type' => 'existing' // Đã tồn tại
                ];
            }
        }
        
        // Commit transaction
        db_commit();
        
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        db_rollback();
        
        // Ghi log lỗi
        error_log("Lỗi đăng ký bản tin: " . $e->getMessage() . " - Email: $email");
        
        $result = [
            'success' => false,
            'message' => "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại sau.",
            'error' => $e->getMessage()
        ];
    }
    
    return $result;
}

/**
 * Hàm gửi email xác nhận đăng ký (tùy chọn)
 * Đây là hàm mẫu để triển khai sau này
 * 
 * @param string $email Email đăng ký
 * @return bool Trạng thái gửi email
 */
function sendSubscriptionConfirmationEmail($email) {
    // Import PHPMailer
    require 'vendor/autoload.php';

    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        
        // Thông tin người gửi và người nhận
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME . ' Newsletter');
        $mail->addAddress($email);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = "Xác nhận đăng ký nhận bản tin - " . get_config('company_name', 'theCleaner');
        
        // Template HTML
        $companyName = get_config('company_name', 'theCleaner');
        $companyEmail = get_config('company_email', 'info@thecleaner.com');
        $companyPhone = get_config('company_phone', '+84 123 456 789');
        
        $mail->Body = "
        <html>
        <head>
            <title>Đăng ký nhận bản tin thành công</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #00a8ff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { font-size: 12px; color: #777; text-align: center; margin-top: 30px; }
                a { color: #00a8ff; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Cảm ơn bạn đã đăng ký nhận bản tin!</h2>
                </div>
                <div class='content'>
                    <p>Xin chào,</p>
                    <p>Cảm ơn bạn đã đăng ký nhận bản tin từ {$companyName}!</p>
                    <p>Chúng tôi sẽ gửi cho bạn các thông tin cập nhật, khuyến mãi và dịch vụ mới nhất.</p>
                    <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email <a href='mailto:{$companyEmail}'>{$companyEmail}</a> hoặc gọi số điện thoại <a href='tel:{$companyPhone}'>{$companyPhone}</a>.</p>
                    <p>Trân trọng,<br>Đội ngũ {$companyName}</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " {$companyName}. Tất cả các quyền được bảo lưu.</p>
                    <p>Nếu bạn không muốn nhận email từ chúng tôi, vui lòng <a href='#'>nhấp vào đây</a> để hủy đăng ký.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Cảm ơn bạn đã đăng ký nhận bản tin từ {$companyName}! Chúng tôi sẽ gửi cho bạn các thông tin cập nhật, khuyến mãi và dịch vụ mới nhất.";
        
        $mail->send();
        
        // Ghi log gửi email thành công
        error_log("Đã gửi email xác nhận đăng ký bản tin đến: $email");
        
        return true;
    } catch (Exception $e) {
        // Ghi log lỗi gửi email
        error_log("Không thể gửi email xác nhận đăng ký bản tin: {$mail->ErrorInfo} - Email: $email");
        return false;
    }
}
?>