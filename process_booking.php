<?php
// Import config và database connection
require_once 'config.php';
require_once 'database_connection.php';

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

    // Lấy service_id từ service_code
    $service = $_POST['bookService'];
    $service_row = db_get_row("SELECT * FROM services WHERE service_code = :service_code AND is_active = 1", 
    ['service_code' => $service]);

    // Thêm debug chi tiết
    error_log("Service row: " . print_r($service_row, true));

    if (!$service_row) {
        $_SESSION['booking_error'] = "Dịch vụ không hợp lệ hoặc hiện không khả dụng";
        header("Location: booking.php");
        exit();
    }

    // Xác định service_id đúng từ kết quả truy vấn
    if (isset($service_row['serviceID'])) {
        $service_id = $service_row['serviceID'];
    } elseif (isset($service_row['id'])) {
        $service_id = $service_row['id'];
    } else {
        error_log("Không thể xác định service_id từ: " . print_r($service_row, true));
        $_SESSION['booking_error'] = "Không thể xác định ID dịch vụ";
        header("Location: booking.php");
        exit();
    }
    
    // Tính giá dịch vụ từ database
    $area = intval($_POST['bookArea']);
    // Thêm debug log
    error_log("Service ID: " . $service_id . ", Area: " . $area);

    $estimatedPrice = calculateServicePriceFromDB($service_id, $area);

    // Nếu không thể tính giá
    if ($estimatedPrice === 0) {
        $_SESSION['booking_error'] = "Không thể tính giá cho dịch vụ này với diện tích đã chọn. Vui lòng liên hệ với chúng tôi.";
        error_log("Không thể tính giá: service_id=$service_id, area=$area");
        header("Location: booking.php");
        exit();
    }

    // Tạo mã đặt lịch
    $bookingId = 'BOOK' . time() . rand(1000, 9999);
    
    // *** THAY ĐỔI: Không lưu vào database ngay, chỉ lưu vào session ***
    
    // Chuẩn bị dữ liệu đặt lịch đầy đủ cho session
    $bookingInfo = [
        'bookingId' => $bookingId,
        'name' => htmlspecialchars($_POST['bookName']),
        'email' => filter_var($_POST['bookEmail'], FILTER_SANITIZE_EMAIL),
        'phone' => htmlspecialchars($_POST['bookPhone']),
        'address' => htmlspecialchars($_POST['bookAddress']),
        'service' => htmlspecialchars($service),
        'service_id' => $service_id,
        'date' => htmlspecialchars($_POST['bookDate']),
        'time' => htmlspecialchars($_POST['bookTime']),
        'area' => $area,
        'note' => htmlspecialchars($_POST['bookNote'] ?? '')
    ];

    // Lưu thông tin vào session
    $_SESSION['booking_info'] = $bookingInfo;
    $_SESSION['estimated_price'] = $estimatedPrice;
    
    // Ghi log hoạt động
    error_log("Đặt lịch đã tạo và lưu vào session: " . $bookingId . " | Email: " . $_POST['bookEmail']);
    
    // Chuyển hướng đến trang thanh toán
    header("Location: payment.php");
    exit();
}

/**
 * Tính giá dịch vụ từ database
 * 
 * @param int $service_id ID dịch vụ
 * @param int $area Diện tích
 * @return float Giá dịch vụ
 */
/**
 * Tính giá dịch vụ từ database
 * 
 * @param int $service_id ID dịch vụ
 * @param int $area Diện tích
 * @return float Giá dịch vụ
 */
function calculateServicePriceFromDB($service_id, $area) {
    // Thêm log để debug
    error_log("Calculating price for service_id: " . $service_id . ", area: " . $area);

    // Truy vấn chi tiết
    $query = "SELECT * FROM service_pricing 
              WHERE service_id = :service_id 
              AND min_area <= :area 
              AND (max_area IS NULL OR max_area >= :area)
              ORDER BY min_area DESC 
              LIMIT 1";
              
    $params = [
        'service_id' => $service_id,
        'area' => $area
    ];
    
    // Log truy vấn
    error_log("Price query: " . $query . " - Params: " . json_encode($params));
    
    $pricing = db_get_row($query, $params);
    error_log("Pricing result: " . ($pricing ? json_encode($pricing) : "No pricing found"));
    
    if (!$pricing) {
        // Nếu không tìm thấy cấu hình giá, tính giá mặc định theo bảng giá hiển thị
        error_log("Không tìm thấy cấu hình giá, sử dụng giá mặc định theo bảng giá hiển thị");
        
        // Giá mặc định dựa vào loại dịch vụ và diện tích theo bảng giá hiển thị
        if ($service_id == 1) { // Vệ sinh nhà ở
            if ($area < 50) {
                return $area * 20000; // 20.000đ/m²
            } elseif ($area <= 100) {
                return $area * 16000; // 16.000đ/m²
            } else {
                return $area * 14000; // 14.000đ/m²
            }
        } else { // Vệ sinh văn phòng hoặc dịch vụ khác
            if ($area < 100) {
                return $area * 25000; // 25.000đ/m²
            } elseif ($area <= 300) {
                return $area * 22000; // 22.000đ/m²
            } else {
                return $area * 20000; // 20.000đ/m²
            }
        }
    }
    
    // Tính giá nếu có cấu hình
    $basePrice = floatval($pricing['base_price']);
    
    // Tính giá theo loại
    if ($pricing['pricing_type'] === 'fixed') {
        // Giá cố định
        $price = $basePrice;
        
        // Nếu có giá bổ sung cho diện tích vượt quá
        if ($pricing['additional_price'] !== null && $area > $pricing['min_area']) {
            $additionalArea = $area - $pricing['min_area'];
            $price += $additionalArea * floatval($pricing['additional_price']);
        }
    } else {
        // Giá theo m²
        $price = $area * $basePrice;
    }
    
    error_log("Calculated price: " . $price);
    return $price;
}

// Nếu không phải POST request, chuyển hướng về trang đặt lịch
header("Location: booking.php");
exit();
?>