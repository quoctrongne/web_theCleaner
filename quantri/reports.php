<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

/* ----------------------
   1. Lấy dữ liệu phiếu nhập kho (Stock Input)
      - Nếu có bộ lọc thời gian từ GET (input_start_date và input_end_date) thì áp dụng.
---------------------- */
if (isset($_GET['input_start_date']) && isset($_GET['input_end_date']) && !empty($_GET['input_start_date']) && !empty($_GET['input_end_date'])) {
    $inputStart = $_GET['input_start_date'];
    $inputEnd   = $_GET['input_end_date'];
    $sqlInputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                       FROM reports r
                       JOIN Inventory i ON r.inventory_id = i.inventory_id
                       WHERE r.description LIKE 'Phiếu nhập kho:%' 
                         AND r.report_date BETWEEN ? AND ?
                       ORDER BY r.report_date DESC";
    $stmtInput = $conn->prepare($sqlInputReport);
    $stmtInput->bind_param("ss", $inputStart, $inputEnd);
    $stmtInput->execute();
    $resultInputReport = $stmtInput->get_result();
    $inputReports = [];
    if ($resultInputReport && $resultInputReport->num_rows > 0) {
        while ($row = $resultInputReport->fetch_assoc()) {
            $inputReports[] = $row;
        }
    }
    $stmtInput->close();
} else {
    // Nếu chưa lọc thì lấy tất cả các phiếu nhập kho (hoặc có thể giới hạn theo ngày hiện tại nếu cần)
    $sqlInputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                       FROM reports r
                       JOIN Inventory i ON r.inventory_id = i.inventory_id
                       WHERE r.description LIKE 'Phiếu nhập kho:%'
                       ORDER BY r.report_date DESC";
    $resultInputReport = $conn->query($sqlInputReport);
    $inputReports = [];
    if ($resultInputReport && $resultInputReport->num_rows > 0) {
        while ($row = $resultInputReport->fetch_assoc()) {
            $inputReports[] = $row;
        }
    }
}

/* ----------------------
   2. Lấy dữ liệu phiếu xuất kho (Stock Output)
      - Nếu có bộ lọc thời gian từ GET (output_start_date và output_end_date) thì áp dụng.
---------------------- */
if (isset($_GET['output_start_date']) && isset($_GET['output_end_date']) && !empty($_GET['output_start_date']) && !empty($_GET['output_end_date'])) {
    $outputStart = $_GET['output_start_date'];
    $outputEnd   = $_GET['output_end_date'];
    $sqlOutputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                        FROM reports r
                        JOIN Inventory i ON r.inventory_id = i.inventory_id
                        WHERE r.description NOT LIKE 'Phiếu nhập kho:%'
                          AND r.report_date BETWEEN ? AND ?
                        ORDER BY r.report_date DESC";
    $stmtOutput = $conn->prepare($sqlOutputReport);
    $stmtOutput->bind_param("ss", $outputStart, $outputEnd);
    $stmtOutput->execute();
    $resultOutputReport = $stmtOutput->get_result();
    $outputReports = [];
    if ($resultOutputReport && $resultOutputReport->num_rows > 0) {
        while ($row = $resultOutputReport->fetch_assoc()) {
            $outputReports[] = $row;
        }
    }
    $stmtOutput->close();
} else {
    $sqlOutputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                        FROM reports r
                        JOIN Inventory i ON r.inventory_id = i.inventory_id
                        WHERE r.description NOT LIKE 'Phiếu nhập kho:%'
                        ORDER BY r.report_date DESC";
    $resultOutputReport = $conn->query($sqlOutputReport);
    $outputReports = [];
    if ($resultOutputReport && $resultOutputReport->num_rows > 0) {
        while ($row = $resultOutputReport->fetch_assoc()) {
            $outputReports[] = $row;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu xuất – nhập kho</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .small-form { max-width: 150px; display: inline-block; }
        .table-section { margin-bottom: 30px; }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar (Admin) -->
        <?php require("includes/sidebar.php"); ?>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar (Admin) -->
                <?php require("includes/topbar.php"); ?>

                <div class="container-fluid">
                    <h2 class="mb-4">Phiếu xuất – nhập kho</h2>
                    
                    <!-- Hiển thị thông báo nếu có -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php 
                        unset($_SESSION['message']); 
                        unset($_SESSION['message_type']); 
                        ?>
                    <?php endif; ?>

                    <!-- Bộ lọc phiếu nhập kho -->
                    <div class="card shadow mb-4 table-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lọc phiếu nhập kho</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="reports.php">
                                <div class="form-group">
                                    <label for="input_start_date">Từ ngày:</label>
                                    <input type="date" name="input_start_date" id="input_start_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="input_end_date">Đến ngày:</label>
                                    <input type="date" name="input_end_date" id="input_end_date" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Lọc phiếu nhập kho</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng Phiếu nhập kho -->
                    <div class="card shadow mb-4 table-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Phiếu nhập kho</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($inputReports)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Tên vật tư</th>
                                                <th>Số lượng nhập</th>
                                                <th>Số lượng còn lại</th>
                                                <th>Ngày nhập kho</th>
                                                <th>Mô tả</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inputReports as $record): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($record['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['consumed']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['remaining']); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($record['report_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($record['description']); ?></td>
                                                    <td>
                                                        <?php if ($record['status'] === 'Pending'): ?>
                                                            <!-- Form xác nhận phiếu nhập kho -->
                                                            <form method="POST" action="confirm_report.php" class="small-form">
                                                                <input type="hidden" name="reportID" value="<?php echo $record['report_id']; ?>">
                                                                <button type="submit" name="confirm_report" class="btn btn-sm btn-success">Xác nhận</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">Đã xác nhận</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có phiếu nhập kho nào.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Bộ lọc phiếu xuất kho -->
                    <div class="card shadow mb-4 table-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lọc phiếu xuất kho</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="reports.php">
                                <div class="form-group">
                                    <label for="output_start_date">Từ ngày:</label>
                                    <input type="date" name="output_start_date" id="output_start_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="output_end_date">Đến ngày:</label>
                                    <input type="date" name="output_end_date" id="output_end_date" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Lọc phiếu xuất kho</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng Phiếu xuất kho -->
                    <div class="card shadow mb-4 table-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Phiếu xuất kho</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($outputReports)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Tên vật tư</th>
                                                <th>Số lượng tiêu thụ</th>
                                                <th>Số lượng còn lại</th>
                                                <th>Ngày giao dịch</th>
                                                <th>Mô tả</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($outputReports as $record): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($record['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['consumed']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['remaining']); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($record['report_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($record['description']); ?></td>
                                                    <td>
                                                        <?php if ($record['status'] === 'Pending'): ?>
                                                            <!-- Form xác nhận phiếu xuất kho -->
                                                            <form method="POST" action="confirm_report.php" class="small-form">
                                                                <input type="hidden" name="reportID" value="<?php echo $record['report_id']; ?>">
                                                                <button type="submit" name="confirm_report" class="btn btn-sm btn-success">Xác nhận</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">Đã xác nhận</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có phiếu xuất kho nào.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div><!-- /.container-fluid -->
            </div><!-- End of Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div><!-- End of Content Wrapper -->
    </div><!-- End of Page Wrapper -->

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
