<?php
/**
 * File chứa các hàm tiện ích cho toàn bộ website
 */

/**
 * Hàm hiển thị thông báo lỗi
 * 
 * @param array $errors Mảng các lỗi
 * @return string HTML thông báo lỗi
 */
function display_errors($errors) {
    if (empty($errors) || !is_array($errors)) {
        return '';
    }
    
    $html = '<div class="alert alert-error"><ul>';
    foreach ($errors as $error) {
        $html .= '<li>' . htmlspecialchars($error) . '</li>';
    }
    $html .= '</ul></div>';
    
    return $html;
}

/**
 * Hàm hiển thị thông báo thành công
 * 
 * @param string $message Thông báo
 * @return string HTML thông báo thành công
 */
function display_success($message) {
    if (empty($message)) {
        return '';
    }
    
    return '<div class="alert alert-success"><p>' . htmlspecialchars($message) . '</p></div>';
}

/**
 * Hàm tạo URL an toàn
 * 
 * @param string $url URL gốc
 * @param array $params Tham số
 * @return string URL hoàn chỉnh
 */
function build_url($url, $params = []) {
    if (empty($params)) {
        return $url;
    }
    
    $query = http_build_query($params);
    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    
    return $url . $separator . $query;
}

/**
 * Hàm tạo chuỗi ngẫu nhiên
 * 
 * @param int $length Độ dài chuỗi
 * @param string $chars Các ký tự có thể có
 * @return string Chuỗi ngẫu nhiên
 */
function random_string($length = 10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
    $result = '';
    $max = strlen($chars) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, $max)];
    }
    
    return $result;
}

/**
 * Hàm định dạng số tiền
 * 
 * @param float $amount Số tiền
 * @param string $currency Đơn vị tiền tệ (mặc định là 'đ')
 * @return string Số tiền đã định dạng
 */
function format_money($amount, $currency = 'đ') {
    return number_format($amount, 0, ',', '.') . ' ' . $currency;
}

/**
 * Hàm định dạng ngày tháng
 * 
 * @param string $date Ngày tháng (định dạng Y-m-d)
 * @param string $format Định dạng mong muốn
 * @return string Ngày tháng đã định dạng
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }
    
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Hàm chuyển đổi khung giờ từ dạng code sang text
 * 
 * @param string $timeSlot Mã khung giờ (vd: '8-10')
 * @return string Khung giờ dạng text (vd: '8:00 - 10:00')
 */
function format_time_slot($timeSlot) {
    $timeSlots = [
        '8-10' => '8:00 - 10:00',
        '10-12' => '10:00 - 12:00',
        '13-15' => '13:00 - 15:00',
        '15-17' => '15:00 - 17:00'
    ];
    
    return isset($timeSlots[$timeSlot]) ? $timeSlots[$timeSlot] : $timeSlot;
}

/**
 * Hàm hiển thị đánh giá dạng sao
 * 
 * @param float $rating Số sao (từ 0 đến 5)
 * @param string $fullStarClass Class CSS cho sao đầy
 * @param string $halfStarClass Class CSS cho nửa sao
 * @param string $emptyStarClass Class CSS cho sao rỗng
 * @return string HTML đánh giá sao
 */
function display_rating($rating, $fullStarClass = 'fas fa-star', $halfStarClass = 'fas fa-star-half-alt', $emptyStarClass = 'far fa-star') {
    $output = '';
    $fullStars = floor($rating);
    $halfStar = $rating - $fullStars >= 0.5;
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            $output .= '<i class="' . $fullStarClass . '"></i>';
        } elseif ($i == $fullStars + 1 && $halfStar) {
            $output .= '<i class="' . $halfStarClass . '"></i>';
        } else {
            $output .= '<i class="' . $emptyStarClass . '"></i>';
        }
    }
    
    return $output;
}

