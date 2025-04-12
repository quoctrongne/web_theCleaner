<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['admin']) || !in_array($_SESSION['admin']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

$employeeID = isset($_GET['id']) ? $_GET['id'] : null;
if (!$employeeID) {
    header("Location: quanly_nhanvien.php");
    exit();
}

// Lấy thông tin nhân viên
$sql = "SELECT Users.fullName, Users.email, Users.phone, Users.address, Employees.department, Employees.hireDate 
        FROM Employees 
        JOIN Users ON Employees.employeeID = Users.userID
        WHERE Employees.employeeID = $employeeID";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $employee = $result->fetch_assoc();
} else {
    header("Location: quanly_nhanvien.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $department = htmlspecialchars(trim($_POST['department']));
    $hireDate = htmlspecialchars(trim($_POST['hireDate']));

    // Cập nhật thông tin nhân viên sử dụng prepared statement
    $sqlUpdateUser = "UPDATE Users SET fullName = ?, email = ?, phone = ?, address = ? WHERE userID = ?";
    $stmtUser = $conn->prepare($sqlUpdateUser);
    $stmtUser->bind_param("ssssi", $fullName, $email, $phone, $address, $employeeID);
    $resultUser = $stmtUser->execute();

    $sqlUpdateEmployee = "UPDATE Employees SET department = ?, hireDate = ? WHERE employeeID = ?";
    $stmtEmp = $conn->prepare($sqlUpdateEmployee);
    $stmtEmp->bind_param("ssi", $department, $hireDate, $employeeID);
    $resultEmp = $stmtEmp->execute();
    if ($conn->query($sqlUpdateUser) === TRUE && $conn->query($sqlUpdateEmployee) === TRUE) {
        header("Location: quanly_nhanvien.php");
        exit();
    } else {
        echo "Lỗi: " . $sqlUpdateUser . "<br>" . $sqlUpdateEmployee . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa nhân viên</title>
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
                    <h2 class="mb-4">Sửa nhân viên</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Họ tên:</label>
                            <input type="text" name="fullName" class="form-control" value="<?php echo $employee['fullName']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $employee['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Điện thoại:</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $employee['phone']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ:</label>
                            <input type="text" name="address" class="form-control" value="<?php echo $employee['address']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phòng ban:</label>
                            <input type="text" name="department" class="form-control" value="<?php echo $employee['department']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày thuê:</label>
                            <input type="date" name="hireDate" class="form-control" value="<?php echo $employee['hireDate']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
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