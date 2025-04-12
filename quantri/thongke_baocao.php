<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập Admin
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Lấy doanh thu theo tháng (nếu cần)
$sqlRevenue = "SELECT MONTH(bookingDate) AS month, SUM(totalAmount) AS revenue FROM bookings GROUP BY MONTH(bookingDate)";
$resultRevenue = $conn->query($sqlRevenue);
$revenues = $resultRevenue->fetch_all(MYSQLI_ASSOC);

// Lấy tổng số đơn đặt dịch vụ
$sqlBookings = "SELECT COUNT(*) AS totalBookings FROM bookings";
$resultBookings = $conn->query($sqlBookings);
$totalBookings = $resultBookings->fetch_assoc();

// Lấy số lượng khách hàng mới trong tháng
$sqlNewCustomers = "SELECT COUNT(*) AS newCustomers FROM customers WHERE MONTH(registrationDate) = MONTH(CURDATE())";
$resultNewCustomers = $conn->query($sqlNewCustomers);
$newCustomers = $resultNewCustomers->fetch_assoc();

// Lấy dịch vụ phổ biến nhất
$sqlPopularService = "SELECT services.serviceName, COUNT(bookings.serviceID) AS totalOrders 
                      FROM bookings 
                      JOIN services ON bookings.serviceID = services.serviceID 
                      GROUP BY services.serviceName 
                      ORDER BY totalOrders DESC 
                      LIMIT 1";
$resultPopularService = $conn->query($sqlPopularService);
$popularService = $resultPopularService->fetch_assoc();

// ========================
// Phần báo cáo thu – chi sử dụng bảng "thuchi"
// ========================

// Tính tổng thu và tổng chi từ bảng "thuchi"
$sqlAccountingSummary = "SELECT 
                            SUM(CASE WHEN loai = 'Thu' THEN soTien ELSE 0 END) AS totalIncome,
                            SUM(CASE WHEN loai = 'Chi' THEN soTien ELSE 0 END) AS totalExpense
                         FROM thuchi";
$resultAccountingSummary = $conn->query($sqlAccountingSummary);
$accountingSummary = $resultAccountingSummary->fetch_assoc();

// ------------------------
// Bộ lọc theo tháng cho báo cáo thu – chi
// ------------------------
$selectedMonth = isset($_GET['filter_month']) && $_GET['filter_month'] !== "" ? intval($_GET['filter_month']) : 0;

if ($selectedMonth > 0) {
    // Nếu có lọc theo tháng thì truy vấn dữ liệu theo tháng được chọn
    $sqlMonthlyNet = "SELECT 
                        MONTH(ngayGiaoDich) AS month, 
                        SUM(CASE WHEN loai = 'Thu' THEN soTien ELSE 0 END) AS totalIncome,
                        SUM(CASE WHEN loai = 'Chi' THEN soTien ELSE 0 END) AS totalExpense,
                        SUM(CASE WHEN loai = 'Thu' THEN soTien ELSE 0 END) - SUM(CASE WHEN loai = 'Chi' THEN soTien ELSE 0 END) AS netAmount
                      FROM thuchi
                      WHERE MONTH(ngayGiaoDich) = ?
                      GROUP BY MONTH(ngayGiaoDich)
                      ORDER BY month ASC";
    $stmt = $conn->prepare($sqlMonthlyNet);
    $stmt->bind_param("i", $selectedMonth);
    $stmt->execute();
    $resultMonthlyNet = $stmt->get_result();
    $monthlyNetRecords = $resultMonthlyNet->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Nếu không lọc theo tháng thì hiển thị tất cả các tháng
    $sqlMonthlyNet = "SELECT 
                        MONTH(ngayGiaoDich) AS month, 
                        SUM(CASE WHEN loai = 'Thu' THEN soTien ELSE 0 END) AS totalIncome,
                        SUM(CASE WHEN loai = 'Chi' THEN soTien ELSE 0 END) AS totalExpense,
                        SUM(CASE WHEN loai = 'Thu' THEN soTien ELSE 0 END) - SUM(CASE WHEN loai = 'Chi' THEN soTien ELSE 0 END) AS netAmount
                      FROM thuchi
                      GROUP BY MONTH(ngayGiaoDich)
                      ORDER BY month ASC";
    $resultMonthlyNet = $conn->query($sqlMonthlyNet);
    $monthlyNetRecords = $resultMonthlyNet->fetch_all(MYSQLI_ASSOC);
}

// ------------------------
// Tách danh sách giao dịch thành hai phần: Thu và Chi
// ------------------------

