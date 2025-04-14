-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 14, 2025 lúc 05:20 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webck`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `bookingID` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `area` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `totalAmount` decimal(12,2) NOT NULL,
  `bookingDate` date NOT NULL,
  `serviceID` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `customerID` int(11) NOT NULL,
  `employeeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `bookingID`, `user_id`, `service_id`, `booking_date`, `booking_time`, `address`, `area`, `note`, `price`, `status`, `created_at`, `updated_at`, `totalAmount`, `bookingDate`, `serviceID`, `amount`, `customerID`, `employeeID`) VALUES
(1, 77542316, 1, 1, '2025-04-14', '10-12', 'quận 8', 1, '', 20000.00, 'confirmed', '2025-04-13 05:12:08', '2025-04-13 05:12:08', 20000.00, '0000-00-00', 0, 20000.00, 1, 9),
(2, 11244902, 1, 1, '2025-04-14', '10-12', 'quận 8', 1, '', 20000.00, 'confirmed', '2025-04-13 05:45:38', '2025-04-13 05:45:38', 20000.00, '0000-00-00', 0, 20000.00, 2, 9),
(3, 73149267, 1, 2, '2025-04-15', '15-17', 'quận 7', 89, '', 2225000.00, 'completed', '2025-04-14 01:34:52', '2025-04-14 02:41:04', 2225000.00, '0000-00-00', 0, 2225000.00, 3, 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_employees`
--

CREATE TABLE `booking_employees` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_employees`
--

INSERT INTO `booking_employees` (`id`, `booking_id`, `employee_id`, `created_at`) VALUES
(1, 1, 9, '2025-04-13 05:12:08'),
(2, 2, 9, '2025-04-13 05:45:38'),
(3, 3, 10, '2025-04-14 01:34:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `customerID` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `registrationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`customerID`, `fullName`, `email`, `phone`, `address`, `latitude`, `longitude`, `registrationDate`) VALUES
(1, 'trọng', 'nguyenquoctrongbt2016@gmail.com', '0326097576', 'quận 8', NULL, NULL, '2025-04-13 05:12:08'),
(2, 'trọng', 'nguyenquoctrongbt2016@gmail.com', '0326097576', 'quận 8', NULL, NULL, '2025-04-13 05:45:38'),
(3, 'trọng', 'lyv05844@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-04-14 01:34:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `employeeID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `hireDate` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 5.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`employeeID`, `userID`, `department`, `hireDate`, `salary`, `age`, `specialization`, `experience`, `bio`, `rating`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nhân viên vệ sinh', '2023-01-15', 12000000.00, 28, 'Vệ sinh nhà ở', '3 năm', 'Anh Minh có kinh nghiệm vệ sinh nhà ở cao cấp với sự tỉ mỉ và chuyên nghiệp. Thành thạo các kỹ thuật vệ sinh hiện đại.', 4.8, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(2, 2, 'Nhân viên vệ sinh', '2023-02-20', 13000000.00, 32, 'Vệ sinh văn phòng', '5 năm', 'Chị Hương chuyên về vệ sinh văn phòng với kinh nghiệm quản lý đội ngũ vệ sinh cho nhiều công ty lớn tại TP.HCM.', 4.9, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(3, 3, 'Nhân viên vệ sinh', '2023-03-10', 11000000.00, 25, 'Vệ sinh nhà ở', '2 năm', 'Anh Hoàng có kỹ năng vệ sinh chuyên sâu cho các khu vực khó tiếp cận và am hiểu về các loại chất tẩy rửa thân thiện với môi trường.', 4.7, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(4, 4, 'Nhân viên vệ sinh', '2023-04-05', 12500000.00, 30, 'Vệ sinh văn phòng', '4 năm', 'Chị Lan có chuyên môn cao trong việc vệ sinh các văn phòng lớn, đặc biệt là khu vực tiếp đón khách hàng và phòng họp.', 4.8, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(5, 5, 'Nhân viên vệ sinh', '2023-05-12', 12000000.00, 27, 'Vệ sinh nhà ở', '3 năm', 'Anh Tú giỏi về vệ sinh khu vực bếp và phòng tắm, khéo léo trong việc xử lý các vết bẩn cứng đầu.', 4.7, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(6, 6, 'Nhân viên vệ sinh', '2023-06-18', 13500000.00, 34, 'Vệ sinh văn phòng', '6 năm', 'Chị Mai có nhiều năm kinh nghiệm vệ sinh các tòa nhà văn phòng cao tầng, chuyên về lau kính và vệ sinh không gian mở.', 4.9, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(7, 7, 'Nhân viên vệ sinh', '2023-07-22', 11500000.00, 26, 'Vệ sinh nhà ở', '2 năm', 'Anh Thắng có sở trường về vệ sinh nội thất và đồ gỗ, biết cách làm sạch mà không làm hỏng các bề mặt nhạy cảm.', 4.6, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(8, 8, 'Nhân viên vệ sinh', '2023-08-30', 12000000.00, 29, 'Vệ sinh văn phòng', '3 năm', 'Chị Hồng chuyên về vệ sinh văn phòng theo tiêu chuẩn quốc tế, am hiểu các quy trình vệ sinh hiện đại.', 4.8, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(9, 9, 'Nhân viên vệ sinh', '2023-09-15', 12500000.00, 31, 'Vệ sinh nhà ở', '4 năm', 'Anh Tuấn có kỹ năng vệ sinh chuyên sâu cho các biệt thự và căn hộ cao cấp, chú trọng đến từng chi tiết nhỏ.', 4.9, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(10, 10, 'Nhân viên vệ sinh', '2023-10-10', 13000000.00, 33, 'Vệ sinh văn phòng', '5 năm', 'Chị Thanh có kinh nghiệm quản lý đội ngũ vệ sinh cho các sự kiện lớn và văn phòng công ty đa quốc gia.', 4.7, '2025-04-12 15:26:50', '2025-04-12 15:26:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `display_order`, `is_active`, `created_at`, `updated_at`, `customer_email`) VALUES
(4, 'Phản hồi từ: Bob (Email: lyv05844@gmail.com, SĐT: 0944658990, Dịch vụ: home)', 'cccccc', 0, 1, '2025-04-13 17:41:01', '2025-04-13 18:46:18', NULL),
(5, 'Phản hồi từ: thanh vinh (Email: yugiohly@gmail.com, SĐT: 0992193110, Dịch vụ: home)', 'bnnnnnjjjj', 0, 1, '2025-04-13 17:54:03', '2025-04-13 17:54:03', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `financial_reports`
--

CREATE TABLE `financial_reports` (
  `id` int(11) NOT NULL,
  `report_key` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `report_data` longtext NOT NULL,
  `sent_date` datetime NOT NULL,
  `sent_by` varchar(100) NOT NULL,
  `status` enum('unread','read','approved','rejected') NOT NULL DEFAULT 'unread',
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `itemName` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `inventoryDate` datetime DEFAULT NULL,
  `inventory_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`itemName`, `quantity`, `inventoryDate`, `inventory_id`) VALUES
('Máy hút bụi', 1991, '2025-04-13 00:00:00', 1),
('Nước lau sàn', 100000, '2025-04-13 00:00:00', 2),
('Xô nước', 2888, '2025-04-13 00:00:00', 3),
('Khăn lau', 98987, '2025-04-13 00:00:00', 4),
('Máy Giặt Thảm', 90890, '2025-04-13 00:00:00', 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `invoiceID` int(11) NOT NULL,
  `supplierName` varchar(255) NOT NULL,
  `issueDate` date NOT NULL,
  `totalAmount` decimal(15,2) NOT NULL,
  `status` enum('Chưa thanh toán','Đã thanh toán','Đang xử lý','Hủy') NOT NULL DEFAULT 'Chưa thanh toán',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `invoices`
--

INSERT INTO `invoices` (`invoiceID`, `supplierName`, `issueDate`, `totalAmount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Công ty TNHH Vật tư vệ sinh Sạch Sẽ', '2025-04-01', 2500000.00, 'Đã thanh toán', '2025-04-13 07:34:44', '2025-04-13 07:34:44'),
(2, 'Công ty CP Hóa chất Tây Sơn', '2025-04-05', 3750000.00, 'Đã thanh toán', '2025-04-13 07:34:44', '2025-04-13 08:36:33'),
(3, 'Doanh nghiệp Thiết bị vệ sinh Việt Long', '2025-04-10', 1800000.00, 'Đã thanh toán', '2025-04-13 07:34:44', '2025-04-13 08:36:27'),
(4, 'Công ty TNHH Dụng cụ vệ sinh Hoàng Phát', '2025-04-12', 4200000.00, 'Đã thanh toán', '2025-04-13 07:34:44', '2025-04-13 08:36:37'),
(6, 'A', '2025-04-11', 123000.00, 'Chưa thanh toán', '2025-04-13 14:38:38', '2025-04-13 14:38:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `momo_logs`
--

CREATE TABLE `momo_logs` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `qr_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `momo_logs`
--

INSERT INTO `momo_logs` (`id`, `transaction_id`, `phone`, `amount`, `description`, `qr_content`, `created_at`) VALUES
(1, 'TR17445211269059', '0326097576', 20000.00, 'TT DV HOME BOOK17445211265504', '2|99|0326097576|theCleaner|TT DV HOME BOOK17445211265504|0|0|20000', '2025-04-13 05:12:06'),
(2, 'TR17445944893374', '0326097576', 2225000.00, 'TT DV OFFICE BOOK17445944888989', '2|99|0326097576|theCleaner|TT DV OFFICE BOOK17445944888989|0|0|2225000', '2025-04-14 01:34:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` enum('momo','bank_transfer','cash') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('pending','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_data` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`id`, `transaction_id`, `booking_id`, `payment_method`, `amount`, `status`, `payment_data`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 'TR17445211286608', 1, 'momo', 20000.00, 'completed', '{\"description\":\"Thanh to\\u00e1n d\\u1ecbch v\\u1ee5 home\"}', '2025-04-13 05:12:08', '2025-04-13 05:12:08', '2025-04-13 05:12:08'),
(3, 'TR17445944911649', 3, 'momo', 2225000.00, 'completed', '{\"description\":\"Thanh to\\u00e1n d\\u1ecbch v\\u1ee5 office\"}', '2025-04-14 01:34:52', '2025-04-14 01:34:52', '2025-04-14 01:34:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `consumed` int(11) NOT NULL,
  `remaining` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `report_date` date NOT NULL DEFAULT curdate(),
  `description` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reports`
--

INSERT INTO `reports` (`report_id`, `inventory_id`, `consumed`, `remaining`, `status`, `report_date`, `description`) VALUES
(1, 4, 1, 98999, 'Confirmed', '2025-04-13', ''),
(2, 4, 12, 98987, 'Confirmed', '2025-04-13', 'dùng để làm vệ sinh '),
(3, 1, 30, 1949, 'Confirmed', '2025-04-13', 'Phiếu nhập kho: thêm 30 đơn vị.'),
(4, 1, 12, 1961, 'Confirmed', '2025-04-13', 'Phiếu nhập kho: thêm 12 đơn vị.'),
(5, 1, 30, 1991, 'Confirmed', '2025-04-13', 'Phiếu nhập kho: thêm 30 đơn vị.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `serviceID` int(11) NOT NULL,
  `service_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `serviceName` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`serviceID`, `service_code`, `name`, `icon`, `description`, `is_active`, `created_at`, `updated_at`, `serviceName`, `price`) VALUES
(1, 'home', 'Vệ sinh nhà ở', 'fas fa-home', 'Dịch vụ vệ sinh toàn diện cho ngôi nhà của bạn, từ phòng khách, phòng ngủ đến nhà bếp và phòng tắm.', 1, '2025-04-12 15:26:50', '2025-04-12 15:26:50', '', 0.00),
(2, 'office', 'Vệ sinh văn phòng', 'fas fa-building', 'Dịch vụ vệ sinh chuyên nghiệp cho văn phòng, tạo môi trường làm việc sạch sẽ và thoải mái.', 1, '2025-04-12 15:26:50', '2025-04-12 15:26:50', '', 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `service_pricing`
--

CREATE TABLE `service_pricing` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `min_area` int(11) NOT NULL,
  `max_area` int(11) DEFAULT NULL,
  `pricing_type` enum('fixed','per_sqm') DEFAULT 'fixed',
  `base_price` decimal(12,2) NOT NULL,
  `additional_price` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `service_pricing`
--

INSERT INTO `service_pricing` (`id`, `service_id`, `min_area`, `max_area`, `pricing_type`, `base_price`, `additional_price`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 50, 'per_sqm', 20000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(2, 1, 50, 100, 'per_sqm', 16000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(3, 1, 100, NULL, 'per_sqm', 14000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(4, 2, 0, 100, 'per_sqm', 25000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(5, 2, 100, 300, 'per_sqm', 22000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50'),
(6, 2, 300, NULL, 'per_sqm', 20000.00, NULL, '2025-04-12 15:26:50', '2025-04-12 15:26:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuchi`
--

CREATE TABLE `thuchi` (
  `thuchiID` int(11) NOT NULL,
  `loai` varchar(50) NOT NULL COMMENT 'Thu hoặc Chi',
  `ngayGiaoDich` date NOT NULL,
  `noiDung` text NOT NULL,
  `soTien` decimal(15,2) NOT NULL,
  `hinhThuc` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thuchi`
--

INSERT INTO `thuchi` (`thuchiID`, `loai`, `ngayGiaoDich`, `noiDung`, `soTien`, `hinhThuc`, `created_at`, `updated_at`) VALUES
(1, 'Thu', '2025-04-13', 'GỌi vốn', 5000000.00, 'Chuyển khoản', '2025-04-13 09:19:25', '2025-04-13 09:19:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transaction_history`
--

CREATE TABLE `transaction_history` (
  `transactionID` int(11) NOT NULL,
  `employeeID` int(11) NOT NULL,
  `transactionTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `transactionType` varchar(255) NOT NULL COMMENT 'Example: Login, Add Invoice, Edit Information, ...',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `transaction_history`
--

INSERT INTO `transaction_history` (`transactionID`, `employeeID`, `transactionTime`, `transactionType`, `description`) VALUES
(1, 9, '2025-04-13 05:12:08', 'AssignCleaning', 'Phân công nhân viên Hoàng Minh Tuấn (ID: 9) cho đặt lịch #1'),
(2, 9, '2025-04-13 05:45:38', 'AssignCleaning', 'Phân công nhân viên Hoàng Minh Tuấn (ID: 9) cho đặt lịch #2'),
(3, 10, '2025-04-14 01:34:52', 'AssignCleaning', 'Phân công nhân viên Lý Thị Thanh (ID: 10) cho đặt lịch #3');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'images/default-avatar.jpg',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('Admin','Employee','WarehouseStaff','Cleaner','Accountant','Consultant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`userID`, `fullName`, `password`, `email`, `phone`, `address`, `gender`, `avatar`, `status`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Nguyễn Văn Minh', 'cleaner123', 'minh.nguyen@example.com', '0901234567', 'Quận 1, TP. Hồ Chí Minh', 'Nam', 'images/cleaner1.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(2, 'Trần Thị Hương', 'cleaner123', 'huong.tran@example.com', '0912345678', 'Quận 3, TP. Hồ Chí Minh', 'Nữ', 'images/cleaner2.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(3, 'Lê Văn Hoàng', 'cleaner123', 'hoang.le@example.com', '0923456789', 'Quận 7, TP. Hồ Chí Minh', 'Nam', 'images/cleaner3.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(4, 'Phạm Thị Lan', 'cleaner123', 'lan.pham@example.com', '0934567890', 'Quận Bình Thạnh, TP. Hồ Chí Minh', 'Nữ', 'images/cleaner4.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(5, 'Đặng Minh Tú', 'cleaner123', 'tu.dang@example.com', '0945678901', 'Quận Phú Nhuận, TP. Hồ Chí Minh', 'Nam', 'images/cleaner5.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(6, 'Vũ Thị Mai', 'cleaner123', 'mai.vu@example.com', '0956789012', 'Quận 10, TP. Hồ Chí Minh', 'Nữ', 'images/cleaner6.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(7, 'Ngô Đức Thắng', 'cleaner123', 'thang.ngo@example.com', '0967890123', 'Quận 5, TP. Hồ Chí Minh', 'Nam', 'images/cleaner7.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(8, 'Trương Thị Hồng', 'cleaner123', 'hong.truong@example.com', '0978901234', 'Quận 9, TP. Hồ Chí Minh', 'Nữ', 'images/cleaner8.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(9, 'Hoàng Minh Tuấn', 'cleaner123', 'tuan.hoang@example.com', '0989012345', 'Quận 4, TP. Hồ Chí Minh', 'Nam', 'images/cleaner9.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(10, 'Lý Thị Thanh', 'cleaner123', 'thanh.ly@example.com', '0990123456', 'Quận 2, TP. Hồ Chí Minh', 'Nữ', 'images/cleaner10.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Cleaner'),
(11, 'Admin', 'admin123', 'admin@thecleaner.com', '0123456789', 'TP. Hồ Chí Minh', 'Nam', 'images/admin.jpg', 'active', '2025-04-12 15:26:50', '2025-04-12 15:26:50', 'Admin'),
(12, 'Nguyễn Tuấn Khanh', '123', 'nguyenquoctrong12a1@gmail.com', '0326097576', 'Long An', NULL, 'images/default-avatar.jpg', 'active', '2025-04-13 04:19:28', '2025-04-13 04:19:28', 'WarehouseStaff'),
(15, 'Trong Nguyen', '123', '52200201@gmail.com', '0326097576', '416, QUI DIEN A, THANH PHU DONG, GIONG T ROM, BEN TRE; 416, QUI DIEN A, THANH PHU Dong, Giong Trom, Ben Tre', NULL, 'images/default-avatar.jpg', 'active', '2025-04-13 04:40:57', '2025-04-13 04:40:57', 'Cleaner'),
(16, 'Vinh', '123', 'vinhkhung@ws.com', '0326097576', '416, QUI DIEN A, THANH PHU DONG, GIONG T ROM, BEN TRE; 416, QUI DIEN A, THANH PHU Dong, Giong Trom, Ben Tre', NULL, 'images/default-avatar.jpg', 'active', '2025-04-13 04:51:54', '2025-04-13 04:51:54', 'WarehouseStaff'),
(17, 'Trong Nguyen', '123', '52200197@gmail.com', '0326097576', '416, QUI DIEN A, THANH PHU DONG, GIONG T ROM, BEN TRE; 416, QUI DIEN A, THANH PHU Dong, Giong Trom, Ben Tre', NULL, 'images/default-avatar.jpg', 'active', '2025-04-13 04:52:39', '2025-04-13 04:52:39', 'Accountant'),
(18, 'Trọng XCCS', '123', 'xccs@gmail.com', '0944856943', 'Quận 8', NULL, 'images/default-avatar.jpg', 'active', '2025-04-13 09:25:30', '2025-04-13 09:25:30', 'Consultant');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `employeeID` (`employeeID`),
  ADD KEY `customerID` (`customerID`);

--
-- Chỉ mục cho bảng `booking_employees`
--
ALTER TABLE `booking_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id_employee_id` (`booking_id`,`employee_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerID`);

--
-- Chỉ mục cho bảng `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employeeID`),
  ADD KEY `userID` (`userID`);

--
-- Chỉ mục cho bảng `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_key` (`report_key`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`);

--
-- Chỉ mục cho bảng `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoiceID`);

--
-- Chỉ mục cho bảng `momo_logs`
--
ALTER TABLE `momo_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_inventory` (`inventory_id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`serviceID`),
  ADD UNIQUE KEY `service_code` (`service_code`);

--
-- Chỉ mục cho bảng `service_pricing`
--
ALTER TABLE `service_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Chỉ mục cho bảng `thuchi`
--
ALTER TABLE `thuchi`
  ADD PRIMARY KEY (`thuchiID`);

--
-- Chỉ mục cho bảng `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `employeeID` (`employeeID`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `booking_employees`
--
ALTER TABLE `booking_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `momo_logs`
--
ALTER TABLE `momo_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `serviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `service_pricing`
--
ALTER TABLE `service_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `thuchi`
--
ALTER TABLE `thuchi`
  MODIFY `thuchiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`serviceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`employeeID`) REFERENCES `employees` (`employeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_employees`
--
ALTER TABLE `booking_employees`
  ADD CONSTRAINT `booking_employees_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employeeID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`);

--
-- Các ràng buộc cho bảng `service_pricing`
--
ALTER TABLE `service_pricing`
  ADD CONSTRAINT `service_pricing_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`serviceID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `transaction_history`
--
ALTER TABLE `transaction_history`
  ADD CONSTRAINT `transaction_history_ibfk_1` FOREIGN KEY (`employeeID`) REFERENCES `employees` (`employeeID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
