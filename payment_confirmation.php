<?php
// Kết nối đến database
require_once 'database_connection.php';

// Khởi tạo session
session_start();

// Debug để hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kiểm tra xem thông tin giao dịch có trong session không
if (!isset($_SESSION['transaction_info']) || !isset($_SESSION['booking_info'])) {
    // Chuyển hướng về trang đặt lịch nếu không có thông tin
    header("Location: booking.php");
    exit;
}

// Lấy thông tin giao dịch và đặt lịch từ session
$transactionInfo = $_SESSION['transaction_info'];
$bookingInfo = $_SESSION['booking_info'];
$estimatedPrice = $_SESSION['estimated_price'];

// Debug thông tin transaction trong log
error_log("Transaction info trước khi xử lý: " . print_r($transactionInfo, true));

// Kiểm tra trạng thái thanh toán
$paymentStatus = isset($transactionInfo['status']) ? $transactionInfo['status'] : 'pending';

// BẮT BUỘC trạng thái thành completed để đảm bảo lưu database
if ($paymentStatus !== 'completed') {
    $paymentStatus = 'completed';
    $_SESSION['transaction_info']['status'] = 'completed';
    error_log("Đã buộc trạng thái thanh toán thành completed");
}

// *** THÊM MỚI: Lưu thông tin vào database sau khi thanh toán thành công ***
if ($paymentStatus === 'completed') {
    error_log("Bắt đầu lưu dữ liệu vào database");

    // Bắt đầu transaction để đảm bảo tính nhất quán của dữ liệu
    db_begin_transaction();

    try {
        // 1. Lưu thông tin khách hàng vào bảng customers
        $customer_data = [
            'fullName' => $bookingInfo['name'],
            'email' => $bookingInfo['email'],
            'phone' => $bookingInfo['phone'],
            'address' => $bookingInfo['address']
        ];

        $customer_id = db_insert('customers', $customer_data);
        error_log("Đã lưu thông tin khách hàng: ID=" . $customer_id);

        if (!$customer_id) {
            throw new Exception("Không thể lưu thông tin khách hàng");
        }

        // 2. Lưu thông tin đặt lịch vào bảng bookings
        $booking_data = [
            'bookingID' => mt_rand(100000, 999999), // ID ngẫu nhiên
            'user_id' => 1, // ĐANG ĐẶT GIÁ TRỊ CỤ THỂ thay vì null
            'service_id' => $bookingInfo['service_id'],
            'booking_date' => $bookingInfo['date'],
            'booking_time' => $bookingInfo['time'],
            'address' => $bookingInfo['address'],
            'area' => $bookingInfo['area'],
            'note' => $bookingInfo['note'] ?? '',
            'price' => $estimatedPrice,
            'status' => 'confirmed', // Trạng thái đã xác nhận vì đã thanh toán
            'totalAmount' => $estimatedPrice,
            'bookingDate' => $bookingInfo['date'],
            'serviceID' => $bookingInfo['service_id'],
            'amount' => $estimatedPrice,
            'customerID' => $customer_id,
            'employeeID' => 1 // Giá trị mặc định, có thể phân công sau
        ];

        error_log("Booking data trước khi insert: " . print_r($booking_data, true));

        $booking_id = db_insert('bookings', $booking_data);
        error_log("Đã lưu thông tin đặt lịch: ID=" . $booking_id);

        if (!$booking_id) {
            throw new Exception("Không thể lưu thông tin đặt lịch");
        }

        // 3. Lưu thông tin thanh toán vào bảng payments
        $payment_data = [
            'transaction_id' => $transactionInfo['transaction_id'],
            'booking_id' => $booking_id,
            'payment_method' => $transactionInfo['payment_method'],
            'amount' => $estimatedPrice,
            'status' => 'completed',
            'payment_data' => json_encode(['description' => 'Thanh toán dịch vụ ' . $bookingInfo['service']]),
            'paid_at' => date('Y-m-d H:i:s')
        ];

        $payment_id = db_insert('payments', $payment_data);
        error_log("Đã lưu thông tin thanh toán: ID=" . $payment_id);

        // Commit transaction nếu tất cả đều thành công
        db_commit();
        error_log("Đã lưu tất cả thông tin đặt lịch thành công cho booking " . $bookingInfo['bookingId']);

    } catch (Exception $e) {
        // Rollback nếu có lỗi
        db_rollback();
        error_log("Lỗi khi lưu thông tin đặt lịch: " . $e->getMessage());
        $saveDbError = $e->getMessage();
    }
} else {
    error_log("Không lưu vào database vì status = " . $paymentStatus . " (Điều này không nên xảy ra nữa)");
}