// Danh sách giao dịch Thu
$sqlAccountingRecordsIncome = "SELECT ngayGiaoDich, loai, noiDung, soTien, hinhThuc 
                               FROM thuchi 
                               WHERE loai = 'Thu'
                               ORDER BY ngayGiaoDich DESC";
$resultAccountingRecordsIncome = $conn->query($sqlAccountingRecordsIncome);
$accountingRecordsIncome = $resultAccountingRecordsIncome->fetch_all(MYSQLI_ASSOC);

// Danh sách giao dịch Chi
$sqlAccountingRecordsExpense = "SELECT ngayGiaoDich, loai, noiDung, soTien, hinhThuc 
                                FROM thuchi 
                                WHERE loai = 'Chi'
                                ORDER BY ngayGiaoDich DESC";
$resultAccountingRecordsExpense = $conn->query($sqlAccountingRecordsExpense);
$accountingRecordsExpense = $resultAccountingRecordsExpense->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê báo cáo</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php require("includes/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Thống kê báo cáo</h2>
                    <div class="row">
                        <!-- Tổng đơn đặt dịch vụ -->
                        <div class="col-md-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5>Tổng đơn đặt dịch vụ</h5>
                                    <p><?php echo $totalBookings['totalBookings']; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Khách hàng mới trong tháng -->
                        <div class="col-md-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5>Khách hàng mới trong tháng</h5>
                                    <p><?php echo $newCustomers['newCustomers']; ?></p>
                                </div>
                            </div>
                        </div>
                        <!-- Dịch vụ phổ biến nhất -->
                        <div class="col-md-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5>Dịch vụ phổ biến nhất</h5>
                                    <p>
                                        <?php echo isset($popularService['serviceName']) ? $popularService['serviceName'] : "Không có dữ liệu"; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc theo tháng cho báo cáo thu – chi -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <form method="GET" action="">
                                <div class="form-group">
                                    <label for="filter_month">Chọn tháng</label>
                                    <select name="filter_month" id="filter_month" class="form-control">
                                        <option value="">Tất cả các tháng</option>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            $selected = ($selectedMonth == $i) ? 'selected' : '';
                                            echo "<option value=\"$i\" $selected>Tháng $i</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Lọc</button>
                            </form>
                        </div>
                    </div>

                    <!-- Báo cáo thu – chi theo tháng (Số dư: Thu - Chi) -->
                    <h4 class="mt-4">Báo cáo thu – chi theo tháng</h4>
                    <?php if(count($monthlyNetRecords) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Tổng Thu</th>
                                        <th>Tổng Chi</th>
                                        <th>Số dư (Thu - Chi)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($monthlyNetRecords as $record): ?>
                                        <tr>
                                            <td><?php echo $record['month']; ?></td>
                                            <td><?php echo number_format($record['totalIncome'], 2); ?></td>
                                            <td><?php echo number_format($record['totalExpense'], 2); ?></td>
                                            <td><?php echo number_format($record['netAmount'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Không có dữ liệu báo cáo theo tháng.</p>
                    <?php endif; ?>

                    <!-- Danh sách giao dịch Thu -->
                    <h4 class="mt-4">Danh sách giao dịch Thu</h4>
                    <?php if(count($accountingRecordsIncome) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Ngày giao dịch</th>
                                        <th>Nội dung</th>
                                        <th>Số tiền</th>
                                        <th>Hình thức</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($accountingRecordsIncome as $record): ?>
                                        <tr>
                                            <td><?php echo date("d/m/Y", strtotime($record['ngayGiaoDich'])); ?></td>
                                            <td><?php echo htmlspecialchars($record['noiDung']); ?></td>
                                            <td><?php echo number_format($record['soTien'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($record['hinhThuc']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Không có dữ liệu giao dịch Thu.</p>
                    <?php endif; ?>

                    <!-- Danh sách giao dịch Chi -->
                    <h4 class="mt-5">Danh sách giao dịch Chi</h4>
                    <?php if(count($accountingRecordsExpense) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Ngày giao dịch</th>
                                        <th>Nội dung</th>
                                        <th>Số tiền</th>
                                        <th>Hình thức</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($accountingRecordsExpense as $record): ?>
                                        <tr>
                                            <td><?php echo date("d/m/Y", strtotime($record['ngayGiaoDich'])); ?></td>
                                            <td><?php echo htmlspecialchars($record['noiDung']); ?></td>
                                            <td><?php echo number_format($record['soTien'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($record['hinhThuc']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Không có dữ liệu giao dịch Chi.</p>
                    <?php endif; ?>

                </div><!-- /.container-fluid -->
            </div><!-- End of Main Content -->
        </div><!-- End of Content Wrapper -->
    </div><!-- End of Page Wrapper -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
