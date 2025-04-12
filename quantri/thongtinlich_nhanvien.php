<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Cleaner'])) {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once("../db/conn.php");

// Lấy thông tin nhân viên đang đăng nhập
if (isset($_SESSION['user']['employeeID'])) {
    $loggedInEmployeeID = $_SESSION['user']['employeeID'];

    // Lấy thông tin lịch làm việc của nhân viên vệ sinh cụ thể và trạng thái công việc
    $workSchedules = [];
    $sqlWorkSchedule = "SELECT
        ws.workScheduleID,
        c.fullName AS customerName,
        c.address AS customerAddress,
        s.name AS serviceName,
        ws.shiftDate,
        ws.shiftTime,
        ws.status AS workStatus
    FROM WorkSchedule ws
    JOIN Customers c ON ws.customerID = c.customerID
    JOIN Services s ON ws.serviceID = s.serviceID
    WHERE ws.employeeID = ?
    ORDER BY ws.shiftDate DESC";
    $stmtWorkSchedule = $conn->prepare($sqlWorkSchedule);
    $stmtWorkSchedule->bind_param("i", $loggedInEmployeeID);
    $stmtWorkSchedule->execute();
    $resultWorkSchedule = $stmtWorkSchedule->get_result();

    if ($resultWorkSchedule && $resultWorkSchedule->num_rows > 0) {
        while ($row = $resultWorkSchedule->fetch_assoc()) {
            $workSchedules[] = $row;
        }
    }

    $stmtWorkSchedule->close();
} else {
    // Xử lý trường hợp không có employeeID trong session (có thể log lỗi hoặc redirect)
    error_log("Không tìm thấy employeeID trong session.");
    // Ví dụ: header("Location: error.php"); exit();
    $workSchedules = []; // Gán một mảng rỗng để tránh lỗi hiển thị
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Lịch làm việc của bạn</title>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="cleaner_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-broom"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Cleaner Panel</div>
            </a>

            <hr class="sidebar-divider my-0" />

            <li class="nav-item active">
                <a class="nav-link" href="cleaner_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider" />

            <div class="sidebar-heading">Chức năng chính:</div>
            <li class="nav-item">
                <a class="nav-link" href="thongtinlich_nhanvien.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Thông tin lịch làm việc</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lichsu_GDNV.php">
                    <i class="fas fa-history"></i>
                    <span>Lịch sử giao dịch</span>
                </a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>

                <div class="container-fluid">
                    <h2 class="mb-4">Lịch làm việc của bạn</h2>
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Mã lịch</th>
                                            <th>Khách hàng</th>
                                            <th>Địa chỉ</th>
                                            <th>Dịch vụ</th>
                                            <th>Ngày làm việc</th>
                                            <th>Ca làm việc</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($workSchedules)): ?>
                                            <tr><td colspan="8" class="text-center">Không có lịch làm việc nào.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($workSchedules as $schedule): ?>
                                                <tr>
                                                    <td><?php echo $schedule['workScheduleID']; ?></td>
                                                    <td><?php echo $schedule['customerName']; ?></td>
                                                    <td><?php echo $schedule['customerAddress']; ?></td>
                                                    <td><?php echo $schedule['serviceName']; ?></td>
                                                    <td><?php echo $schedule['shiftDate']; ?></td>
                                                    <td><?php echo $schedule['shiftTime']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($schedule['workStatus'] == 'pending') {
                                                            echo '<span class="badge badge-warning">Chưa hoàn thành</span>';
                                                        } elseif ($schedule['workStatus'] == 'completed') {
                                                            echo '<span class="badge badge-success">Đã hoàn thành</span>';
                                                        } elseif ($schedule['workStatus'] == 'ongoing') {
                                                            echo '<span class="badge badge-info">Đang thực hiện</span>';
                                                        } else {
                                                            echo '<span class="badge badge-secondary">' . $schedule['workStatus'] . '</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($schedule['workStatus'] == 'pending' || $schedule['workStatus'] == 'ongoing'): ?>
                                                            <a href="capnhat_trangthai_lich.php?id=<?php echo $schedule['workScheduleID']; ?>" class="btn btn-sm btn-info">Cập nhật trạng thái</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
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

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>