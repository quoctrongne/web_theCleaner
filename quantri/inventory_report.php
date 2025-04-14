<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập cho nhân viên kho (WarehouseStaff)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['WarehouseStaff'])) {
    header("Location: login.php");
    exit();
}

// ----------------------
// Xử lý cập nhật tiêu thụ vật tư (khi nhân viên kho submit form cập nhật)
// ----------------------
if (isset($_POST['update_consumption'])) {
    $itemName    = $_POST['itemName'];
    $consumed    = $_POST['consumed'];
    $description = $_POST['description']; // Lấy mô tả tiêu thụ
    // Lấy trạng thái từ form, mặc định là "Pending"
    $status = isset($_POST['status']) ? $_POST['status'] : 'Pending';
    
    // Lấy inventory_id và số lượng từ bảng Inventory dựa trên itemName
    $sqlCheck = "SELECT inventory_id, quantity FROM Inventory WHERE itemName = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $itemName);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    
    if ($resultCheck->num_rows > 0) {
        $row = $resultCheck->fetch_assoc();
        $inventoryID    = $row['inventory_id'];
        $currentQuantity = $row['quantity'];
        
        // Kiểm tra số lượng tiêu thụ không vượt quá tồn kho
        if ($consumed > $currentQuantity) {
            $_SESSION['message'] = "Số lượng tiêu thụ không được vượt quá tồn kho hiện tại!";
            $_SESSION['message_type'] = "danger";
        } else {
            $newQuantity = $currentQuantity - $consumed;
            
            // Cập nhật số lượng mới vào bảng Inventory
            $sqlUpdate = "UPDATE Inventory SET quantity = ? WHERE inventory_id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ii", $newQuantity, $inventoryID);
            if ($stmtUpdate->execute()) {
                // Insert phiếu xuất kho vào bảng reports với khóa ngoại inventory_id và mô tả tiêu thụ
                $sqlInsertReport = "INSERT INTO reports (inventory_id, consumed, remaining, status, report_date, description) VALUES (?, ?, ?, ?, CURDATE(), ?)";
                $stmtInsertReport = $conn->prepare($sqlInsertReport);
                $stmtInsertReport->bind_param("iiiss", $inventoryID, $consumed, $newQuantity, $status, $description);
                if ($stmtInsertReport->execute()) {
                    $_SESSION['message'] = "Tiêu thụ của '{$itemName}' được cập nhật thành công và phiếu xuất kho đã được tạo!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Tiêu thụ đã cập nhật nhưng xảy ra lỗi khi tạo phiếu xuất kho: " . $stmtInsertReport->error;
                    $_SESSION['message_type'] = "danger";
                }
                $stmtInsertReport->close();
            } else {
                $_SESSION['message'] = "Lỗi cập nhật tiêu thụ: " . $stmtUpdate->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmtUpdate->close();
        }
    } else {
        $_SESSION['message'] = "Không tìm thấy vật tư '{$itemName}' trong kho.";
        $_SESSION['message_type'] = "danger";
    }
    $stmtCheck->close();
    header("Location: inventory_report.php");
    exit();
}

// ----------------------
// Lấy dữ liệu tồn kho hôm nay từ bảng Inventory
// ----------------------
$reportItems = [];
$sqlReport = "SELECT itemName, SUM(quantity) AS dailyQuantity 
              FROM Inventory 
              WHERE DATE(inventoryDate) = CURDATE() 
              GROUP BY itemName";
$resultReport = $conn->query($sqlReport);
if ($resultReport && $resultReport->num_rows > 0) {
    while ($row = $resultReport->fetch_assoc()) {
        $reportItems[] = $row;
    }
}

