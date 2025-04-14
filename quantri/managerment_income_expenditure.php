<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");

// Lấy giá trị lọc - Cải tiến: Thêm lọc theo khoảng thời gian
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // Mặc định là ngày 1 của tháng hiện tại
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Mặc định là ngày cuối của tháng hiện tại

// Xác định tháng hiện tại từ khoảng thời gian đã chọn (để tương thích với code cũ)
$selectedMonth = date('Y-m', strtotime($startDate));
$dateRangeText = date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate));
$currentMonth = "Tháng " . date('m/Y', strtotime($selectedMonth));

// Lấy số ngày trong khoảng thời gian đã chọn
$startTimestamp = strtotime($startDate);
$endTimestamp = strtotime($endDate);
$daysInRange = ceil(($endTimestamp - $startTimestamp) / (60 * 60 * 24)) + 1;
$chartData = [];
$days = [];

// Khởi tạo dữ liệu trống cho mỗi ngày trong khoảng thời gian
for ($i = 0; $i < $daysInRange; $i++) {
    $timestamp = $startTimestamp + ($i * 86400); // 86400 = 24 * 60 * 60 (số giây trong một ngày)
    $currentDate = date('Y-m-d', $timestamp);
    $day = date('d', $timestamp);
    $days[] = $day;
    
    // Khởi tạo dữ liệu trống cho mỗi ngày
    $chartData[$currentDate] = [
        'incomeAmount' => 0,
        'expenseAmount' => 0,
        'netRevenue' => 0
    ];
}

// Lấy dữ liệu thu theo ngày từ booking trong khoảng thời gian
$sqlIncome = "SELECT DATE(booking_date) as day, SUM(totalAmount) as total 
             FROM bookings 
             WHERE booking_date BETWEEN ? AND ?
             GROUP BY DATE(booking_date)";
$stmtIncome = $conn->prepare($sqlIncome);
$stmtIncome->bind_param("ss", $startDate, $endDate);
$stmtIncome->execute();
$resultIncome = $stmtIncome->get_result();
if ($resultIncome && $resultIncome->num_rows > 0) {
    while ($row = $resultIncome->fetch_assoc()) {
        if (isset($chartData[$row['day']])) {
            $chartData[$row['day']]['incomeAmount'] = $row['total'] ?? 0;
        }
    }
}

// Lấy dữ liệu chi theo ngày từ invoice trong khoảng thời gian
$sqlExpenses = "SELECT DATE(issueDate) as day, SUM(totalAmount) as total 
               FROM invoices 
               WHERE issueDate BETWEEN ? AND ?
               GROUP BY DATE(issueDate)";
$stmtExpenses = $conn->prepare($sqlExpenses);
$stmtExpenses->bind_param("ss", $startDate, $endDate);
$stmtExpenses->execute();
$resultExpenses = $stmtExpenses->get_result();
if ($resultExpenses && $resultExpenses->num_rows > 0) {
    while ($row = $resultExpenses->fetch_assoc()) {
        if (isset($chartData[$row['day']])) {
            $chartData[$row['day']]['expenseAmount'] = $row['total'] ?? 0;
        }
    }
}

// Tính doanh thu ròng cho mỗi ngày
foreach ($chartData as $day => $data) {
    $chartData[$day]['netRevenue'] = $data['incomeAmount'] - $data['expenseAmount'];
}

// Lấy tổng thu từ booking trong khoảng thời gian
$totalIncomeAmount = 0;
$sqlTotalIncome = "SELECT SUM(totalAmount) as total FROM bookings WHERE booking_date BETWEEN ? AND ?";
$stmtTotalIncome = $conn->prepare($sqlTotalIncome);
$stmtTotalIncome->bind_param("ss", $startDate, $endDate);
$stmtTotalIncome->execute();
$resultTotalIncome = $stmtTotalIncome->get_result();
if ($resultTotalIncome && $resultTotalIncome->num_rows > 0) {
    $rowTotalIncome = $resultTotalIncome->fetch_assoc();
    $totalIncomeAmount = $rowTotalIncome['total'] ?: 0;
}

