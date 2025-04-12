-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 11, 2025 lúc 05:55 PM
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
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('admin','manager','staff') DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 392090, 1, 1, '2025-04-11', '13-15', 'quận 7', 89, '', 800000.00, 'confirmed', '2025-04-10 07:22:23', '2025-04-10 07:22:23', 800000.00, '2025-04-11', 1, 800000.00, 2, 1),
(2, 222126, 1, 2, '2025-04-12', '13-15', 'quận 7', 88, '', 1320000.00, 'confirmed', '2025-04-10 07:42:29', '2025-04-10 07:42:29', 1320000.00, '2025-04-12', 2, 1320000.00, 3, 1),
(3, 822604, 1, 2, '2025-04-12', '13-15', 'quận 7', 88, '', 1320000.00, 'confirmed', '2025-04-10 07:53:48', '2025-04-10 07:53:48', 1320000.00, '2025-04-12', 2, 1320000.00, 4, 1),
(4, 143622, 1, 1, '2025-04-11', '15-17', 'quận 8', 80, '', 800000.00, 'confirmed', '2025-04-10 07:54:26', '2025-04-10 07:54:26', 800000.00, '2025-04-11', 1, 800000.00, 5, 1),
(5, 723661, 1, 1, '2025-04-11', '15-17', 'quận 8', 80, '', 800000.00, 'confirmed', '2025-04-10 07:58:29', '2025-04-10 07:58:29', 800000.00, '2025-04-11', 1, 800000.00, 6, 1),
(6, 709459, 1, 2, '2025-04-11', '15-17', 'quận 7', 88, '', 1320000.00, 'confirmed', '2025-04-10 07:59:20', '2025-04-10 07:59:20', 1320000.00, '2025-04-11', 2, 1320000.00, 7, 1),
(7, 440695, 1, 1, '2025-04-11', '13-15', 'quận 9', 89, '', 800000.00, 'confirmed', '2025-04-10 08:05:39', '2025-04-10 08:05:39', 800000.00, '2025-04-11', 1, 800000.00, 8, 1),
(8, 218738, 1, 1, '2025-04-11', '13-15', 'quận 9', 89, '', 800000.00, 'confirmed', '2025-04-10 08:10:29', '2025-04-10 08:10:29', 800000.00, '2025-04-11', 1, 800000.00, 9, 1),
(9, 769149, 1, 1, '2025-04-11', '10-12', 'quận bình thạnh', 90, '', 800000.00, 'confirmed', '2025-04-10 08:11:08', '2025-04-10 08:11:08', 800000.00, '2025-04-11', 1, 800000.00, 10, 1),
(10, 861882, 1, 1, '2025-04-11', '10-12', 'quận bình thạnh', 90, '', 800000.00, 'confirmed', '2025-04-10 16:37:54', '2025-04-10 16:37:54', 800000.00, '2025-04-11', 1, 800000.00, 11, 1),
(11, 642159, 1, 1, '2025-04-11', '10-12', 'quận bình thạnh', 90, '', 800000.00, 'confirmed', '2025-04-10 18:26:47', '2025-04-10 18:26:47', 800000.00, '2025-04-11', 1, 800000.00, 12, 1),
(12, 638686, 1, 1, '2025-04-11', '10-12', 'quận bình thạnh', 90, '', 800000.00, 'confirmed', '2025-04-10 18:27:51', '2025-04-10 18:27:51', 800000.00, '2025-04-11', 1, 800000.00, 13, 1),
(13, 856355, 1, 2, '2025-04-11', '10-12', 'quận 7', 89, '', 1335000.00, 'confirmed', '2025-04-10 18:33:11', '2025-04-10 18:33:11', 1335000.00, '2025-04-11', 2, 1335000.00, 14, 1),
(14, 140851, 1, 2, '2025-04-11', '10-12', 'quận 7', 89, '', 1335000.00, 'confirmed', '2025-04-10 18:36:23', '2025-04-10 18:36:23', 1335000.00, '2025-04-11', 2, 1335000.00, 15, 1);

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
(1, 1, 1, '2025-04-10 07:22:23'),
(2, 2, 1, '2025-04-10 07:42:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cleanerinventory`
--

CREATE TABLE `cleanerinventory` (
  `id` int(11) NOT NULL,
  `employeeID` int(11) DEFAULT NULL,
  `itemName` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `inventoryDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cleaning_staff`
--

CREATE TABLE `cleaning_staff` (
  `employeeID` int(11) NOT NULL,
  `specialization_detail` varchar(255) DEFAULT NULL,
  `certification` varchar(255) DEFAULT NULL,
  `rating_details` text DEFAULT NULL,
  `total_bookings` int(11) DEFAULT 0,
  `feedback_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating` decimal(3,1) DEFAULT 5.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Bẫy `cleaning_staff`
--
DELIMITER $$
CREATE TRIGGER `update_employee_rating` AFTER UPDATE ON `cleaning_staff` FOR EACH ROW BEGIN
    UPDATE
        employees
    SET
        rating = NEW.rating
    WHERE
        employeeID = NEW.employeeID ;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','in_progress','completed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Vinhk', 'vinh123@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-03-31 04:14:15'),
(2, 'Trọng balckpink', 'lyv05844@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-04-10 07:22:23'),
(3, 'Minh IT', 'yugiohly@gmail.com', '0944848668', 'quận 7', NULL, NULL, '2025-04-10 07:42:29'),
(4, 'Minh IT', 'yugiohly@gmail.com', '0944848668', 'quận 7', NULL, NULL, '2025-04-10 07:53:48'),
(5, 'Khanh BigBang', 'lyv05844@gmail.com', '0944841698', 'quận 8', NULL, NULL, '2025-04-10 07:54:26'),
(6, 'Khanh BigBang', 'lyv05844@gmail.com', '0944841698', 'quận 8', NULL, NULL, '2025-04-10 07:58:29'),
(7, 'Khanh BigBang', 'lyv05844@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-04-10 07:59:20'),
(8, 'Đại CKSG', 'lyv05844@gmail.com', '0944841698', 'quận 9', NULL, NULL, '2025-04-10 08:05:39'),
(9, 'Đại CKSG', 'lyv05844@gmail.com', '0944841698', 'quận 9', NULL, NULL, '2025-04-10 08:10:29'),
(10, 'Trọng balckpink lisa', 'lyv05844@gmail.com', '0944848904', 'quận bình thạnh', NULL, NULL, '2025-04-10 08:11:08'),
(11, 'Trọng balckpink lisa', 'lyv05844@gmail.com', '0944848904', 'quận bình thạnh', NULL, NULL, '2025-04-10 16:37:54'),
(12, 'Trọng balckpink lisa', 'lyv05844@gmail.com', '0944848904', 'quận bình thạnh', NULL, NULL, '2025-04-10 18:26:47'),
(13, 'Trọng balckpink lisa', 'lyv05844@gmail.com', '0944848904', 'quận bình thạnh', NULL, NULL, '2025-04-10 18:27:51'),
(14, 'Trọng balckpink', 'lyv05844@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-04-10 18:33:11'),
(15, 'Trọng balckpink', 'lyv05844@gmail.com', '0944841668', 'quận 7', NULL, NULL, '2025-04-10 18:36:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `employeeID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `hireDate` date DEFAULT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'images/default-avatar.jpg',
  `bio` text DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 5.0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`employeeID`, `userID`, `department`, `hireDate`, `fullName`, `email`, `phone`, `address`, `salary`, `gender`, `age`, `specialization`, `experience`, `avatar`, `bio`, `rating`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 'Nhân viên vệ sinh', '2025-03-14', 'Nguyễn Văn A', 'cleaner@domain.com', '0904567890', 'Hà Nội', 15000000.00, 'Nam', 28, 'Vệ sinh nhà ở', '5 năm', 'images/team1.jpg', 'Với hơn 15 năm kinh nghiệm trong ngành dịch vụ vệ sinh, anh A đã xây dựng và phát triển theCleaner từ một doanh nghiệp nhỏ thành công ty hàng đầu trong lĩnh vực.', 4.9, 'active', '2025-04-08 11:45:59', '2025-04-10 18:36:23'),
(2, NULL, 'Nhân viên vệ sinh', '2025-03-19', 'Trần Thị B', 'tranthib@example.com', '0923456789', 'Đà Nẵng', 12000000.00, 'Nữ', 32, 'Vệ sinh văn phòng', '7 năm', 'images/team2.jpg', 'Chị B quản lý đội ngũ và quy trình vận hành, đảm bảo chất lượng dịch vụ luôn ở mức cao nhất. Với kinh nghiệm quản lý hơn 10 năm, chị đã xây dựng quy trình vận hành hiệu quả.', 4.8, 'active', '2025-04-08 11:45:59', '2025-04-10 18:36:23'),
(3, NULL, 'Nhân viên vệ sinh', '2025-04-30', 'Lê Văn C', 'warehouse@domain.com', '0903456789', 'Đà Nẵng', 10000000.00, 'Nam', 25, 'Vệ sinh nhà ở', '3 năm', 'images/team3.jpg', 'Anh C chịu trách nhiệm đảm bảo sự hài lòng của khách hàng, xử lý phản hồi và không ngừng cải thiện chất lượng dịch vụ dựa trên ý kiến của khách hàng.', 4.7, 'active', '2025-04-08 11:45:59', '2025-04-10 18:36:23'),
(4, 5, 'Nhân viên kế toán', '2025-04-18', 'Phan Thị E', 'accountant@domain.com', '0905678901', 'Cần Thơ', 11000000.00, NULL, NULL, NULL, NULL, 'images/default-avatar.jpg', NULL, 5.0, 'active', '2025-04-08 11:45:59', '2025-04-10 18:36:23'),
(5, 6, 'Nhân viên tư vấn', '2025-04-23', 'Trương Quốc F', 'consultant@domain.com', '0906789012', 'Bình Dương', 9000000.00, NULL, NULL, NULL, NULL, 'images/default-avatar.jpg', NULL, 5.0, 'active', '2025-04-08 11:45:59', '2025-04-10 18:36:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `itemName` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `inventoryDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `invoices`
--

CREATE TABLE `invoices` (
  `invoiceID` int(11) NOT NULL,
  `supplierName` varchar(255) NOT NULL,
  `issueDate` date NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `materials`
--

CREATE TABLE `materials` (
  `materialID` int(11) NOT NULL,
  `materialName` varchar(255) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `quantityInStock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rentals`
--

CREATE TABLE `rentals` (
  `rentalID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL,
  `rentalDate` date NOT NULL,
  `rentalTime` varchar(20) NOT NULL,
  `area` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `serviceName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rental_materials`
--

CREATE TABLE `rental_materials` (
  `rentalID` int(11) NOT NULL,
  `materialID` int(11) NOT NULL,
  `quantityUsed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `reportID` int(11) NOT NULL,
  `reportName` varchar(255) DEFAULT NULL,
  `reportDate` date DEFAULT NULL,
  `reportContent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `reviewID` int(11) NOT NULL,
  `customerID` int(11) DEFAULT NULL,
  `rentalID` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'home', 'Vệ sinh nhà ở', 'fas fa-home', 'Dịch vụ vệ sinh toàn diện cho ngôi nhà của bạn, từ phòng khách, phòng ngủ đến nhà bếp và phòng tắm.', 1, '2025-04-08 11:45:59', '2025-04-08 11:45:59', '', 0.00),
(2, 'office', 'Vệ sinh văn phòng', 'fas fa-building', 'Dịch vụ vệ sinh chuyên nghiệp cho văn phòng, tạo môi trường làm việc sạch sẽ và thoải mái.', 1, '2025-04-08 11:45:59', '2025-04-08 11:45:59', '', 0.00);

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
(1, 1, 0, 50, 'fixed', 500000.00, NULL, '2025-04-08 11:45:59', '2025-04-08 11:45:59'),
(2, 1, 50, 100, 'fixed', 800000.00, NULL, '2025-04-08 11:45:59', '2025-04-08 11:45:59'),
(3, 1, 100, NULL, 'fixed', 1000000.00, 8000.00, '2025-04-08 11:45:59', '2025-04-08 11:45:59'),
(4, 2, 0, 100, 'per_sqm', 15000.00, NULL, '2025-04-08 11:45:59', '2025-04-08 11:45:59'),
(5, 2, 100, 300, 'per_sqm', 13000.00, NULL, '2025-04-08 11:45:59', '2025-04-08 11:45:59'),
(6, 2, 300, NULL, 'per_sqm', 11000.00, NULL, '2025-04-08 11:45:59', '2025-04-08 11:45:59');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('Admin','Employee','WarehouseStaff','Cleaner','Accountant','Consultant') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`userID`, `fullName`, `password`, `email`, `phone`, `address`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Nguyễn Văn A', 'admin123', 'admin@domain.com', '0901234567', 'Hà Nội', '2025-04-08 12:16:15', '2025-04-08 12:16:15', 'Admin'),
(3, 'Lê Văn C', 'warehouse123', 'warehouse@domain.com', '0903456789', 'Đà Nẵng', '2025-04-08 12:16:15', '2025-04-08 12:16:15', 'WarehouseStaff'),
(4, 'Nguyễn Quốc D', 'cleaner123', 'cleaner@domain.com', '0904567890', 'Hải Phòng', '2025-04-08 12:16:15', '2025-04-08 12:16:15', 'Cleaner'),
(5, 'Phan Thị E', 'accountant123', 'accountant@domain.com', '0905678901', 'Cần Thơ', '2025-04-08 12:16:15', '2025-04-08 12:16:15', 'Accountant'),
(6, 'Trương Quốc F', 'consultant123', 'consultant@domain.com', '0906789012', 'Bình Dương', '2025-04-08 12:16:15', '2025-04-08 12:16:15', 'Consultant');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

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
-- Chỉ mục cho bảng `cleanerinventory`
--
ALTER TABLE `cleanerinventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employeeID` (`employeeID`);

--
-- Chỉ mục cho bảng `cleaning_staff`
--
ALTER TABLE `cleaning_staff`
  ADD PRIMARY KEY (`employeeID`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

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
-- Chỉ mục cho bảng `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`materialID`);

--
-- Chỉ mục cho bảng `momo_logs`
--
ALTER TABLE `momo_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Chỉ mục cho bảng `rentals`
--
ALTER TABLE `rentals`
  ADD KEY `customerID` (`customerID`),
  ADD KEY `serviceID` (`serviceID`);

--
-- Chỉ mục cho bảng `rental_materials`
--
ALTER TABLE `rental_materials`
  ADD PRIMARY KEY (`rentalID`,`materialID`),
  ADD KEY `materialID` (`materialID`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`reportID`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`reviewID`),
  ADD KEY `customerID` (`customerID`),
  ADD KEY `rentalID` (`rentalID`);

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
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `booking_employees`
--
ALTER TABLE `booking_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cleanerinventory`
--
ALTER TABLE `cleanerinventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `materials`
--
ALTER TABLE `materials`
  MODIFY `materialID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `momo_logs`
--
ALTER TABLE `momo_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `serviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `service_pricing`
--
ALTER TABLE `service_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `thuchi`
--
ALTER TABLE `thuchi`
  MODIFY `thuchiID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `transaction_history`
--
ALTER TABLE `transaction_history`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Các ràng buộc cho bảng `cleanerinventory`
--
ALTER TABLE `cleanerinventory`
  ADD CONSTRAINT `cleanerinventory_ibfk_1` FOREIGN KEY (`employeeID`) REFERENCES `employees` (`employeeID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cleaning_staff`
--
ALTER TABLE `cleaning_staff`
  ADD CONSTRAINT `cleaning_staff_ibfk_1` FOREIGN KEY (`employeeID`) REFERENCES `employees` (`employeeID`) ON DELETE CASCADE;

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
-- Các ràng buộc cho bảng `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`serviceID`) REFERENCES `services` (`serviceID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `rental_materials`
--
ALTER TABLE `rental_materials`
  ADD CONSTRAINT `rental_materials_ibfk_1` FOREIGN KEY (`materialID`) REFERENCES `materials` (`materialID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customers` (`customerID`) ON DELETE SET NULL;

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