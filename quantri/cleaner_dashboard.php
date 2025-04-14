<?php
session_start();

// Kiểm tra quyền truy cập cho nhân viên Cleaner
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Cleaner') {
    header("Location: login.php");
    exit();
}

// Kết nối đến CSDL
require_once("../db/conn.php");

// Lấy Cleaner ID từ session (giả sử trong session lưu trường userID của bảng users)
$cleanerID = isset($_SESSION['user']['userID']) ? $_SESSION['user']['userID'] : 0;
if ($cleanerID == 0) {
    die("Không tìm thấy thông tin Cleaner trong session.");
}

// Truy vấn bảng bookings để lấy các đơn hàng được giao cho Cleaner
$sql = "SELECT bookingID, booking_date, booking_time, address, area, totalAmount, status 
        FROM bookings 
        WHERE employeeID = ? 
        ORDER BY booking_date ASC, booking_time ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}
$stmt->bind_param("i", $cleanerID);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch làm việc của Cleaner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Liên kết CSS: Bootstrap, FontAwesome và CSS tùy chỉnh -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar cho Cleaner -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="cleaner_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-broom"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Vệ sinh</div>
            </a>
            <hr class="sidebar-divider my-0">
            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="cleaner_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Bảng Điều Khiển</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="cleaner_dashboard.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Lịch làm việc</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar cho Cleaner -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Thông tin người dùng -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : "Cleaner"; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown thông tin người dùng -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Trang cá nhân
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End Topbar -->

                <!-- Nội dung chính -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Lịch làm việc của bạn</h1>
                    <?php if (!empty($tasks)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Booking ID</th>
                                        <th>Ngày</th>
                                        <th>Giờ</th>
                                        <th>Địa chỉ</th>
                                        <th>Khu vực</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($task['bookingID']); ?></td>
                                            <td><?php echo htmlspecialchars($task['booking_date']); ?></td>
                                            <td><?php echo htmlspecialchars($task['booking_time']); ?></td>
                                            <td><?php echo htmlspecialchars($task['address']); ?></td>
                                            <td><?php echo htmlspecialchars($task['area']); ?></td>
                                            <td><?php echo number_format($task['totalAmount'], 0, ".", ","); ?></td>
                                            <td>
                                                <?php if ($task['status'] !== 'completed'): ?>
                                                    <form method="POST" action="complete_booking.php">
                                                        <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($task['bookingID']); ?>">
                                                        <button type="submit" class="btn btn-sm btn-warning">Chưa hoàn thành</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Hoàn thành</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">Không có công việc nào được giao.</p>
                    <?php endif; ?>
                </div>
                <!-- End Main Content -->
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
        <!-- End Content Wrapper -->
    </div>
    <!-- End Page Wrapper -->

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