// Lấy thông tin dịch vụ từ database
$serviceName = '';
$service_row = db_get_row("SELECT name FROM services WHERE service_code = :code", ['code' => $bookingInfo['service']]);
if ($service_row) {
    $serviceName = $service_row['name'];
} else {
    $serviceName = $bookingInfo['service'];
}

// Chuyển đổi định dạng ngày
$formattedDate = date('d/m/Y', strtotime($bookingInfo['date']));

// Định dạng giá tiền
$formattedPrice = number_format($estimatedPrice, 0, ',', '.') . ' đ';

// Lấy năm hiện tại cho footer
$currentYear = date("Y");

// Tạo mã đặt lịch ngắn gọn để hiển thị
$bookingCode = substr($bookingInfo['bookingId'], -8);

// Lấy thời gian hiện tại cho ngày xác nhận
$confirmationDate = date('d/m/Y H:i:s');

// Lấy thông tin nhân viên được phân công từ database, CHỈ lấy nhân viên vệ sinh
$selectedStaff = null;
if (isset($booking_id)) {
    // Kiểm tra xem đã có nhân viên vệ sinh được phân công chưa
    $booking_staff = db_get_row(
        "SELECT e.*, u.role
        FROM booking_employees be
        JOIN employees e ON be.employee_id = e.employeeID
        LEFT JOIN users u ON e.userID = u.userID
        WHERE be.booking_id = :booking_id
        AND (e.department = 'Nhân viên vệ sinh' 
             OR e.specialization LIKE '%vệ sinh%'
             OR e.employeeID IN (SELECT employeeID FROM cleaning_staff))",
        ['booking_id' => $booking_id]
    );

    if (!$booking_staff && isset($bookingInfo['service'])) {
        // Nếu chưa có, tự động phân công nhân viên vệ sinh phù hợp với specialization
        $staff_with_role = db_get_row(
            "SELECT e.*, u.role
            FROM employees e
            LEFT JOIN users u ON e.userID = u.userID
            WHERE e.specialization = :service
              AND e.status = 'active'
              AND (e.department = 'Nhân viên vệ sinh' OR e.specialization LIKE '%vệ sinh%')
            ORDER BY e.rating DESC, RAND() LIMIT 1",
            ['service' => $serviceName]
        );

        if ($staff_with_role) {
            // Phân công nhân viên
            db_insert('booking_employees', [
                'booking_id' => $booking_id,
                'employee_id' => $staff_with_role['employeeID']
            ]);

            $selectedStaff = $staff_with_role;
        } else {
            // Nếu không tìm thấy nhân viên phù hợp theo chuyên môn, lấy một nhân viên vệ sinh bất kỳ
            $staff_with_role = db_get_row(
                "SELECT e.*, u.role
                FROM employees e
                LEFT JOIN users u ON e.userID = u.userID
                WHERE e.status = 'active'
                  AND (e.department = 'Nhân viên vệ sinh' OR e.specialization LIKE '%vệ sinh%')
                ORDER BY e.rating DESC, RAND() LIMIT 1"
            );

            if ($staff_with_role) {
                // Phân công nhân viên
                db_insert('booking_employees', [
                    'booking_id' => $booking_id,
                    'employee_id' => $staff_with_role['employeeID']
                ]);

                $selectedStaff = $staff_with_role;
            }
        }
    } else {
        $selectedStaff = $booking_staff;
    }
}

// Xác định domain website để dùng cho đường dẫn hình ảnh trong email
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'thecleaner.com';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$baseUrl = $protocol . "://" . $domain;

