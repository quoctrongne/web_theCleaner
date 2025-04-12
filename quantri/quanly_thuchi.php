<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập cho nhân viên kế toán
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

// Lấy giá trị lọc theo tháng (định dạng Y-m); mặc định là tháng hiện tại
$dateFilter = isset($_GET['date']) ? $_GET['date'] : date('Y-m');

// --- Tính số liệu thu --- //
// Tổng doanh thu (tức là tổng tiền khách hàng thanh toán dịch vụ) được lấy từ bảng bookings
$sqlRevenue = "SELECT SUM(totalAmount) AS revenue 
               FROM bookings 
               WHERE DATE_FORMAT(bookingDate, '%Y-%m') = '$dateFilter'";
$resultRevenue = $conn->query($sqlRevenue);
$rowRevenue = $resultRevenue->fetch_assoc();
$totalRevenue = $rowRevenue['revenue'] ?? 0;

// --- Tính số liệu chi --- //
// Tổng chi được tính từ bảng thuchi, chỉ tính các giao dịch có loai = 'Chi'
$sqlExpense = "SELECT SUM(soTien) AS expense 
               FROM thuchi 
               WHERE loai = 'Chi' 
                 AND DATE_FORMAT(ngayGiaoDich, '%Y-%m') = '$dateFilter'";
$resultExpense = $conn->query($sqlExpense);
$rowExpense = $resultExpense->fetch_assoc();
$totalExpense = $rowExpense['expense'] ?? 0;

// Số dư (Thu - Chi)
$netAmount = $totalRevenue - $totalExpense;

// --- Xử lý các thao tác C,R,U,D cho khoản Chi --- //