/**
 * Hàm rút gọn văn bản
 * 
 * @param string $text Văn bản cần rút gọn
 * @param int $length Độ dài tối đa
 * @param string $suffix Hậu tố (vd: '...')
 * @return string Văn bản đã rút gọn
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Hàm kiểm tra trang hiện tại
 * 
 * @param string $page Tên trang cần kiểm tra
 * @param string $currentPage Tên trang hiện tại
 * @return bool Kết quả kiểm tra
 */
function is_current_page($page, $currentPage) {
    return ($page === $currentPage);
}

/**
 * Hàm tạo lớp CSS cho menu
 * 
 * @param string $page Tên trang cần kiểm tra
 * @param string $currentPage Tên trang hiện tại
 * @param string $class Class CSS bổ sung (tùy chọn)
 * @return string Class CSS
 */
function menu_class($page, $currentPage, $class = '') {
    return is_current_page($page, $currentPage) ? 'active ' . $class : $class;
}

/**
 * Hàm kiểm tra email hợp lệ
 * 
 * @param string $email Email cần kiểm tra
 * @return bool Kết quả kiểm tra
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Hàm kiểm tra số điện thoại hợp lệ (Việt Nam)
 * 
 * @param string $phone Số điện thoại cần kiểm tra
 * @return bool Kết quả kiểm tra
 */
function is_valid_phone($phone) {
    return preg_match('/^(\+84|0)[3|5|7|8|9][0-9]{8}$/', $phone) === 1;
}

/**
 * Hàm chuyển đổi chuỗi thành slug
 * 
 * @param string $text Chuỗi cần chuyển đổi
 * @return string Slug
 */
