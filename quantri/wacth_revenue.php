<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");


// Lấy tháng hiện tại hoặc tháng được chọn
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Tính ngày đầu và cuối của tháng đã chọn
$startDate = date('Y-m-01', strtotime($selectedMonth));
$endDate = date('Y-m-t', strtotime($selectedMonth));

// Lấy tổng doanh thu từ đặt dịch vụ
$sqlIncome = "SELECT SUM(totalAmount) as total FROM bookings 
              WHERE booking_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sqlIncome);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalIncome = $row['total'] ?: 0;

// Lấy tổng chi phí từ hóa đơn
$sqlExpense = "SELECT SUM(totalAmount) as total FROM invoices 
               WHERE issueDate BETWEEN ? AND ?";
$stmt = $conn->prepare($sqlExpense);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalExpense = $row['total'] ?: 0;

// Tính doanh thu
$revenue = $totalIncome - $totalExpense;

// Lấy số ngày trong tháng
$daysInMonth = date('t', strtotime($selectedMonth . '-01'));
$days = [];

// Tạo dữ liệu biểu đồ trống
for ($i = 1; $i <= $daysInMonth; $i++) {
    $days[] = sprintf('%02d', $i);
}

$chartData = [
    'labels' => $days,
    'income' => array_fill(0, $daysInMonth, 0),
    'expenses' => array_fill(0, $daysInMonth, 0),
    'revenue' => array_fill(0, $daysInMonth, 0)
];

// Lấy dữ liệu thu theo ngày
$sqlDailyIncome = "SELECT DAY(booking_date) as day, SUM(totalAmount) as total 
                   FROM bookings 
                   WHERE booking_date BETWEEN ? AND ?
                   GROUP BY DAY(booking_date)";
$stmt = $conn->prepare($sqlDailyIncome);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $day = intval($row['day']) - 1; // Điều chỉnh index cho mảng (0-based)
    $chartData['income'][$day] = floatval($row['total']);
}

// Lấy dữ liệu chi theo ngày
$sqlDailyExpense = "SELECT DAY(issueDate) as day, SUM(totalAmount) as total 
                    FROM invoices 
                    WHERE issueDate BETWEEN ? AND ?
                    GROUP BY DAY(issueDate)";
$stmt = $conn->prepare($sqlDailyExpense);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $day = intval($row['day']) - 1; // Điều chỉnh index cho mảng (0-based)
    $chartData['expenses'][$day] = floatval($row['total']);
}

// Tính doanh thu theo ngày
for ($i = 0; $i < $daysInMonth; $i++) {
    $chartData['revenue'][$i] = $chartData['income'][$i] - $chartData['expenses'][$i];
}

// Lấy dữ liệu cho bảng dịch vụ theo khoảng thời gian
$sqlServices = "SELECT s.name AS serviceName, COUNT(b.id) AS bookingCount, 
                SUM(b.totalAmount) AS totalAmount
                FROM bookings b
                JOIN services s ON b.service_id = s.serviceID
                WHERE b.booking_date BETWEEN ? AND ?
                GROUP BY s.serviceID
                ORDER BY totalAmount DESC";
$stmt = $conn->prepare($sqlServices);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$serviceResult = $stmt->get_result();
$serviceData = [];
while ($row = $serviceResult->fetch_assoc()) {
    $serviceData[] = $row;
}

// Hiển thị định dạng tháng
$currentMonth = "Tháng " . date('m/Y', strtotime($selectedMonth));

// Định dạng khoảng thời gian đầy đủ
$fullDateRange = date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate));

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Doanh Thu</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
        .revenue-card {
            transition: all 0.3s ease;
        }
        .revenue-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .report-info {
            background-color: #f8f9fc;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .date-range-label {
            font-style: italic;
            color: #6c757d;
            margin-left: 5px;
        }
        .date-range-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .action-buttons {
            margin-top: 15px;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 0.3em 0.6em;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">
            
            <li class="nav-item">
                <a class="nav-link" href="managerment_customer.php">
                    <i class="fas fa-users"></i>
                    <span>Quản lý khách hàng</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="managerment_employee.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Quản lý nhân viên</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="managerment_rental.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Quản lý lịch thuê</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="managerment_service.php">
                    <i class="fas fa-broom"></i>
                    <span>Quản lý dịch vụ</span>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="wacth_revenue.php">
                    <i class="fas fa-chart-area"></i>
                    <span>Xem doanh thu</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Thống kê báo cáo</span></a>
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

                        <div class="topbar-divider d-none d-sm-block"></div>

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
                    <h1 class="h3 mb-4 text-gray-800">Xem doanh thu</h1>

                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <!-- Lọc theo khoảng thời gian và tháng -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <!-- Lọc theo tháng (cách cũ) -->
                            <form method="GET" action="wacth_revenue.php" class="form-inline">
                                <div class="input-group">
                                    <input type="month" name="month" class="form-control" value="<?php echo $selectedMonth; ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Lọc theo tháng
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Thẻ tổng kết -->
                    <div class="row">
                        <!-- Tổng thu -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 revenue-card">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Tổng thu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalIncome, 0, ',', '.'); ?> VND</div>
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
                            <div class="card border-left-danger shadow h-100 py-2 revenue-card">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Tổng chi</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalExpense, 0, ',', '.'); ?> VND</div>
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
                            <div class="card border-left-success shadow h-100 py-2 revenue-card">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Tổng doanh thu</div>
                                            <div class="h5 mb-0 font-weight-bold <?php echo $revenue >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($revenue, 0, ',', '.'); ?> VND
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

                    <!-- Biểu đồ doanh thu -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu theo ngày</h6>
                            <div>
                                <?php if ($hasReport): ?>
                                <span class="badge badge-info p-2 mr-2">
                                    <i class="fas fa-check-circle"></i> Báo cáo từ Kế toán
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng doanh thu theo dịch vụ -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo dịch vụ</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Dịch vụ</th>
                                            <th>Số lượng đơn</th>
                                            <th>Tổng doanh thu</th>
                                            <th>Tỷ lệ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($serviceData)): ?>
                                            <?php foreach ($serviceData as $service): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($service['serviceName']); ?></td>
                                                    <td class="text-center"><?php echo $service['bookingCount']; ?></td>
                                                    <td class="text-right"><?php echo number_format($service['totalAmount'], 0, ',', '.'); ?> VND</td>
                                                    <td class="text-center">
                                                        <?php 
                                                            $percentage = ($totalIncome > 0) ? ($service['totalAmount'] / $totalIncome * 100) : 0;
                                                            echo number_format($percentage, 1) . '%';
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Không có dữ liệu dịch vụ trong khoảng thời gian này</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            // Biểu đồ doanh thu
            var ctx = document.getElementById('revenueChart').getContext('2d');
            var chartLabels = <?php echo json_encode($chartData['labels']); ?>;
            var chartIncome = <?php echo json_encode($chartData['income']); ?>;
            var chartExpenses = <?php echo json_encode($chartData['expenses']); ?>;
            var chartRevenue = <?php echo json_encode($chartData['revenue']); ?>;
            
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Tổng thu',
                            data: chartIncome,
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Tổng chi',
                            data: chartExpenses,
                            backgroundColor: 'rgba(231, 74, 59, 0.1)',
                            borderColor: 'rgba(231, 74, 59, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Doanh thu',
                            data: chartRevenue,
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
            
            // Khởi tạo DataTable
            $('#dataTable').DataTable({
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
        });
    </script>
</body>
</html>