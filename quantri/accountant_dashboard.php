<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập cho nhân viên kế toán
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once("../db/conn.php");

// Lấy thông tin thống kê cho kế toán (đơn dịch vụ)
$totalBookings = 0;
$pendingPayments = 0;
$completedPayments = 0;
$processingBookings = 0;

// Truy vấn tổng số đơn dịch vụ
$sqlTotalBookings = "SELECT COUNT(*) AS total FROM bookings";
$resultTotalBookings = $conn->query($sqlTotalBookings);
if ($resultTotalBookings && $resultTotalBookings->num_rows > 0) {
    $rowTotalBookings = $resultTotalBookings->fetch_assoc();
    $totalBookings = $rowTotalBookings['total'];
}

// Truy vấn số lượng đơn chưa thanh toán
$sqlPendingPayments = "SELECT COUNT(*) AS total FROM bookings WHERE status = 'pending'";
$resultPendingPayments = $conn->query($sqlPendingPayments);
if ($resultPendingPayments && $resultPendingPayments->num_rows > 0) {
    $rowPendingPayments = $resultPendingPayments->fetch_assoc();
    $pendingPayments = $rowPendingPayments['total'];
}

// Truy vấn số lượng đơn đã thanh toán hoàn tất
$sqlCompletedPayments = "SELECT COUNT(*) AS total FROM bookings WHERE status = 'completed'";
$resultCompletedPayments = $conn->query($sqlCompletedPayments);
if ($resultCompletedPayments && $resultCompletedPayments->num_rows > 0) {
    $rowCompletedPayments = $resultCompletedPayments->fetch_assoc();
    $completedPayments = $rowCompletedPayments['total'];
}

// Truy vấn số lượng đơn đang được xử lý
$sqlProcessingBookings = "SELECT COUNT(*) AS total FROM bookings WHERE status = 'confirmed'";
$resultProcessingBookings = $conn->query($sqlProcessingBookings);
if ($resultProcessingBookings && $resultProcessingBookings->num_rows > 0) {
    $rowProcessingBookings = $resultProcessingBookings->fetch_assoc();
    $processingBookings = $rowProcessingBookings['total'];
}

// Lấy tổng doanh thu
$totalRevenue = 0;
$sqlTotalRevenue = "SELECT SUM(totalAmount) AS total FROM bookings WHERE status IN ('completed', 'confirmed')";
$resultTotalRevenue = $conn->query($sqlTotalRevenue);
if ($resultTotalRevenue && $resultTotalRevenue->num_rows > 0) {
    $rowTotalRevenue = $resultTotalRevenue->fetch_assoc();
    $totalRevenue = $rowTotalRevenue['total'] ?? 0;
}

// Lấy tổng chi phí từ bảng thuchi
$expensesFromThuChi = 0;
$sqlThuChi = "SELECT SUM(soTien) AS total FROM thuchi WHERE loai = 'Chi'";
$resultThuChi = $conn->query($sqlThuChi);
if ($resultThuChi && $resultThuChi->num_rows > 0) {
    $rowThuChi = $resultThuChi->fetch_assoc();
    $expensesFromThuChi = $rowThuChi['total'] ?? 0;
}

// Lấy tổng chi phí từ hóa đơn (invoices)
$expensesFromInvoices = 0;
$sqlInvoices = "SELECT SUM(totalAmount) AS total FROM invoices WHERE status = 'Đã thanh toán'";
$resultInvoices = $conn->query($sqlInvoices);
if ($resultInvoices && $resultInvoices->num_rows > 0) {
    $rowInvoices = $resultInvoices->fetch_assoc();
    $expensesFromInvoices = $rowInvoices['total'] ?? 0;
}

// Tính tổng chi phí
$totalExpenses = $expensesFromThuChi + $expensesFromInvoices;

// Tính lợi nhuận
$profit = $totalRevenue - $totalExpenses;

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ Kế toán - TheCleaner</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="accountant_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kế toán</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="accountant_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Quản lý hóa đơn -->
            <li class="nav-item">
                <a class="nav-link" href="managerment_invoice.php">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Quản lý hóa đơn</span>
                </a>
            </li>

            <!-- Nav Item - Quản lý thu chi -->
            <li class="nav-item">
                <a class="nav-link" href="managerment_income_expenditure.php">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Quản lý thu chi</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['user']['fullName']; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Hồ sơ
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Tổng quan Kế toán</h1>
                    </div>

                    <!-- Content Row - Dashboard Cards -->
                    <div class="row">
                        <!-- Tổng số đơn dịch vụ Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Tổng số đơn dịch vụ</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalBookings; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Đơn chưa thanh toán Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Chờ xác nhận</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pendingPayments; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Đơn đã hoàn thành Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Đã hoàn thành</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $completedPayments; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Đơn đang xử lý Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Đã xác nhận</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $processingBookings; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê tài chính -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Tổng kết tài chính</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <div class="card border-left-primary h-100">
                                                <div class="card-body">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Tổng doanh thu</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> VND</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card border-left-danger h-100">
                                                <div class="card-body">
                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                        Tổng chi phí</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalExpenses, 0, ',', '.'); ?> VND</div>
                                                    <div class="text-xs text-muted mt-2">
                                                        <span class="text-danger">* Chi phí từ hóa đơn: <?php echo number_format($expensesFromInvoices, 0, ',', '.'); ?> VND</span><br>
                                                        <span class="text-danger">* Chi phí khác: <?php echo number_format($expensesFromThuChi, 0, ',', '.'); ?> VND</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-4">
                                            <div class="card border-left-success h-100">
                                                <div class="card-body">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Lợi nhuận</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($profit, 0, ',', '.'); ?> VND</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; TheCleaner 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bạn chắc chắn muốn đăng xuất?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Chọn "Đăng xuất" nếu bạn đã sẵn sàng kết thúc phiên làm việc hiện tại.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                    <a class="btn btn-primary" href="logout.php">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>