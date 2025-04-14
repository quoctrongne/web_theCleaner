# theCleaner - Website Dịch Vụ Vệ Sinh Chuyên Nghiệp

Website quản lý dịch vụ vệ sinh chuyên nghiệp với đầy đủ chức năng đặt lịch, thanh toán và quản lý.

## Cài đặt

### Yêu cầu hệ thống
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Composer (để quản lý thư viện PHP)

### Các bước cài đặt

1. Clone repository về máy:
```bash
git clone https://github.com/yourusername/thecleaner.git
cd thecleaner
```

2. Cài đặt các thư viện PHP thông qua Composer:
```bash
composer install
```

3. Tạo database và import cấu trúc:
```bash
mysql -u root -p
CREATE DATABASE thecleaner;
exit;
mysql -u root -p thecleaner < database.sql
```

4. Cấu hình kết nối database:
   Mở file `database_connection.php` và cập nhật thông tin kết nối:
```php
$db_host = 'localhost';  // Địa chỉ database server
$db_name = 'thecleaner'; // Tên database
$db_user = 'root';       // Tên đăng nhập
$db_pass = '';           // Mật khẩu
```

5. Cấu hình email (để gửi thông báo):
   Mở file `config.php` và cập nhật thông tin email:
```php
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your_email@gmail.com');
define('MAIL_PASSWORD', 'your_app_password');
```

6. Thiết lập quyền thư mục uploads (cho Unix/Linux):
```bash
mkdir -p uploads/testimonials
chmod -R 755 uploads
```

7. Truy cập vào website thông qua webserver (Apache/Nginx).

## Cấu trúc Database

Website sử dụng cơ sở dữ liệu MySQL với các bảng chính sau:

1. `users` - Lưu thông tin khách hàng
2. `services` - Lưu thông tin dịch vụ
3. `service_pricing` - Lưu bảng giá theo dịch vụ và diện tích
4. `bookings` - Lưu thông tin đặt lịch
5. `payments` - Lưu thông tin thanh toán
6. `staff` - Lưu thông tin nhân viên
7. `testimonials` - Lưu đánh giá khách hàng
8. `newsletter_subscribers` - Lưu đăng ký nhận bản tin
9. `contacts` - Lưu tin nhắn liên hệ
10. `faqs` - Lưu câu hỏi thường gặp
11. `configurations` - Lưu cấu hình hệ thống

## Các chức năng chính

### Frontend

1. **Trang chủ**: Giới thiệu dịch vụ, thống kê, đánh giá nổi bật
2. **Dịch vụ**: Thông tin chi tiết về các dịch vụ, bảng giá
3. **Về chúng tôi**: Giới thiệu công ty, đội ngũ, thành tựu
4. **Đánh giá**: Hiển thị và cho phép khách hàng gửi đánh giá
5. **Liên hệ**: Form liên hệ, thông tin chi nhánh, FAQs
6. **Đặt lịch**: Form đặt lịch dịch vụ
7. **Thanh toán**: Thanh toán qua MoMo
8. **Xác nhận đặt lịch**: Thông tin chi tiết sau khi đặt lịch và thanh toán

### Backend (To be developed)

1. **Quản lý đặt lịch**: Xem, duyệt, hủy đơn đặt lịch
2. **Quản lý dịch vụ**: Thêm, sửa, xóa dịch vụ và bảng giá
3. **Quản lý nhân viên**: Thêm, sửa, xóa thông tin nhân viên
4. **Quản lý đánh giá**: Duyệt và quản lý đánh giá khách hàng
5. **Quản lý liên hệ**: Xem và phản hồi tin nhắn liên hệ
6. **Quản lý người dùng**: Thêm, sửa, xóa thông tin khách hàng
7. **Cấu hình hệ thống**: Thay đổi thông tin công ty, cài đặt thanh toán

## Các tệp chính

- `index.php`: Trang chủ
- `services.php`: Trang dịch vụ
- `about.php`: Trang giới thiệu
- `testimonials.php`: Trang đánh giá
- `contact.php`: Trang liên hệ
- `booking.php`: Trang đặt lịch
- `payment.php`: Trang thanh toán
- `payment_confirmation.php`: Trang xác nhận đặt lịch
- `process_booking.php`: Xử lý đặt lịch
- `process_newsletter.php`: Xử lý đăng ký bản tin
- `process_testimonial.php`: Xử lý gửi đánh giá
- `momo_qr_generator.php`: Tạo QR code thanh toán MoMo
- `send_email.php`: Xử lý gửi email
- `database_connection.php`: Kết nối database và các hàm truy vấn
- `config.php`: Cấu hình hệ thống

## Các thư viện sử dụng

- **PHPMailer**: Gửi email thông báo
- **Font Awesome**: Icon cho giao diện
- **Google Maps**: Hiển thị bản đồ chi nhánh

## Hỗ trợ

Nếu bạn có câu hỏi hoặc cần hỗ trợ, vui lòng liên hệ:
- Email: support@thecleaner.com
- Điện thoại: +84 123 456 789