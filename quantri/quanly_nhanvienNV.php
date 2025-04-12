<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [ 'Consultant'])) {
    header("Location: login.php");
    exit();
}

$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$sqlSearch = '';
if (!empty($searchKeyword)) {
    $sqlSearch = "WHERE department LIKE '%$searchKeyword%'";
}

$sql = "SELECT employeeID, department, hireDate FROM employees " . $sqlSearch;
$result = $conn->query($sql);
$employees = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="consultant_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">User Panel</div>
    </a>

    <hr class="sidebar-divider my-0" />

    <li class="nav-item active">
        <a class="nav-link" href="admin_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider" />

    <div class="sidebar-heading">Chức năng chính:</div>
    <li class="nav-item">
        <a class="nav-link" href="quanly_khachhangNV.php">
            <i class="fas fa-users"></i>
            <span>Quản lý khách hàng</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="quanly_nhanvienNV.php">
            <i class="fas fa-users"></i>
            <span>Quản lý nhân viên</span>
        </a>
    </li>

</ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý nhân viên</h2>
                    <form method="GET" class="mb-3 d-flex">
                        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Tìm kiếm theo phòng ban" value="<?php echo $searchKeyword; ?>">
                        <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </form>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Phòng ban</th>
                                            <th>Ngày thuê</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($employees)) : ?>
                                            <?php foreach ($employees as $employee) : ?>
                                                <tr>
                                                    <td><?php echo $employee['employeeID']; ?></td>
                                                    <td><?php echo $employee['department']; ?></td>
                                                    <td><?php echo $employee['hireDate']; ?></td>
                                                    <td>
                                                        <a href="sua_nhanvien.php?id=<?php echo $employee['employeeID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                        <a href="xoa_nhanvien.php?id=<?php echo $employee['employeeID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </a>
                                                        <a href="khoa_nhanvien.php?id=<?php echo $employee['employeeID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?')">
                                                            <i class="fas fa-lock"></i> Khóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Không có nhân viên nào</td>
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>