// Lấy tổng chi từ invoice trong khoảng thời gian
$totalExpenseAmount = 0;
$sqlTotalExpense = "SELECT SUM(totalAmount) as total FROM invoices WHERE issueDate BETWEEN ? AND ?";
$stmtTotalExpense = $conn->prepare($sqlTotalExpense);
$stmtTotalExpense->bind_param("ss", $startDate, $endDate);
$stmtTotalExpense->execute();
$resultTotalExpense = $stmtTotalExpense->get_result();
if ($resultTotalExpense && $resultTotalExpense->num_rows > 0) {
    $rowTotalExpense = $resultTotalExpense->fetch_assoc();
    $totalExpenseAmount = $rowTotalExpense['total'] ?: 0;
}

// Tính tổng doanh thu
$totalRevenue = $totalIncomeAmount - $totalExpenseAmount;

// Lấy danh sách khoản chi từ bảng thuchi với loại "Chi" trong khoảng thời gian
$thuchiList = [];
$sqlThuChi = "SELECT * FROM thuchi WHERE loai = 'Chi' AND ngayGiaoDich BETWEEN ? AND ? ORDER BY ngayGiaoDich DESC";
$stmtThuChi = $conn->prepare($sqlThuChi);
$stmtThuChi->bind_param("ss", $startDate, $endDate);
$stmtThuChi->execute();
$resultThuChi = $stmtThuChi->get_result();
if ($resultThuChi && $resultThuChi->num_rows > 0) {
    while ($row = $resultThuChi->fetch_assoc()) {
        $thuchiList[] = $row;
    }
}

// Lấy danh sách khoản thu từ bảng thuchi với loại "Thu" trong khoảng thời gian
$thuList = [];
$sqlThu = "SELECT * FROM thuchi WHERE loai = 'Thu' AND ngayGiaoDich BETWEEN ? AND ? ORDER BY ngayGiaoDich DESC";
$stmtThu = $conn->prepare($sqlThu);
$stmtThu->bind_param("ss", $startDate, $endDate);
$stmtThu->execute();
$resultThu = $stmtThu->get_result();
if ($resultThu && $resultThu->num_rows > 0) {
    while ($row = $resultThu->fetch_assoc()) {
        $thuList[] = $row;
    }
}

// Xử lý thêm khoản chi mới
if (isset($_POST['add_expense'])) {
    $ngayGiaoDich = $_POST['ngayGiaoDich'];
    $noiDung = $_POST['noiDung'];
    $soTien = $_POST['soTien'];
    $hinhThuc = $_POST['hinhThuc'];
    
    $sqlInsert = "INSERT INTO thuchi (loai, ngayGiaoDich, noiDung, soTien, hinhThuc) 
                 VALUES ('Chi', ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("ssds", $ngayGiaoDich, $noiDung, $soTien, $hinhThuc);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm khoản chi thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm khoản chi: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: managerment_income_expenditure.php?start_date={$startDate}&end_date={$endDate}");
    exit();
}

// Xử lý thêm khoản thu mới
if (isset($_POST['add_income'])) {
    $ngayGiaoDich = $_POST['ngayGiaoDich'];
    $noiDung = $_POST['noiDung'];
    $soTien = $_POST['soTien'];
    $hinhThuc = $_POST['hinhThuc'];
    
    $sqlInsert = "INSERT INTO thuchi (loai, ngayGiaoDich, noiDung, soTien, hinhThuc) 
                 VALUES ('Thu', ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("ssds", $ngayGiaoDich, $noiDung, $soTien, $hinhThuc);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Thêm khoản thu thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm khoản thu: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: managerment_income_expenditure.php?start_date={$startDate}&end_date={$endDate}");
    exit();
}

// Xử lý xóa khoản chi
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $delete_id = $_GET['delete_id'];
    $type = $_GET['type'];
    $loai = ($type === 'chi') ? 'Chi' : 'Thu';
    
    $sqlDelete = "DELETE FROM thuchi WHERE thuchiID = ? AND loai = ?";
    $stmt = $conn->prepare($sqlDelete);
    $stmt->bind_param("is", $delete_id, $loai);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Xóa khoản " . strtolower($loai) . " thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa khoản " . strtolower($loai) . ": " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: managerment_income_expenditure.php?start_date={$startDate}&end_date={$endDate}");
    exit();
}

// Chuẩn bị dữ liệu cho biểu đồ
$chartLabels = json_encode($days);
$chartIncome = [];
$chartExpenses = [];
$chartRevenue = [];

foreach ($chartData as $date => $data) {
    $chartIncome[] = $data['incomeAmount'];
    $chartExpenses[] = $data['expenseAmount'];
    $chartRevenue[] = $data['netRevenue'];
}