// ----------------------
// Lấy dữ liệu các phiếu xuất kho đã được Admin xác nhận từ bảng reports
// JOIN với bảng Inventory để lấy tên vật tư
$confirmedReports = [];
if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $filterStart = $_GET['start_date'];
    $filterEnd   = $_GET['end_date'];
    $sqlConfirmed = "SELECT i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                     FROM reports r 
                     JOIN Inventory i ON r.inventory_id = i.inventory_id 
                     WHERE r.status = 'Confirmed' AND r.report_date BETWEEN ? AND ?
                     ORDER BY r.report_date DESC";
    $stmtConfirmed = $conn->prepare($sqlConfirmed);
    $stmtConfirmed->bind_param("ss", $filterStart, $filterEnd);
    $stmtConfirmed->execute();
    $resultConfirmed = $stmtConfirmed->get_result();
    if ($resultConfirmed && $resultConfirmed->num_rows > 0) {
        while ($row = $resultConfirmed->fetch_assoc()) {
            $confirmedReports[] = $row;
        }
    }
    $stmtConfirmed->close();
} else {
    // Mặc định: hiển thị các phiếu xuất kho của hôm nay
    $sqlConfirmed = "SELECT i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                     FROM reports r 
                     JOIN Inventory i ON r.inventory_id = i.inventory_id 
                     WHERE r.status = 'Confirmed' AND DATE(r.report_date) = CURDATE()
                     ORDER BY r.report_date DESC";
    $resultConfirmed = $conn->query($sqlConfirmed);
    if ($resultConfirmed && $resultConfirmed->num_rows > 0) {
        while ($row = $resultConfirmed->fetch_assoc()) {
            $confirmedReports[] = $row;
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo vật tư - Kiểm kê</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .small-form { max-width: 150px; display: inline-block; }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="warehouse_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kho</div>
            </a>
            <hr class="sidebar-divider my-0" />
            <li class="nav-item">
                <a class="nav-link" href="warehouse_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider" />
          
            <li class="nav-item">
                <a class="nav-link" href="inventory_check.php">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Kiểm kê vật tư</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory_management.php">
                    <i class="fas fa-boxes"></i>
                    <span>Quản lý kho</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory_report.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo kho</span>
                </a>
            </li>
        </ul>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Báo cáo vật tư </h2>
                    
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
                    
                    <!-- Bảng cập nhật tiêu thụ tồn kho cho nhân viên kho -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Cập nhật tiêu thụ tồn kho</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tên vật tư</th>
                                            <th>Tổng số lượng</th>
                                            <th>Nhập số lượng tiêu thụ</th>
                                            <th>Lý do tiêu thụ</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($reportItems)): ?>
                                            <?php foreach ($reportItems as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['dailyQuantity']); ?></td>
                                                    <td>
                                                        <!-- Form nhập số lượng tiêu thụ -->
                                                        <form method="POST" action="inventory_report.php" class="small-form">
                                                            <input type="hidden" name="itemName" value="<?php echo htmlspecialchars($item['itemName']); ?>">
                                                            <input type="number" name="consumed" class="form-control" placeholder="SL tiêu thụ" required>
                                                    </td>
                                                    <td>
                                                            <input type="text" name="description" class="form-control" placeholder="Lý do tiêu thụ" required>
                                                            <!-- Trạng thái mặc định "Pending" ẩn -->
                                                            <input type="hidden" name="status" value="Pending">
                                                    </td>
                                                    <td>
                                                            <button type="submit" name="update_consumption" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Không có dữ liệu tồn kho.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-3 text-muted">Lưu ý: Nhập số lượng tiêu thụ dương. Hệ thống sẽ trừ số lượng đó khỏi tồn kho và tạo phiếu xuất kho (trạng thái mặc định: "Pending").</p>
                        </div>
                    </div>
                    
                    <!-- Form lọc phiếu xuất kho đã được Admin xác nhận -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lọc phiếu xuất kho đã xác nhận</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="inventory_report.php">
                                <div class="form-group">
                                    <label for="start_date">Từ ngày:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Đến ngày:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Lọc</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Bảng hiển thị các phiếu xuất kho đã được Admin xác nhận -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Phiếu xuất kho đã xác nhận</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($confirmedReports)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Tên vật tư</th>
                                                <th>Số lượng tiêu thụ</th>
                                                <th>Số lượng còn lại</th>
                                                <th>Ngày giao dịch</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($confirmedReports as $record): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($record['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['consumed']); ?></td>
                                                    <td><?php echo htmlspecialchars($record['remaining']); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($record['report_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có phiếu xuất kho nào được Admin xác nhận theo khoảng thời gian đã lọc.</p>
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