function to_slug($text) {
    // Chuyển đổi tiếng Việt sang không dấu
    $text = remove_vietnamese_accents($text);
    
    // Chuyển đổi thành slug
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Hàm loại bỏ dấu tiếng Việt
 * 
 * @param string $text Chuỗi cần xử lý
 * @return string Chuỗi đã loại bỏ dấu
 */
function remove_vietnamese_accents($text) {
    $search = array(
        'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ằ', 'ắ', 'ẳ', 'ẵ', 'ặ', 'â', 'ầ', 'ấ', 'ẩ', 'ẫ', 'ậ',
        'đ',
        'è', 'é', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ề', 'ế', 'ể', 'ễ', 'ệ',
        'ì', 'í', 'ỉ', 'ĩ', 'ị',
        'ò', 'ó', 'ỏ', 'õ', 'ọ', 'ô', 'ồ', 'ố', 'ổ', 'ỗ', 'ộ', 'ơ', 'ờ', 'ớ', 'ở', 'ỡ', 'ợ',
        'ù', 'ú', 'ủ', 'ũ', 'ụ', 'ư', 'ừ', 'ứ', 'ử', 'ữ', 'ự',
        'ỳ', 'ý', 'ỷ', 'ỹ', 'ỵ',
        'À', 'Á', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ằ', 'Ắ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ầ', 'Ấ', 'Ẩ', 'Ẫ', 'Ậ',
        'Đ',
        'È', 'É', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ề', 'Ế', 'Ể', 'Ễ', 'Ệ',
        'Ì', 'Í', 'Ỉ', 'Ĩ', 'Ị',
        'Ò', 'Ó', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ồ', 'Ố', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ờ', 'Ớ', 'Ở', 'Ỡ', 'Ợ',
        'Ù', 'Ú', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ừ', 'Ứ', 'Ử', 'Ữ', 'Ự',
        'Ỳ', 'Ý', 'Ỷ', 'Ỹ', 'Ỵ'
    );
    
    $replace = array(
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'd',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
        'D',
        'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
        'I', 'I', 'I', 'I', 'I',
        'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
        'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
        'Y', 'Y', 'Y', 'Y', 'Y'
    );
    
    return str_replace($search, $replace, $text);
}

/**
 * Hàm tạo input token CSRF
 * 
 * @return string HTML input hidden
 */
function csrf_token_input() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Hàm kiểm tra token CSRF
 * 
 * @param string $token Token từ form
 * @return bool Kết quả kiểm tra
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hàm kiểm tra quyền truy cập
 * 
 * @param array $allowed_roles Mảng các role được phép
 * @return bool Kết quả kiểm tra
 */
function check_permission($allowed_roles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    return in_array($_SESSION['user_role'], $allowed_roles);
}

/**
 * Hàm chuyển hướng đến URL
 * 
 * @param string $url URL cần chuyển hướng
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Hàm tạo form đặt lịch
 * 
 * @param array $services Mảng các dịch vụ
 * @param array $timeSlots Mảng các khung giờ
 * @return string HTML form đặt lịch
 */
function generate_booking_form($services, $timeSlots) {
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    
    $html = '
    <form id="bookingForm" class="booking-form" method="post" action="process_booking.php">
        <div class="form-row">
            <div class="form-group">
                <label for="bookName">Họ và tên</label>
                <input type="text" id="bookName" name="bookName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="bookEmail">Email</label>
                <input type="email" id="bookEmail" name="bookEmail" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="bookPhone">Số điện thoại</label>
                <input type="tel" id="bookPhone" name="bookPhone" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="bookAddress">Địa chỉ</label>
                <input type="text" id="bookAddress" name="bookAddress" class="form-control" placeholder="Nhập địa chỉ đầy đủ" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="bookService">Loại dịch vụ</label>
                <select id="bookService" name="bookService" class="form-control" required>
                    <option value="" disabled selected>Chọn dịch vụ</option>';
    
    foreach ($services as $service) {
        $html .= '<option value="' . htmlspecialchars($service['value']) . '">' . htmlspecialchars($service['label']) . '</option>';
    }
    
    $html .= '
                </select>
            </div>
            <div class="form-group">
                <label for="bookDate">Ngày thực hiện</label>
                <input type="date" id="bookDate" name="bookDate" class="form-control" min="' . $tomorrow . '" value="' . $tomorrow . '" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="bookTime">Thời gian</label>
                <select id="bookTime" name="bookTime" class="form-control" required>
                    <option value="" disabled selected>Chọn thời gian</option>';
    
    foreach ($timeSlots as $slot) {
        $html .= '<option value="' . htmlspecialchars($slot['value']) . '">' . htmlspecialchars($slot['label']) . '</option>';
    }
    
    $html .= '
                </select>
            </div>
            <div class="form-group">
                <label for="bookArea">Diện tích (m²)</label>
                <input type="number" id="bookArea" name="bookArea" class="form-control" min="1" required>
            </div>
        </div>
        <div class="form-group">
            <label for="bookNote">Ghi chú thêm</label>
            <textarea id="bookNote" name="bookNote" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-pricing">
            <div class="pricing-note">
                <p><i class="fas fa-info-circle"></i> Giá dịch vụ được tính dựa trên loại dịch vụ và diện tích. Chi tiết giá sẽ được hiển thị ở trang thanh toán.</p>
            </div>
        </div>
        ' . csrf_token_input() . '
        <button type="submit" class="btn btn-primary">Đặt Lịch Ngay</button>
    </form>';
    
    return $html;
}

/**
 * Hàm hiển thị thông tin công ty trong footer
 * 
 * @return string HTML thông tin công ty
 */
function get_company_info_footer() {
    $company_name = get_config('company_name', 'theCleaner');
    $company_address = get_config('company_address', '123 Đường ABC, Quận XYZ, Thành phố Hà Nội, Việt Nam');
    $company_phone = get_config('company_phone', '+84 123 456 789');
    $company_email = get_config('company_email', 'info@thecleaner.com');
    
    return "
    <div class='footer-col'>
        <h4>Về {$company_name}</h4>
        <p>{$company_name} là công ty chuyên cung cấp dịch vụ vệ sinh chuyên nghiệp, với đội ngũ nhân viên chuyên nghiệp và trang thiết bị hiện đại.</p>
        <div class='footer-social'>
            <a href='#'><i class='fab fa-facebook-f'></i></a>
            <a href='#'><i class='fab fa-twitter'></i></a>
            <a href='#'><i class='fab fa-instagram'></i></a>
            <a href='#'><i class='fab fa-linkedin-in'></i></a>
        </div>
    </div>";
}
?>