$chartIncomeData = json_encode($chartIncome);
$chartExpensesData = json_encode($chartExpenses);
$chartRevenueData = json_encode($chartRevenue);

// Lấy danh sách invoice trong khoảng thời gian đã chọn
$invoiceList = [];
$sqlInvoices = "SELECT * FROM invoices WHERE issueDate BETWEEN ? AND ? ORDER BY issueDate DESC";
$stmtInvoices = $conn->prepare($sqlInvoices);
$stmtInvoices->bind_param("ss", $startDate, $endDate);
$stmtInvoices->execute();
$resultInvoices = $stmtInvoices->get_result();
if ($resultInvoices && $resultInvoices->num_rows > 0) {
    while ($row = $resultInvoices->fetch_assoc()) {
        $invoiceList[] = $row;
    }
}

// Lấy danh sách booking trong khoảng thời gian đã chọn
$bookingList = [];
$sqlBookings = "SELECT b.id, c.fullName AS customerName, 
               b.booking_date, s.name AS serviceName, 
               b.totalAmount, b.status
               FROM bookings b
               JOIN customers c ON b.customerID = c.customerID
               JOIN services s ON b.service_id = s.serviceID
               WHERE b.booking_date BETWEEN ? AND ?
               ORDER BY b.booking_date DESC";
$stmtBookings = $conn->prepare($sqlBookings);
$stmtBookings->bind_param("ss", $startDate, $endDate);
$stmtBookings->execute();
$resultBookings = $stmtBookings->get_result();
if ($resultBookings && $resultBookings->num_rows > 0) {
    while ($row = $resultBookings->fetch_assoc()) {
        $bookingList[] = $row;
    }
}

// Kiểm tra xem báo cáo đã được gửi chưa - Sửa lỗi hiển thị hai thông báo
$reportSent = isset($_GET['report_sent']) && $_GET['report_sent'] == 1;
$reportKey = isset($_SESSION['report_key']) ? $_SESSION['report_key'] : '';
$reportDate = isset($_SESSION['report_date']) ? $_SESSION['report_date'] : '';

// Kiểm tra xem báo cáo cho khoảng thời gian này đã tồn tại không
$currentReportKey = $startDate . '_to_' . $endDate;
$isReportSent = false;

