<?php
session_start();

// Kiểm tra đăng nhập cho nhân viên (Cleaner)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Cleaner') {
    header("Location: login.php"); // Chuyển hướng nếu không phải nhân viên
    exit();
}

require_once("../db/conn.php");

// Lấy ID của nhân viên đang đăng nhập
$loggedInUserID = $_SESSION['user']['userID'];

// Truy vấn lịch sử giao dịch của nhân viên
$sql = "SELECT
            th.transactionTime,
            th.transactionType,
            th.description
        FROM
            transaction_history th
        WHERE
            th.employeeID = ?
        ORDER BY
            th.transactionTime DESC"; // Sắp xếp theo thời gian mới nhất trước

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();

$transactionHistory = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactionHistory[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Lịch sử giao dịch của bạn</title>
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
            <li class="nav-item">
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
            <li class="nav-item active">
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
                    <h1 class="h3 mb-2 text-gray-800">Lịch sử giao dịch của bạn</h1>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Thời gian giao dịch</th>
                                            <th>Loại giao dịch</th>
                                            <th>Mô tả</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($transactionHistory)): ?>
                                            <tr><td colspan="3" class="text-center">Không có giao dịch nào.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($transactionHistory as $transaction): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($transaction['transactionTime']); ?></td>
                                                    <td><?php echo htmlspecialchars($transaction['transactionType']); ?></td>
                                                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
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
            <?php require("includes/footer.php"); ?>
            </div>
        </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php require("includes/logout.php"); ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>

    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>