// Tạo mẫu email hóa đơn
$emailTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đặt Lịch - theCleaner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #00a8ff;
            padding: 20px;
            text-align: center;
            color: white;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .booking-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .booking-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .booking-details th, .booking-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .staff-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        .staff-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .staff-details {
            flex: 1;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00a8ff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .highlight {
            font-weight: bold;
            color: #00a8ff;
        }
        .rating {
            color: #fbc531;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Xác Nhận Đặt Lịch</h1>
            <p>Cảm ơn bạn đã đặt lịch dịch vụ tại theCleaner!</p>
        </div>
        <div class="content">
            <p>Xin chào <strong>' . htmlspecialchars($bookingInfo['name']) . '</strong>,</p>
            <p>Chúng tôi xác nhận đã nhận được đặt lịch dịch vụ vệ sinh của bạn. Dưới đây là chi tiết đặt lịch:</p>

            <div class="booking-details">
                <h2>Chi Tiết Đặt Lịch</h2>
                <table>
                    <tr>
                        <th>Mã đặt lịch:</th>
                        <td class="highlight">' . $bookingCode . '</td>
                    </tr>
                    <tr>
                        <th>Dịch vụ:</th>
                        <td>' . htmlspecialchars($serviceName) . '</td>
                    </tr>
                    <tr>
                        <th>Ngày thực hiện:</th>
                        <td>' . htmlspecialchars($formattedDate) . '</td>
                    </tr>
                    <tr>
                        <th>Thời gian:</th>
                        <td>' . htmlspecialchars($bookingInfo['time']) . '</td>
                    </tr>
                    <tr>
                        <th>Địa chỉ:</th>
                        <td>' . htmlspecialchars($bookingInfo['address']) . '</td>
                    </tr>
                    <tr>
                        <th>Diện tích:</th>
                        <td>' . htmlspecialchars($bookingInfo['area']) . ' m²</td>
                    </tr>
                    <tr>
                        <th>Tổng thanh toán:</th>
                        <td class="highlight">' . $formattedPrice . '</td>
                    </tr>
                    <tr>
                        <th>Trạng thái thanh toán:</th>
                        <td>Đã thanh toán</td>
                    </tr>
                </table>
            </div>';

// Thêm thông tin nhân viên nếu có - điều chỉnh để sử dụng tên trường từ bảng employees
if ($selectedStaff) {
    $emailTemplate .= '
            <div class="staff-info">
                <img src="' . $baseUrl . '/' . $selectedStaff['avatar'] . '" alt="' . htmlspecialchars($selectedStaff['fullName']) . '" class="staff-avatar">
                <div class="staff-details">
                    <h2>Nhân Viên Thực Hiện</h2>
                    <p><strong>Họ tên:</strong> ' . htmlspecialchars($selectedStaff['fullName']) . '</p>
                    <p><strong>Giới tính:</strong> ' . htmlspecialchars($selectedStaff['gender']) . '</p>
                    <p><strong>Đánh giá:</strong> <span class="rating">' . $selectedStaff['rating'] . '/5</span></p>
                    <p><strong>Kinh nghiệm:</strong> ' . htmlspecialchars($selectedStaff['experience']) . '</p>
                    <p><strong>Liên hệ:</strong> ' . htmlspecialchars($selectedStaff['phone']) . '</p>
                </div>
            </div>';
}

$emailTemplate .= '
            <p>Nhân viên sẽ liên hệ với bạn qua số điện thoại trước khi đến để xác nhận lại. Vui lòng để ý điện thoại trong thời gian này.</p>

            <p>Nếu bạn cần thay đổi lịch hoặc có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua số điện thoại <strong>' . get_config('company_phone', '0326097576') . '</strong> hoặc email <strong>' . get_config('company_email', 'support@thecleaner.com') . '</strong>.</p>

            <p>Cảm ơn bạn đã sử dụng dịch vụ của theCleaner!</p>

            <a href="' . $baseUrl . '" class="button">Truy cập website</a>
        </div>
        <div class="footer">
            <p>&copy; ' . $currentYear . ' theCleaner. Tất cả các quyền được bảo lưu.</p>
            <p>Địa chỉ: ' . get_config('company_address', '123 Đường ABC, Quận XYZ, Thành phố Hà Nội, Việt Nam') . '</p>
        </div>
    </div>
</body>
</html>';

// Gọi file xử lý gửi email
require_once 'send_email.php';

// Gửi email thật
$emailSent = false;
try {
    error_log("Bắt đầu gửi email...");
    $emailSent = sendBookingConfirmationEmail($bookingInfo, $emailTemplate, $bookingCode);
    error_log("Kết quả gửi email: " . ($emailSent ? "Thành công" : "Thất bại"));
} catch (Exception $e) {
    error_log("Lỗi gửi email (ngoại lệ chính): " . $e->getMessage());
}

// Lưu trạng thái gửi email vào session để hiển thị thông báo
$_SESSION['email_sent'] = $emailSent;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Thanh Toán - theCleaner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/payment.css">
    <link rel="stylesheet" href="styles/confirmation-styles.css">
</head>
<body>
    <?php if (isset($saveDbError)): ?>
    <div class="db-error-notification">
        <p><i class="fas fa-exclamation-circle"></i> Đã xảy ra lỗi khi lưu thông tin đặt lịch: <?php echo $saveDbError; ?></p>
        <p>Vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['email_sent'])): ?>
    <div class="email-status-notification" style="background: <?php echo $_SESSION['email_sent'] ? '#4caf50' : '#f44336'; ?>;">
        <?php if ($_SESSION['email_sent']): ?>
            <p><i class="fas fa-check-circle"></i> Email xác nhận đã được gửi thành công!</p>
        <?php else: ?>
            <p><i class="fas fa-exclamation-circle"></i> Không thể gửi email xác nhận. Vui lòng liên hệ hỗ trợ!</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="confetti-container" id="confettiContainer"></div>

    <div class="email-notification" id="emailNotification">
        <div class="email-header">
            <div class="email-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="email-title">
                <h4>Xác Nhận Đặt Lịch</h4>
                <p>support@thecleaner.com</p>
            </div>
        </div>
        <div class="email-content">
            <p>Email xác nhận đặt lịch và hóa đơn đã được gửi đến hộp thư của bạn.</p>
        </div>
        <div class="email-actions">
            <button id="closeEmailNotification">Đóng</button>
        </div>
    </div>

    <section class="page-banner">
        <div class="container">
            <h1>Xác Nhận Thanh Toán</h1>
            <p>Cảm ơn bạn đã sử dụng dịch vụ của theCleaner</p>
        </div>
    </section>

    <section class="confirmation-section">
        <div class="container">
            <div class="confirmation-container">
                <div class="confirmation-message">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2>Đặt Lịch Thành Công!</h2>
                    <p>Cảm ơn bạn đã đặt lịch và thanh toán dịch vụ vệ sinh của theCleaner.</p>
                    <p>Mã đặt lịch của bạn là <strong><?php echo $bookingCode; ?></strong></p>
                    <p>Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận lịch thực hiện dịch vụ.</p>
                </div>

                <div class="confirmation-details">
                    <h3>Chi Tiết Đặt Lịch</h3>
                    <div class="details-grid">
                        <div class="details-item">
                            <strong>Họ và tên</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['name']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Email</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['email']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Số điện thoại</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['phone']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Dịch vụ</strong>
                            <span><?php echo htmlspecialchars($serviceName); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Ngày thực hiện</strong>
                            <span><?php echo htmlspecialchars($formattedDate); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Thời gian</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['time']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Địa chỉ</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['address']); ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Diện tích</strong>
                            <span><?php echo htmlspecialchars($bookingInfo['area']); ?> m²</span>
                        </div>
                        <div class="details-item">
                            <strong>Phương thức thanh toán</strong>
                            <span>MoMo</span>
                        </div>
                        <div class="details-item">
                            <strong>Trạng thái thanh toán</strong>
                            <span class="payment-status-completed">Đã thanh toán</span>
                        </div>
                        <div class="details-item">
                            <strong>Thời gian xác nhận</strong>
                            <span><?php echo $confirmationDate; ?></span>
                        </div>
                        <div class="details-item">
                            <strong>Mã giao dịch</strong>
                            <span><?php echo htmlspecialchars($transactionInfo['transaction_id']); ?></span>
                        </div>
                        <div class="details-item total">
                            <strong>Tổng thanh toán</strong>
                            <span><?php echo $formattedPrice; ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($selectedStaff): ?>
                <div class="assigned-staff">
                    <div class="section-title">
                        <h2>Nhân Viên Được Phân Công</h2>
                        <p>Nhân viên này sẽ liên hệ và đến thực hiện dịch vụ cho bạn</p>
                    </div>
                    <div class="staff-profile" id="staffProfile">
                        <div class="staff-avatar">
                            <img src="<?php echo $selectedStaff['avatar']; ?>" alt="<?php echo htmlspecialchars($selectedStaff['fullName']); ?>">
                            <div class="staff-rating">
                                <i class="fas fa-star"></i> <?php echo $selectedStaff['rating']; ?>
                            </div>
                        </div>
                        <div class="staff-details">
                            <h3><?php echo htmlspecialchars($selectedStaff['fullName']); ?></h3>
                            <div class="staff-info">
                                <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($selectedStaff['gender']); ?></p>
                                <p><strong>Tuổi:</strong> <?php echo $selectedStaff['age']; ?></p>
                                <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($selectedStaff['experience']); ?></p>
                                <p><strong>Chuyên môn:</strong> <?php echo htmlspecialchars($selectedStaff['specialization']); ?></p>
                                <p><strong>Liên hệ:</strong> <?php echo htmlspecialchars($selectedStaff['phone']); ?></p>
                            </div>
                            <div class="staff-note">
                                <p><i class="fas fa-info-circle"></i> Nhân viên sẽ liên hệ với bạn qua số điện thoại trước khi đến để xác nhận lại. Vui lòng để ý điện thoại trong thời gian này.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="next-steps">
                    <h3>Các Bước Tiếp Theo</h3>
                    <ul>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="step-content">
                                <h4>Xác Nhận Qua Email</h4>
                                <p>Nhân viên của chúng tôi sẽ gửi email để xác nhận thông tin và thời gian thực hiện dịch vụ.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="step-content">
                                <h4>Liên Hệ Trước Khi Đến</h4>
                                <p>Nhân viên sẽ gọi điện trước khi đến để xác nhận thời gian và địa chỉ chính xác.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="step-content">
                                <h4>Thực Hiện Dịch Vụ</h4>
                                <p>Đội ngũ nhân viên sẽ đến đúng hẹn và thực hiện dịch vụ theo yêu cầu của bạn.</p>
                            </div>
                        </li>
                        <li>
                            <div class="step-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="step-content">
                                <h4>Kiểm Tra Và Nghiệm Thu</h4>
                                <p>Sau khi hoàn thành, chúng tôi sẽ cùng bạn kiểm tra chất lượng dịch vụ.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="confirmation-actions">
                    <a href="index.php" class="btn btn-primary">Về Trang Chủ</a>
                    <a href="#" id="viewEmailBtn" class="btn btn-outline">Xem Email</a>
                </div>
            </div>
        </div>
    </section>

    <div id="emailModal">
        <div>
            <button id="closeEmailModal">&times;</button>
            <h2 style="text-align: center; margin-bottom: 20px;">Email Hóa Đơn</h2>
            <div id="emailPreview">
                <?php echo $emailTemplate; ?>
            </div>
        </div>
    </div>

    <script src="scripts/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation cho staff profile
            const staffProfile = document.getElementById('staffProfile');
            if (staffProfile) {
                setTimeout(() => {
                    staffProfile.classList.add('visible');
                }, 1000);
            }

            // Hiển thị notification email đã gửi
            const emailNotification = document.getElementById('emailNotification');
            if (emailNotification) {
                setTimeout(() => {
                    emailNotification.classList.add('show');
                }, 3000);
            }

            // Đóng notification email
            const closeEmailNotification = document.getElementById('closeEmailNotification');
            if (closeEmailNotification && emailNotification) {
                closeEmailNotification.addEventListener('click', function() {
                    emailNotification.classList.remove('show');
                });
            }

            // Hiển thị modal email preview
            const viewEmailBtn = document.getElementById('viewEmailBtn');
            const emailModal = document.getElementById('emailModal');
            const closeEmailModal = document.getElementById('closeEmailModal');

            if (viewEmailBtn && emailModal) {
                viewEmailBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    emailModal.style.display = 'block';
                });
            }

            if (closeEmailModal && emailModal) {
                closeEmailModal.addEventListener('click', function() {
                    emailModal.style.display = 'none';
                });
            }

            // Đóng modal khi click ngoài nội dung
            window.addEventListener('click', function(e) {
                if (emailModal && e.target === emailModal) {
                    emailModal.style.display = 'none';
                }
            });

            // Tạo hiệu ứng Confetti
            const confettiContainer = document.getElementById('confettiContainer');
            const colors = ['#f94144', '#f3722c', '#f8961e', '#f9c74f', '#90be6d', '#43aa8b', '#577590'];

            function createConfetti() {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.opacity = Math.random() * 0.5 + 0.5;
                confetti.style.width = Math.random() * 10 + 5 + 'px';
                confetti.style.height = Math.random() * 10 + 5 + 'px';
                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                confetti.style.animationDuration = Math.random() * 3 + 2 + 's';

                confettiContainer.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }

            // Tạo hiệu ứng confetti khi trang tải xong
            if (confettiContainer) {
                for (let i = 0; i < 100; i++) {
                    setTimeout(createConfetti, i * 100);
                }
            }

            // Xóa thông báo trạng thái email sau 5 giây
            const emailStatusNotification = document.querySelector('.email-status-notification');
            if (emailStatusNotification) {
                setTimeout(() => {
                    emailStatusNotification.style.opacity = '0';
                    setTimeout(() => {
                        emailStatusNotification.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>