// Thêm khoản chi
if (isset($_POST['add_chi'])) {
    $ngayGiaoDich = $_POST['ngayGiaoDichChi'];
    $noiDung = $_POST['noiDungChi'];
    $soTien = $_POST['soTienChi'];
    $hinhThuc = $_POST['hinhThucChi'];
    
    $sqlInsertChi = "INSERT INTO thuchi (loai, ngayGiaoDich, noiDung, soTien, hinhThuc) 
                     VALUES ('Chi', ?, ?, ?, ?)";
    $stmtInsertChi = $conn->prepare($sqlInsertChi);
    $stmtInsertChi->bind_param("ssds", $ngayGiaoDich, $noiDung, $soTien, $hinhThuc);
    if ($stmtInsertChi->execute()) {
        $_SESSION['message'] = "Thêm khoản chi thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm khoản chi: " . $stmtInsertChi->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtInsertChi->close();
    header("Location: quanly_thuchi.php?date=" . $dateFilter);
    exit();
}

// Sửa khoản chi
if (isset($_POST['edit_chuchi'])) {
    $thuchiID = $_POST['thuchiID'];
    $ngayGiaoDich = $_POST['ngayGiaoDich'];
    $noiDung = $_POST['noiDung'];
    $soTien = $_POST['soTien'];
    $hinhThuc = $_POST['hinhThuc'];
    
    $sqlUpdate = "UPDATE thuchi 
                  SET ngayGiaoDich = ?, noiDung = ?, soTien = ?, hinhThuc = ? 
                  WHERE thuchiID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssdsi", $ngayGiaoDich, $noiDung, $soTien, $hinhThuc, $thuchiID);
    if ($stmtUpdate->execute()) {
        $_SESSION['message'] = "Cập nhật khoản chi thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật khoản chi: " . $stmtUpdate->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtUpdate->close();
    header("Location: quanly_thuchi.php?date=" . $dateFilter);
    exit();
}

// Xóa khoản chi
if (isset($_GET['delete_id'])) {
    $thuchiID = $_GET['delete_id'];
    $sqlDelete = "DELETE FROM thuchi WHERE thuchiID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $thuchiID);
    if ($stmtDelete->execute()) {
        $_SESSION['message'] = "Xóa khoản chi thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa khoản chi: " . $stmtDelete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtDelete->close();
    header("Location: quanly_thuchi.php?date=" . $dateFilter);
    exit();
}

// Lấy thông tin khoản chi cần sửa (nếu có)
$chiToEdit = null;
if (isset($_GET['edit_id'])) {
    $editID = $_GET['edit_id'];
    $sqlEdit = "SELECT * FROM thuchi WHERE thuchiID = ? AND loai = 'Chi'";
    $stmtEdit = $conn->prepare($sqlEdit);
    $stmtEdit->bind_param("i", $editID);
    $stmtEdit->execute();
    $resultEdit = $stmtEdit->get_result();
    if ($resultEdit->num_rows == 1) {
        $chiToEdit = $resultEdit->fetch_assoc();
    }
    $stmtEdit->close();
}

// Lấy danh sách các khoản chi của tháng hiện tại (cho bảng hiển thị)
$sqlExpenseRecords = "SELECT * FROM thuchi 
                      WHERE loai = 'Chi' AND DATE_FORMAT(ngayGiaoDich, '%Y-%m') = '$dateFilter' 
                      ORDER BY ngayGiaoDich DESC";
$resultExpenseRecords = $conn->query($sqlExpenseRecords);
$expenseRecords = [];
if ($resultExpenseRecords && $resultExpenseRecords->num_rows > 0) {
    while ($row = $resultExpenseRecords->fetch_assoc()) {
        $expenseRecords[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Thu Chi</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .me-2 { margin-right: 0.5rem; }
  </style>
</head>
<body id="page-top">
  <div id="wrapper">
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="ketoan_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kế toán</div>
    </a>
    <hr class="sidebar-divider my-0">
    
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="ketoan_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    
    <!-- Heading -->
    <div class="sidebar-heading">
        Chức năng chính:
    </div>
    
    <!-- Quản lý hóa đơn -->
    <li class="nav-item">
        <a class="nav-link" href="quanly_hoadon.php">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Quản lý hóa đơn</span>
        </a>
    </li>
    
    <!-- Quản lý thu chi -->
    <li class="nav-item">
        <a class="nav-link" href="quanly_thuchi.php">
            <i class="fas fa-money-bill-wave"></i>
            <span>Quản lý thu chi</span>
        </a>
    </li>
    
    <!-- Lập báo cáo -->
    <li class="nav-item">
        <a class="nav-link" href="lap_baocao.php">
            <i class="fas fa-chart-line"></i>
            <span>Lập báo cáo</span>
        </a>
    </li>
    
    <hr class="sidebar-divider d-none d-md-block">
    
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo $_SESSION['user']['fullName']; ?>
                </span>
                <!-- Thay thế đường dẫn avatar bằng đường dẫn thực tế -->
                <img class="img-profile rounded-circle" src="path/to/your/avatar.png" alt="Avatar">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
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
            <h2 class="mb-4">Quản lý Thu Chi</h2>
            <!-- Bộ lọc theo tháng -->
            <form method="GET" class="mb-3 d-flex">
              <input type="month" name="date" class="form-control w-25 me-2" value="<?php echo $dateFilter; ?>">
              <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Lọc</button>
            </form>
            <!-- Summary Cards -->
            <div class="row">
              <div class="col-md-4">
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng tiền thanh toán dịch vụ</h6>
                  </div>
                  <div class="card-body text-center">
                    <h3 class="text-success font-weight-bold">
                      <?php echo number_format($totalRevenue, 0); ?> VND
                    </h3>
                    <p class="mb-0">Tháng <?php echo $dateFilter; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tổng chi</h6>
                  </div>
                  <div class="card-body text-center">
                    <h3 class="text-danger font-weight-bold">
                      <?php echo number_format($totalExpense, 0); ?> VND
                    </h3>
                    <p class="mb-0">Tháng <?php echo $dateFilter; ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card shadow mb-4">
                  <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Số dư (Thu - Chi)</h6>
                  </div>
                  <div class="card-body text-center">
                    <h3 class="font-weight-bold" style="color: <?php echo ($netAmount >= 0 ? 'green' : 'red'); ?>;">
                      <?php echo number_format($netAmount, 0); ?> VND
                    </h3>
                    <p class="mb-0">Tháng <?php echo $dateFilter; ?></p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Bảng danh sách khoản chi -->
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách khoản chi</h6>
                <button class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#addChiModal">
                  <i class="fas fa-plus"></i> Thêm chi
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Ngày giao dịch</th>
                        <th>Nội dung</th>
                        <th>Số tiền</th>
                        <th>Hình thức</th>
                        <th>Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($expenseRecords as $record): ?>
                        <tr>
                          <td><?php echo $record['thuchiID']; ?></td>
                          <td><?php echo date("d/m/Y", strtotime($record['ngayGiaoDich'])); ?></td>
                          <td><?php echo htmlspecialchars($record['noiDung']); ?></td>
                          <td><?php echo number_format($record['soTien'], 0); ?></td>
                          <td><?php echo htmlspecialchars($record['hinhThuc']); ?></td>
                          <td>
                            <a href="quanly_thuchi.php?edit_id=<?php echo $record['thuchiID']; ?>&date=<?php echo $dateFilter; ?>" class="btn btn-primary btn-sm">
                              <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="quanly_thuchi.php?delete_id=<?php echo $record['thuchiID']; ?>&date=<?php echo $dateFilter; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khoản này?')">
                              <i class="fas fa-trash"></i> Xóa
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
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
             <span>Bản quyền &copy; Your Website <?php echo date('Y'); ?></span>
           </div>
         </div>
      </footer>
    </div>
  </div>

  <!-- Modal thêm khoản chi -->
  <div class="modal fade" id="addChiModal" tabindex="-1" role="dialog" aria-labelledby="addChiModalLabel" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="addChiModalLabel">Thêm khoản chi</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <form method="POST" action="quanly_thuchi.php?date=<?php echo $dateFilter; ?>">
                 <div class="modal-body">
                     <div class="form-group">
                         <label for="ngayGiaoDichChi">Ngày giao dịch:</label>
                         <input type="date" class="form-control" id="ngayGiaoDichChi" name="ngayGiaoDichChi" required>
                     </div>
                     <div class="form-group">
                         <label for="noiDungChi">Nội dung:</label>
                         <textarea class="form-control" id="noiDungChi" name="noiDungChi" rows="3" required></textarea>
                     </div>
                     <div class="form-group">
                         <label for="soTienChi">Số tiền:</label>
                         <input type="number" class="form-control" id="soTienChi" name="soTienChi" step="0.01" required>
                     </div>
                     <div class="form-group">
                         <label for="hinhThucChi">Hình thức:</label>
                         <input type="text" class="form-control" id="hinhThucChi" name="hinhThucChi">
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                     <button type="submit" class="btn btn-info" name="add_chi">Thêm chi</button>
                 </div>
             </form>
         </div>
     </div>
  </div>

  <?php if ($chiToEdit): ?>
  <!-- Modal sửa khoản chi -->
  <div class="modal fade show" id="editChiModal" tabindex="-1" role="dialog" aria-labelledby="editChiModalLabel" aria-hidden="true" style="display: block;">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="editChiModalLabel">Sửa khoản chi</h5>
                  <button type="button" class="close" onclick="window.location.href='quanly_thuchi.php?date=<?php echo $dateFilter; ?>'" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <form method="POST" action="quanly_thuchi.php?date=<?php echo $dateFilter; ?>">
                  <input type="hidden" name="thuchiID" value="<?php echo $chiToEdit['thuchiID']; ?>">
                  <div class="modal-body">
                      <div class="form-group">
                          <label for="ngayGiaoDich">Ngày giao dịch:</label>
                          <input type="date" class="form-control" id="ngayGiaoDich" name="ngayGiaoDich" value="<?php echo htmlspecialchars($chiToEdit['ngayGiaoDich']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label for="noiDung">Nội dung:</label>
                          <textarea class="form-control" id="noiDung" name="noiDung" rows="3" required><?php echo htmlspecialchars($chiToEdit['noiDung']); ?></textarea>
                      </div>
                      <div class="form-group">
                          <label for="soTien">Số tiền:</label>
                          <input type="number" class="form-control" id="soTien" name="soTien" step="0.01" value="<?php echo htmlspecialchars($chiToEdit['soTien']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label for="hinhThuc">Hình thức:</label>
                          <input type="text" class="form-control" id="hinhThuc" name="hinhThuc" value="<?php echo htmlspecialchars($chiToEdit['hinhThuc']); ?>">
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" onclick="window.location.href='quanly_thuchi.php?date=<?php echo $dateFilter; ?>'">Đóng</button>
                      <button type="submit" class="btn btn-primary" name="edit_chuchi">Lưu thay đổi</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
  <?php endif; ?>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/sb-admin-2.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
