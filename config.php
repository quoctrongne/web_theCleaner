<?php
/**
 * File cấu hình chung cho website
 * Include file này trước database_connection.php để thiết lập môi trường
 */

// Chế độ phát triển - đặt true để hiển thị lỗi
define('DEVELOPMENT_MODE', true);

// Cấu hình kết nối database
define('DB_HOST', 'localhost');
define('DB_NAME', 'webck');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
// Nếu ở chế độ phát triển, hiển thị tất cả lỗi
if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Đường dẫn website (tự động phát hiện)
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('SITE_URL', $protocol . "://" . $domain);

// Thiết lập đường dẫn gốc
define('ROOT_PATH', __DIR__);
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB

// API keys - mô phỏng
define('MOMO_PARTNER_CODE', 'MOMOLRJZ20180529');
define('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
define('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2Juz');

// Cấu hình email
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'nguyenquoctrongbt2018@gmail.com');
define('MAIL_PASSWORD', 'iiph cmsi wpst qqgz');
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'info@thecleaner.com');
define('MAIL_FROM_NAME', 'theCleaner Service');

// ⚠️ Cấu hình session chỉ khi session chưa bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 86400);
    ini_set('session.gc_maxlifetime', 86400);
    session_start();
}

// Các hằng số khác
define('DEFAULT_LANG', 'vi');
define('ITEMS_PER_PAGE', 10);

// Cấu hình công ty
$configurations = [
    'company_name' => 'theCleaner',
    'company_address' => '123 Đường ABC, Quận XYZ, Thành phố Hà Nội, Việt Nam',
    'company_phone' => '+84 123 456 789',
    'company_email' => 'info@thecleaner.com',
    'company_working_hours' => 'Thứ Hai - Thứ Bảy: 8:00 - 18:00, Chủ Nhật: Nghỉ',
    'founded_year' => '2015',
    'momo_phone' => '0326097576',
    'momo_account_name' => 'CÔNG TY TNHH DỊCH VỤ VỆ SINH THE CLEANER',
];

// Hàm lấy cấu hình
function get_config($key, $default = null) {
    global $configurations;
    return $configurations[$key] ?? $default;
}
?>