// Kiểm tra trong database
$sqlCheckReport = "SELECT id FROM financial_reports WHERE report_key = ?";
$stmtCheckReport = $conn->prepare($sqlCheckReport);
$stmtCheckReport->bind_param("s", $currentReportKey);
$stmtCheckReport->execute();
$resultCheckReport = $stmtCheckReport->get_result();
if ($resultCheckReport->num_rows > 0) {
    $isReportSent = true;
}
$stmtCheckReport->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thu Chi - TheCleaner</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .card-summary {
            margin-bottom: 20px;
        }
        .card-value {
            font-size: 24px;
            font-weight: bold;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid #4e73df;
        }
        .date-range-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .date-range-label {
            font-style: italic;
            color: #6c757d;
            margin-left: 5px;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="accountant_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kế toán</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="accountant_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">


            <li class="nav-item">
                <a class="nav-link" href="managerment_invoice.php">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Quản lý hóa đơn</span>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="managerment_income_expenditure.php">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Quản lý thu chi</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['user']['fullName']; ?></span>
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Hồ sơ
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Quản lý Thu Chi</h1>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <!-- Kiểm tra nếu báo cáo đã được gửi - CHỈ HIỂN THỊ MỘT THÔNG BÁO -->
                    <?php if ($reportSent && isset($_SESSION['reports'][$reportKey])): ?>
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle"></i> Báo cáo từ ngày <?php echo date('d/m/Y', strtotime($startDate)); ?> đến ngày <?php echo date('d/m/Y', strtotime($endDate)); ?> đã được gửi tới Admin vào lúc 
                        <?php echo date('d/m/Y H:i:s', strtotime($reportDate)); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Lọc theo khoảng thời gian và nút xuất Excel -->
                    <div class="row mb-4">
                        <div class="col-md-7">
                            <form method="GET" action="managerment_income_expenditure.php" class="form-inline">
                                <div class="date-range-container">
                                    <label for="start_date" class="mr-2">Từ ngày:</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control mr-3" value="<?php echo $startDate; ?>">
                
                                    <label for="end_date" class="mr-2">Đến ngày:</la>
                                    <input type="date" id="end_date" name="end_date" class="form-control mr-3" value="<?php echo $endDate; ?>">
                
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-filter"></i> Lọc
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-5 text-right">
                            <button class="btn btn-success" onclick="exportThuChiToExcel()">
                                <i class="fas fa-file-excel"></i> Xuất Excel
                            </button>
                        </div>
                    </div>

                    <!-- Danh sách báo cáo đã gửi -->
                    <?php 
                    // Lấy danh sách báo cáo đã gửi từ database
                    $sentReports = [];
                    $sqlSentReports = "SELECT * FROM financial_reports WHERE sent_by = ? ORDER BY sent_date DESC";
                    $stmtSentReports = $conn->prepare($sqlSentReports);
                    $stmtSentReports->bind_param("s", $_SESSION['user']['fullName']);
                    $stmtSentReports->execute();
                    $resultSentReports = $stmtSentReports->get_result();
                    if ($resultSentReports->num_rows > 0) {
                        while ($row = $resultSentReports->fetch_assoc()) {
                            $sentReports[] = $row;
                        }
                    }
                    $stmtSentReports->close();
                    
                    if (!empty($sentReports)): 
                    ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-history"></i> Lịch sử báo cáo đã gửi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Khoảng thời gian</th>
                                            <th>Thời gian gửi</th>
                                            <th>Trạng thái</th>
                                            <th>Phản hồi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sentReports as $report): 
                                            $isCurrentReport = ($report['report_key'] == $currentReportKey);
                                            $reportData = json_decode($report['report_data'], true);
                                            $statusBadge = '';
                                            
                                            switch($report['status']) {
                                                case 'unread': 
                                                    $statusBadge = '<span class="badge badge-warning">Chưa đọc</span>'; 
                                                    break;
                                                case 'read': 
                                                    $statusBadge = '<span class="badge badge-info">Đã đọc</span>'; 
                                                    break;
                                                case 'approved': 
                                                    $statusBadge = '<span class="badge badge-success">Đã duyệt</span>'; 
                                                    break;
                                                case 'rejected': 
                                                    $statusBadge = '<span class="badge badge-danger">Từ chối</span>'; 
                                                    break;
                                            }
                                        ?>
                                            <tr class="<?php echo $isCurrentReport ? 'table-active' : ''; ?>">
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($report['start_date'])); ?> - 
                                                    <?php echo date('d/m/Y', strtotime($report['end_date'])); ?>
                                                    <?php if ($isCurrentReport): ?>
                                                    <span class="badge badge-info ml-2">Đang xem</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i:s', strtotime($report['sent_date'])); ?></td>
                                                <td><?php echo $statusBadge; ?></td>
                                                <td><?php echo !empty($report['feedback']) ? $report['feedback'] : 'Chưa có phản hồi'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Biểu đồ đường -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Biểu đồ thu chi</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="financeChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Thẻ tổng kết -->
                    <div class="row">
                        <!-- Tổng thu -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Tổng thu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalIncomeAmount, 0, ',', '.'); ?> VND</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tổng chi -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Tổng chi</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalExpenseAmount, 0, ',', '.'); ?> VND</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tổng doanh thu -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Tổng doanh thu</div>
                                            <div class="h5 mb-0 font-weight-bold <?php echo $totalRevenue >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($totalRevenue, 0, ',', '.'); ?> VND
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Thu Chi -->
                    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="expense-tab" data-toggle="tab" href="#expense" role="tab" aria-controls="expense" aria-selected="true">
                                <i class="fas fa-minus-circle text-danger"></i> Khoản Chi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="income-tab" data-toggle="tab" href="#income" role="tab" aria-controls="income" aria-selected="false">
                                <i class="fas fa-plus-circle text-success"></i> Khoản Thu
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="myTabContent">
                        <!-- Tab Khoản Chi -->
                        <div class="tab-pane fade show active" id="expense" role="tabpanel" aria-labelledby="expense-tab">
                            <!-- Danh sách khoản chi từ invoice -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách khoản chi (từ Invoice)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTableExpense" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nhà cung cấp</th>
                                                    <th>Ngày lập</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($invoiceList)): ?>
                                                    <?php foreach ($invoiceList as $invoice): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($invoice['supplierName']); ?></td>
                                                            <td><?php echo date('d/m/Y', strtotime($invoice['issueDate'])); ?></td>
                                                            <td><?php echo number_format($invoice['totalAmount'], 0, ',', '.'); ?> VND</td>
                                                            <td><?php echo htmlspecialchars($invoice['status']); ?></td>
                                                            <td>
                                                                <a href="managerment_invoice.php?edit_id=<?php echo $invoice['invoiceID']; ?>" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-edit"></i> Sửa
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">Không có dữ liệu hóa đơn trong khoảng thời gian này</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab Khoản Thu -->
                        <div class="tab-pane fade" id="income" role="tabpanel" aria-labelledby="income-tab">
                            <!-- Danh sách khoản thu từ booking -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách khoản thu (từ Booking)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTableIncome" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Ngày đặt</th>
                                                    <th>Dịch vụ</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($bookingList)): ?>
                                                    <?php foreach ($bookingList as $booking): ?>
                                                        <tr>
                                                            <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                                            <td><?php echo htmlspecialchars($booking['serviceName']); ?></td>
                                                            <td><?php echo number_format($booking['totalAmount'], 0, ',', '.'); ?> VND</td>
                                                            <td>
                                                                <?php 
                                                                    switch($booking['status']) {
                                                                        case 'pending':
                                                                            echo '<span class="badge badge-warning">Chờ xử lý</span>';
                                                                            break;
                                                                        case 'confirmed':
                                                                            echo '<span class="badge badge-primary">Đã xác nhận</span>';
                                                                            break;
                                                                        case 'completed':
                                                                            echo '<span class="badge badge-success">Hoàn thành</span>';
                                                                            break;
                                                                        case 'cancelled':
                                                                            echo '<span class="badge badge-danger">Đã hủy</span>';
                                                                            break;
                                                                        default:
                                                                            echo $booking['status'];
                                                                    }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">Không có dữ liệu đặt dịch vụ trong khoảng thời gian này</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; TheCleaner 2025</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
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
                <div class="modal-body">Chọn "Đăng xuất" nếu bạn muốn kết thúc phiên làm việc.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                    <a class="btn btn-primary" href="logout.php">Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Kiểm tra đầu vào ngày
            $('#start_date, #end_date').on('change', function() {
                let startDate = new Date($('#start_date').val());
                let endDate = new Date($('#end_date').val());
                
                if (startDate > endDate) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc!');
                    $('#end_date').val($('#start_date').val());
                }
            });
            
            // Khởi tạo DataTables
            $('#dataTableExpense').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ dòng",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Trang _PAGE_ / _PAGES_",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ dòng)",
                    "search": "Tìm kiếm:",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Sau",
                        "previous": "Trước"
                    }
                }
            });
            
            $('#dataTableIncome').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ dòng",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Trang _PAGE_ / _PAGES_",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ dòng)",
                    "search": "Tìm kiếm:",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Sau",
                        "previous": "Trước"
                    }
                }
            });
            
            // Biểu đồ đường
            var ctx = document.getElementById('financeChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo $chartLabels; ?>,
                    datasets: [
                        {
                            label: 'Tổng thu',
                            data: <?php echo $chartIncomeData; ?>,
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Tổng chi',
                            data: <?php echo $chartExpensesData; ?>,
                            backgroundColor: 'rgba(231, 74, 59, 0.1)',
                            borderColor: 'rgba(231, 74, 59, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Doanh thu',
                            data: <?php echo $chartRevenueData; ?>,
                            backgroundColor: 'rgba(28, 200, 138, 0.1)',
                            borderColor: 'rgba(28, 200, 138, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('vi-VN') + ' VND';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y.toLocaleString('vi-VN') + ' VND';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
        
        /**
         * Hàm xuất dữ liệu thu chi sang Excel
         */
        function exportThuChiToExcel() {
            // Lấy thông tin ngày từ form
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const formattedStartDate = formatDate(startDate);
            const formattedEndDate = formatDate(endDate);
            
            // Lấy dữ liệu từ biểu đồ
            const chart = Chart.getChart('financeChart');
            const dailyData = [];
            
            if (chart) {
                const labels = chart.data.labels;
                const incomeData = chart.data.datasets[0].data;
                const expenseData = chart.data.datasets[1].data;
                const revenueData = chart.data.datasets[2].data;
                
                // Tạo mảng dữ liệu theo ngày
                for (let i = 0; i < labels.length; i++) {
                    dailyData.push({
                        date: labels[i],
                        income: formatCurrency(incomeData[i]),
                        expense: formatCurrency(expenseData[i]),
                        revenue: formatCurrency(revenueData[i])
                    });
                }
            }
            
            // Lấy dữ liệu chi tiết
            const expenseDetails = getTableData(document.getElementById('dataTableExpense'));
            const incomeDetails = getTableData(document.getElementById('dataTableIncome'));
            
            // Lấy tổng số liệu
            const totalIncomeElement = document.querySelector('.text-primary + .h5');
            const totalExpenseElement = document.querySelector('.text-danger + .h5');
            const totalRevenueElement = document.querySelector('.text-success + .h5');
            
            const totalIncome = totalIncomeElement ? totalIncomeElement.textContent.trim() : '';
            const totalExpense = totalExpenseElement ? totalExpenseElement.textContent.trim() : '';
            const totalRevenue = totalRevenueElement ? totalRevenueElement.textContent.trim() : '';
            
            // Tạo workbook mới
            const wb = XLSX.utils.book_new();
            
            // Tạo worksheet tổng quan
            const wsOverview = XLSX.utils.aoa_to_sheet([
                ['BÁO CÁO THU CHI THECLEANER'],
                [`Từ ngày ${formattedStartDate} đến ngày ${formattedEndDate}`],
                [''],
                ['TỔNG KẾT'],
                [`Tổng thu: ${totalIncome}`],
                [`Tổng chi: ${totalExpense}`],
                [`Tổng doanh thu: ${totalRevenue}`],
                ['']
            ]);
            
            // Định dạng tiêu đề
            wsOverview['!merges'] = [
                {s: {r: 0, c: 0}, e: {r: 0, c: 5}},
                {s: {r: 1, c: 0}, e: {r: 1, c: 5}}
            ];
            
            // Thêm worksheet tổng quan vào workbook
            XLSX.utils.book_append_sheet(wb, wsOverview, 'Tổng quan');
            
            // Tạo worksheet dữ liệu chi tiết theo ngày
            const wsDailyData = XLSX.utils.aoa_to_sheet([
                ['DOANH THU THEO NGÀY'],
                [''],
                ['Ngày', 'Tổng thu', 'Tổng chi', 'Doanh thu']
            ]);
            
            // Thêm dữ liệu theo ngày
            let rowIndex = 3;
            dailyData.forEach(item => {
                XLSX.utils.sheet_add_aoa(wsDailyData, [[
                    item.date,
                    item.income,
                    item.expense,
                    item.revenue
                ]], {origin: {r: rowIndex, c: 0}});
                rowIndex++;
            });
            
            // Thêm worksheet dữ liệu theo ngày vào workbook
            XLSX.utils.book_append_sheet(wb, wsDailyData, 'Dữ liệu theo ngày');
            
            // Thêm worksheet khoản chi
            if (expenseDetails && expenseDetails.length > 0) {
                const wsExpenses = XLSX.utils.json_to_sheet(expenseDetails);
                XLSX.utils.book_append_sheet(wb, wsExpenses, 'Khoản chi');
            }
            
            // Thêm worksheet khoản thu
            if (incomeDetails && incomeDetails.length > 0) {
                const wsIncomes = XLSX.utils.json_to_sheet(incomeDetails);
                XLSX.utils.book_append_sheet(wb, wsIncomes, 'Khoản thu');
            }
            
            // Tạo tên file
            const fileName = `BaoCaoThuChi_${formattedStartDate.replace(/\//g, '-')}_den_${formattedEndDate.replace(/\//g, '-')}.xlsx`;
            
            // Xuất file Excel
            XLSX.writeFile(wb, fileName);
        }

        /**
         * Hàm lấy dữ liệu từ bảng HTML
         */
        function getTableData(table) {
            if (!table) return [];
            
            const data = [];
            const rows = table.querySelectorAll('tbody tr');
            const headers = [];
            
            // Lấy tiêu đề
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            
            // Lấy dữ liệu từng dòng
            rows.forEach(row => {
                const rowData = {};
                row.querySelectorAll('td').forEach((cell, index) => {
                    if (index < headers.length) {
                        rowData[headers[index]] = cell.textContent.trim();
                    }
                });
                data.push(rowData);
            });
            
            return data;
        }

        /**
         * Hàm định dạng ngày từ YYYY-MM-DD sang DD/MM/YYYY
         */
        function formatDate(dateString) {
            if (!dateString) return '';
            const parts = dateString.split('-');
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        /**
         * Hàm định dạng số tiền
         */
        function formatCurrency(value) {
            if (typeof value !== 'number') return value;
            return value.toLocaleString('vi-VN') + ' VND';
        }
    </script>
</body>
</html>