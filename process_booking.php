<?php
// Hiển thị lỗi - chỉ sử dụng trong môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Khởi tạo session
session_start();

// Xử lý POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Danh sách các trường bắt buộc
    $requiredFields = [
        'bookName' => 'Họ và tên',
        'bookEmail' => 'Email',
        'bookPhone' => 'Số điện thoại',
        'bookAddress' => 'Địa chỉ',
        'bookService' => 'Dịch vụ',
        'bookDate' => 'Ngày',
        'bookTime' => 'Thời gian',
        'bookArea' => 'Diện tích'
    ];

    $errors = [];

    // Kiểm tra các trường bắt buộc
    foreach ($requiredFields as $field => $label) {
        if (empty($_POST[$field])) {
            $errors[] = "Vui lòng nhập {$label}";
        }
    }

    // Validate email
    if (!filter_var($_POST['bookEmail'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Địa chỉ email không hợp lệ";
    }

    // Validate số điện thoại
    $phoneRegex = '/^(\+84|0)[3|5|7|8|9][0-9]{8}$/';
    if (!preg_match($phoneRegex, $_POST['bookPhone'])) {
        $errors[] = "Số điện thoại không hợp lệ";
    }

    // Validate ngày
    $selectedDate = strtotime($_POST['bookDate']);
    $today = strtotime(date('Y-m-d'));
    if ($selectedDate < $today) {
        $errors[] = "Ngày phải là ngày trong tương lai";
    }

    // Validate diện tích
    if (intval($_POST['bookArea']) <= 0) {
        $errors[] = "Diện tích phải là số dương";
    }

    // Nếu có lỗi
    if (!empty($errors)) {
        $_SESSION['booking_errors'] = $errors;
        header("Location: booking.php");
        exit();
    }

    // Tính giá dịch vụ
    $estimatedPrice = calculateServicePrice($_POST['bookService'], intval($_POST['bookArea']));

    // Tạo mã đặt lịch
    $bookingId = 'BOOK' . time() . rand(1000, 9999);

    // Chuẩn bị dữ liệu đặt lịch
    $bookingData = [
        'bookingId' => $bookingId,
        'name' => htmlspecialchars($_POST['bookName']),
        'email' => filter_var($_POST['bookEmail'], FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars($_POST['bookPhone']),
        'address' => htmlspecialchars($_POST['bookAddress']),
        'service' => htmlspecialchars($_POST['bookService']),
        'date' => htmlspecialchars($_POST['bookDate']),
        'time' => htmlspecialchars($_POST['bookTime']),
        'area' => intval($_POST['bookArea']),
        'note' => htmlspecialchars($_POST['bookNote'] ?? '')
    ];

    // Lưu thông tin vào session
    $_SESSION['booking_info'] = $bookingData;
    $_SESSION['estimated_price'] = $estimatedPrice;

    // Chuyển hướng đến trang thanh toán
    header("Location: payment.php");
    exit();
}

// Hàm tính giá dịch vụ
function calculateServicePrice($service, $area) {
    $basePrice = 0;
    
    if ($service === 'home') { // Vệ sinh nhà ở
        if ($area < 50) {
            $basePrice = 500000;
        } else if ($area < 100) {
            $basePrice = 800000;
        } else {
            $basePrice = 1000000 + ($area - 100) * 8000;
        }
    } else if ($service === 'office') { // Vệ sinh văn phòng
        if ($area < 100) {
            $basePrice = $area * 15000;
        } else if ($area < 300) {
            $basePrice = $area * 13000;
        } else {
            $basePrice = $area * 11000;
        }
    }
    
    return $basePrice;
}

// Nếu không phải POST request, chuyển hướng về trang đặt lịch
header("Location: booking.php");
exit();
?>