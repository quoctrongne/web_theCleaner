<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
require_once("../db/conn.php");

// Xử lý lọc theo tháng và năm
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Xử lý tìm kiếm
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = [];
$params = [];
$types = "";

// Thêm điều kiện tìm kiếm nếu có
if (!empty($searchKeyword)) {
    $whereClause[] = "(fullName LIKE ? OR email LIKE ?)";
    $searchParam = "%" . $searchKeyword . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "ss";
}

// Thêm điều kiện tháng nếu có
if ($selectedMonth != '') {
    $whereClause[] = "MONTH(registrationDate) = ?";
    $params[] = $selectedMonth;
    $types .= "i";
}

// Thêm điều kiện năm
$whereClause[] = "YEAR(registrationDate) = ?";
$params[] = $selectedYear;
$types .= "i";

// Tạo câu truy vấn SQL
$sql = "SELECT * FROM customers";
if (!empty($whereClause)) {
    $sql .= " WHERE " . implode(" AND ", $whereClause);
}
$sql .= " ORDER BY registrationDate DESC";

// Thực thi truy vấn
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$customers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// Đếm số lượng khách hàng hiện tại (sau khi lọc)
$customerCount = count($customers);

// Lấy tổng số khách hàng trong hệ thống
$sqlTotalCustomers = "SELECT COUNT(*) as total FROM customers";
$resultTotal = $conn->query($sqlTotalCustomers);
$totalCustomers = 0;
if ($resultTotal && $resultTotal->num_rows > 0) {
    $rowTotal = $resultTotal->fetch_assoc();
    $totalCustomers = $rowTotal['total'];
}

// Lấy danh sách các năm có trong dữ liệu
$sqlYears = "SELECT DISTINCT YEAR(registrationDate) as year FROM customers ORDER BY year DESC";
$resultYears = $conn->query($sqlYears);
$years = [];
if ($resultYears && $resultYears->num_rows > 0) {
    while ($row = $resultYears->fetch_assoc()) {
        $years[] = $row['year'];
    }
}

// Nếu không có năm nào được chọn và có dữ liệu năm, sử dụng năm gần nhất
if (empty($selectedYear) && !empty($years)) {
    $selectedYear = $years[0];
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng</title>
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
                    <h2 class="mb-4">Quản lý khách hàng</h2>

                    <!-- Bộ lọc và tìm kiếm -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tìm kiếm và lọc</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="mb-0">
                                <div class="row align-items-end">
                                    <div class="col-md-3 mb-3">
                                        <label for="month">Tháng:</label>
                                        <select name="month" id="month" class="form-control">
                                            <option value="">Tất cả các tháng</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($selectedMonth == $i) ? 'selected' : ''; ?>>
                                                    Tháng <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="year">Năm:</label>
                                        <select name="year" id="year" class="form-control">
                                            <?php foreach ($years as $year): ?>
                                                <option value="<?php echo $year; ?>" <?php echo ($selectedYear == $year) ? 'selected' : ''; ?>>
                                                    <?php echo $year; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="search">Tìm kiếm:</label>
                                        <input type="text" name="search" id="search" class="form-control" placeholder="Tìm theo tên hoặc email" value="<?php echo $searchKeyword; ?>">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i> Lọc kết quả
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách khách hàng</h6>
                            <div class="text-right">
                                <span class="badge badge-info">Tổng số: <?php echo $totalCustomers; ?> khách hàng</span>
                            </div>
                        </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Mã KH</th>
                                            <th>Họ và Tên</th>
                                            <th>Email</th>
                                            <th>Điện thoại</th>
                                            <th>Địa chỉ</th>
                                            <th>Ngày đăng ký</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($customers)) : ?>
                                            <?php foreach ($customers as $customer) : ?>
                                                <tr>
                                                    <td><?php echo $customer['customerID']; ?></td>
                                                    <td><?php echo $customer['fullName']; ?></td>
                                                    <td><?php echo $customer['email']; ?></td>
                                                    <td><?php echo $customer['phone']; ?></td>
                                                    <td><?php echo $customer['address']; ?></td>
                                                    <td><?php echo $customer['registrationDate']; ?></td>
                                                    <td>
                                                        <a href="sua_khachhang.php?id=<?php echo $customer['customerID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                        <a href="xoa_khachhang.php?id=<?php echo $customer['customerID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Không có khách hàng nào</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-right">
                                <p class="mb-0">Hiển thị <?php echo $customerCount; ?> khách hàng</p>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.container-fluid -->
            </div> <!-- /.content -->
        </div> <!-- /.content-wrapper -->
    </div> <!-- /.wrapper -->

    <!-- Script -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>