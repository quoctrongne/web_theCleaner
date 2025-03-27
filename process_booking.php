<?php
/**
 * File này xử lý form đặt lịch dịch vụ
 */

// Khởi tạo session nếu chưa có
session_start();

// Kiểm tra phương thức gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate các trường bắt buộc
    if (
        !empty($_POST['bookName']) && 
        !empty($_POST['bookEmail']) && 
        !empty($_POST['bookPhone']) && 
        !empty($_POST['bookAddress']) && 
        !empty($_POST['bookService']) && 
        !empty($_POST['bookDate']) && 
        !empty($_POST['bookTime']) && 
        !empty($_POST['bookArea'])
    ) {
        // Lọc dữ liệu đầu vào
        $name = filter_var($_POST['bookName'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['bookEmail'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['bookPhone'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['bookAddress'], FILTER_SANITIZE_STRING);
        $formattedAddress = !empty($_POST['formattedAddress']) ? 
                            filter_var($_POST['formattedAddress'], FILTER_SANITIZE_STRING) : 
                            $address;
        $latitude = !empty($_POST['latitude']) ? 
                    filter_var($_POST['latitude'], FILTER_VALIDATE_FLOAT) : 
                    null;
        $longitude = !empty($_POST['longitude']) ? 
                     filter_var($_POST['longitude'], FILTER_VALIDATE_FLOAT) : 
                     null;
        $service = filter_var($_POST['bookService'], FILTER_SANITIZE_STRING);
        $date = filter_var($_POST['bookDate'], FILTER_SANITIZE_STRING);
        $time = filter_var($_POST['bookTime'], FILTER_SANITIZE_STRING);
        $area = filter_var($_POST['bookArea'], FILTER_VALIDATE_INT);
        $note = !empty($_POST['bookNote']) ? 
                filter_var($_POST['bookNote'], FILTER_SANITIZE_STRING) : 
                '';
        
        // Chuyển đổi tên dịch vụ
        $serviceLabels = [
            'home' => 'Vệ sinh nhà ở',
            'office' => 'Vệ sinh văn phòng'
        ];
        
        $serviceLabel = isset($serviceLabels[$service]) ? $serviceLabels[$service] : $service;
        
        // Chuyển đổi thời gian
        $timeLabels = [
            '8-10' => '8:00 - 10:00',
            '10-12' => '10:00 - 12:00',
            '13-15' => '13:00 - 15:00',
            '15-17' => '15:00 - 17:00'
        ];
        
        $timeLabel = isset($timeLabels[$time]) ? $timeLabels[$time] : $time;
        
        // Tạo booking ID
        $bookingId = strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Trong ứng dụng thực tế, bạn sẽ lưu thông tin đặt lịch vào cơ sở dữ liệu
        // Ví dụ: saveBookingToDatabase($bookingId, $name, $email, $phone, $address, $formattedAddress, $latitude, $longitude, $service, $date, $time, $area, $note);
        
        // Lưu thông tin đặt lịch vào session để hiển thị trang thanh toán
        $_SESSION['booking_info'] = [
            'bookingId' => $bookingId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $formattedAddress,
            'service' => $service,
            'serviceLabel' => $serviceLabel,
            'date' => $date,
            'time' => $timeLabel,
            'area' => $area,
            'note' => $note
        ];
        
        // Chuyển hướng đến trang thanh toán
        header("Location: payment.php");
        exit;
    } else {
        // Tạo thông báo lỗi và quay lại trang đặt lịch
        $_SESSION['booking_error'] = true;
        $_SESSION['booking_error_message'] = "Vui lòng điền đầy đủ thông tin để đặt lịch.";
        
        // Chuyển hướng về trang đặt lịch
        header("Location: booking.php");
        exit;
    }
} else {
    // Nếu không phải phương thức POST, chuyển hướng về trang chủ
    header("Location: index.php");
    exit;
}

/**
 * Hàm lưu thông tin đặt lịch vào cơ sở dữ liệu (giả định)
 */
function saveBookingToDatabase($bookingId, $name, $email, $phone, $address, $formattedAddress, $latitude, $longitude, $service, $date, $time, $area, $note) {
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
        
        // Thêm thông tin đặt lịch vào cơ sở dữ liệu
        $stmt = $pdo->prepare("
            INSERT INTO bookings (
                booking_id, name, email, phone, address, formatted_address, 
                latitude, longitude, service, booking_date, booking_time, 
                area, note, status, created_at
            ) 
            VALUES (
                :booking_id, :name, :email, :phone, :address, :formatted_address, 
                :latitude, :longitude, :service, :booking_date, :booking_time, 
                :area, :note, 'pending', NOW()
            )
        ");
        
        $stmt->execute([
            'booking_id' => $bookingId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'formatted_address' => $formattedAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'service' => $service,
            'booking_date' => $date,
            'booking_time' => $time,
            'area' => $area,
            'note' => $note
        ]);
        
        return true;
    } catch (PDOException $e) {
        // Xử lý lỗi cơ sở dữ liệu
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm gửi email xác nhận cho khách hàng
 */
function sendConfirmationEmail($email, $name, $bookingId, $service, $date, $time, $area, $address) {
    $to = $email;
    $subject = "Xác nhận đặt lịch dịch vụ - theCleaner";
    
    // Định dạng lại ngày tháng
    $formatDate = date("d/m/Y", strtotime($date));
    
    $message = "
    <html>
    <head>
        <title>Xác nhận đặt lịch dịch vụ</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #00a8ff; color: #fff; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f8f9fa; }
            .booking-details { background-color: #fff; padding: 15px; margin: 20px 0; border-left: 4px solid #00a8ff; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Xác Nhận Đặt Lịch Dịch Vụ</h2>
            </div>
            <div class='content'>
                <p>Xin chào <strong>{$name}</strong>,</p>
                
                <p>Cảm ơn bạn đã đặt lịch dịch vụ vệ sinh với theCleaner. Chúng tôi đã nhận được yêu cầu đặt lịch của bạn và đang xử lý.</p>
                
                <div class='booking-details'>
                    <h3>Chi tiết đặt lịch</h3>
                    <p><strong>Mã đặt lịch:</strong> {$bookingId}</p>
                    <p><strong>Dịch vụ:</strong> {$service}</p>
                    <p><strong>Ngày thực hiện:</strong> {$formatDate}</p>
                    <p><strong>Thời gian:</strong> {$time}</p>
                    <p><strong>Diện tích:</strong> {$area} m²</p>
                    <p><strong>Địa chỉ:</strong> {$address}</p>
                </div>
                
                <p>Vui lòng hoàn thành bước thanh toán để xác nhận lịch đặt của bạn.</p>
                
                <p>Nếu bạn có bất kỳ câu hỏi hoặc cần thay đổi lịch, vui lòng liên hệ với chúng tôi qua số điện thoại <strong>+84 123 456 789</strong> hoặc email <strong>booking@thecleaner.com</strong>.</p>
                
                <p>Trân trọng,<br>Đội ngũ theCleaner</p>
            </div>
            <div class='footer'>
                <p>&copy; 2025 theCleaner. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Các headers cần thiết để gửi email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: theCleaner <booking@thecleaner.com>" . "\r\n";
    
    // Gửi email
    return mail($to, $subject, $message, $headers);
}

/**
 * Hàm gửi thông báo đặt lịch mới cho admin
 */
function sendAdminNotification($bookingId, $name, $email, $phone, $address, $service, $date, $time, $area, $note) {
    $to = "admin@thecleaner.com";
    $subject = "Đặt lịch mới #{$bookingId} - theCleaner";
    
    // Định dạng lại ngày tháng
    $formatDate = date("d/m/Y", strtotime($date));
    
    $message = "
    <html>
    <head>
        <title>Đặt lịch mới</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #00a8ff; color: #fff; padding: 15px; text-align: center; }
            .content { padding: 20px; background-color: #f8f9fa; }
            .booking-details { background-color: #fff; padding: 15px; margin: 20px 0; border-left: 4px solid #00a8ff; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Đặt Lịch Mới</h2>
            </div>
            <div class='content'>
                <p>Có đơn đặt lịch mới từ website:</p>
                
                <div class='booking-details'>
                    <h3>Chi tiết đặt lịch</h3>
                    <p><strong>Mã đặt lịch:</strong> {$bookingId}</p>
                    <p><strong>Tên khách hàng:</strong> {$name}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Số điện thoại:</strong> {$phone}</p>
                    <p><strong>Dịch vụ:</strong> {$service}</p>
                    <p><strong>Ngày thực hiện:</strong> {$formatDate}</p>
                    <p><strong>Thời gian:</strong> {$time}</p>
                    <p><strong>Diện tích:</strong> {$area} m²</p>
                    <p><strong>Địa chỉ:</strong> {$address}</p>
                    " . (!empty($note) ? "<p><strong>Ghi chú:</strong> {$note}</p>" : "") . "
                </div>
                
                <p>Khách hàng đang trong quá trình thanh toán.</p>
                <p><a href='https://thecleaner.com/admin/bookings.php'>Đăng nhập vào trang quản trị</a> để xem và xử lý đơn đặt lịch này.</p>
            </div>
            <div class='footer'>
                <p>&copy; 2025 theCleaner. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
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