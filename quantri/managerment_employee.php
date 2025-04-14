<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách nhân viên vệ sinh (Cleaner) từ bảng users
$sqlCleaners = "SELECT userID, fullName, email, phone, role FROM users WHERE role = 'Cleaner'";
$resultCleaners = $conn->query($sqlCleaners);
$cleaners = [];
if ($resultCleaners && $resultCleaners->num_rows > 0) {
    while ($row = $resultCleaners->fetch_assoc()) {
        $cleaners[] = $row;
    }
}

// Lấy danh sách nhân viên kế toán (Accountant) từ bảng users
$sqlAccountants = "SELECT userID, fullName, email, phone, role FROM users WHERE role = 'Accountant'";
$resultAccountants = $conn->query($sqlAccountants);
$accountants = [];
if ($resultAccountants && $resultAccountants->num_rows > 0) {
    while ($row = $resultAccountants->fetch_assoc()) {
        $accountants[] = $row;
    }
}

// Lấy danh sách nhân viên kho (WarehouseStaff) từ bảng users
$sqlWarehouse = "SELECT userID, fullName, email, phone, role FROM users WHERE role = 'WarehouseStaff'";
$resultWarehouse = $conn->query($sqlWarehouse);
$warehouseStaffs = [];
if ($resultWarehouse && $resultWarehouse->num_rows > 0) {
    while ($row = $resultWarehouse->fetch_assoc()) {
        $warehouseStaffs[] = $row;
    }
}

// Lấy danh sách nhân viên tư vấn (Consultant) từ bảng users
$sqlConsultants = "SELECT userID, fullName, email, phone, role FROM users WHERE role = 'Consultant'";
$resultConsultants = $conn->query($sqlConsultants);
$consultants = [];
if ($resultConsultants && $resultConsultants->num_rows > 0) {
    while ($row = $resultConsultants->fetch_assoc()) {
        $consultants[] = $row;
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
        <?php require("includes/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý nhân viên</h2>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <a href="add_employee.php" class="btn btn-primary mb-3">Thêm nhân viên</a>
                    
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="cleaners-tab" data-toggle="tab" href="#cleaners" role="tab">
                                Nhân viên vệ sinh (<?php echo count($cleaners); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="warehouse-tab" data-toggle="tab" href="#warehouse" role="tab">
                                Nhân viên kho (<?php echo count($warehouseStaffs); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="accountants-tab" data-toggle="tab" href="#accountants" role="tab">
                                Nhân viên kế toán (<?php echo count($accountants); ?>)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="consultants-tab" data-toggle="tab" href="#consultants" role="tab">
                                Nhân viên tư vấn (<?php echo count($consultants); ?>)
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- Nhân viên vệ sinh (Cleaner) -->
                        <div class="tab-pane fade show active" id="cleaners" role="tabpanel">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên vệ sinh</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Email</th>
                                                    <th>Điện thoại</th>
                                                    <th>Vai trò</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($cleaners)) : ?>
                                                    <?php foreach ($cleaners as $employee) : ?>
                                                        <tr>
                                                            <td><?php echo $employee['userID']; ?></td>
                                                            <td><?php echo $employee['fullName']; ?></td>
                                                            <td><?php echo $employee['email']; ?></td>
                                                            <td><?php echo $employee['phone']; ?></td>
                                                            <td><?php echo $employee['role']; ?></td>
                                                            <td>
                                                                <a href="sua_nhanvien.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                                <a href="xoa_nhanvien.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')"><i class="fas fa-trash-alt"></i> Xóa</a>
                                                                <a href="khoa_nhanvien.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?')"><i class="fas fa-lock"></i> Khóa</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Không có nhân viên vệ sinh nào</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nhân viên kho (WarehouseStaff) -->
                        <div class="tab-pane fade" id="warehouse" role="tabpanel">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên kho</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Email</th>
                                                    <th>Điện thoại</th>
                                                    <th>Vai trò</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($warehouseStaffs)) : ?>
                                                    <?php foreach ($warehouseStaffs as $employee) : ?>
                                                        <tr>
                                                            <td><?php echo $employee['userID']; ?></td>
                                                            <td><?php echo $employee['fullName']; ?></td>
                                                            <td><?php echo $employee['email']; ?></td>
                                                            <td><?php echo $employee['phone']; ?></td>
                                                            <td><?php echo $employee['role']; ?></td>
                                                            <td>
                                                                <a href="fix_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                                <a href="delete_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')"><i class="fas fa-trash-alt"></i> Xóa</a>
                                                                <a href="lock_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?')"><i class="fas fa-lock"></i> Khóa</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Không có nhân viên kho nào</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nhân viên kế toán (Accountant) -->
                        <div class="tab-pane fade" id="accountants" role="tabpanel">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên kế toán</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Email</th>
                                                    <th>Điện thoại</th>
                                                    <th>Vai trò</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($accountants)) : ?>
                                                    <?php foreach ($accountants as $employee) : ?>
                                                        <tr>
                                                            <td><?php echo $employee['userID']; ?></td>
                                                            <td><?php echo $employee['fullName']; ?></td>
                                                            <td><?php echo $employee['email']; ?></td>
                                                            <td><?php echo $employee['phone']; ?></td>
                                                            <td><?php echo $employee['role']; ?></td>
                                                            <td>
                                                                <a href="fix_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                                <a href="delete_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')"><i class="fas fa-trash-alt"></i> Xóa</a>
                                                                <a href="lock_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?')"><i class="fas fa-lock"></i> Khóa</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Không có nhân viên kế toán nào</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nhân viên tư vấn (Consultant) -->
                        <div class="tab-pane fade" id="consultants" role="tabpanel">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên tư vấn</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Họ tên</th>
                                                    <th>Email</th>
                                                    <th>Điện thoại</th>
                                                    <th>Vai trò</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($consultants)) : ?>
                                                    <?php foreach ($consultants as $employee) : ?>
                                                        <tr>
                                                            <td><?php echo $employee['userID']; ?></td>
                                                            <td><?php echo $employee['fullName']; ?></td>
                                                            <td><?php echo $employee['email']; ?></td>
                                                            <td><?php echo $employee['phone']; ?></td>
                                                            <td><?php echo $employee['role']; ?></td>
                                                            <td>
                                                                <a href="fix_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                                <a href="delete_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này không?')"><i class="fas fa-trash-alt"></i> Xóa</a>
                                                                <a href="lock_employee.php?id=<?php echo $employee['userID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?')"><i class="fas fa-lock"></i> Khóa</a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Không có nhân viên tư vấn nào</td>
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
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>