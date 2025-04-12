<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['admin']) || !in_array($_SESSION['admin']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $department = $_POST['department'];
    $hireDate = $_POST['hireDate'];
    $role = $_POST['role'];

    // Kiểm tra xem email có tồn tại trong cơ sở dữ liệu không
    $checkEmailQuery = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email đã tồn tại. Vui lòng sử dụng email khác.";
        header("Location: them_nhanvien.php");
        exit();
    }

    // Thêm người dùng vào bảng Users
    $sqlUser = "INSERT INTO Users (fullName, email, password, phone, address, role) VALUES ('$fullName', '$email', '$password', '$phone', '$address', '$role')";
    if ($conn->query($sqlUser) === TRUE) {
        $userID = $conn->insert_id;

        // Thêm nhân viên vào bảng Employees nếu vai trò không phải Admin hoặc Khachhang
        if ($role !== 'Admin' && $role !== 'Khachhang') {
            $sqlEmployee = "INSERT INTO Employees (employeeID, department, hireDate, roles) VALUES ($userID, '$department', '$hireDate', '$roles')";
            if ($conn->query($sqlEmployee) !== TRUE) {
                echo "Lỗi: " . $sqlEmployee . "<br>" . $conn->error;
            }
        }
        header("Location: quanly_nhanvien.php");
        exit();
    } else {
        echo "Lỗi: " . $sqlUser . "<br>" . $conn->error;
    }
}

$conn->close();
?>

?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial--scale=1.0">
    <title>Thêm nhân viên</title>
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
                    <h2 class="mb-4">Thêm nhân viên</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label>Họ tên:</label>
                            <input type="text" name="fullName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Điện thoại:</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ:</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phòng ban:</label>
                            <input type="text" name="department" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Ngày thuê:</label>
                            <input type="date" name="hireDate" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Vai trò:</label>
                            <select name="role" class="form-control" required>
                                <option value="Employee">Nhân viên</option>
                                <option value="WarehouseStaff">Nhân viên kho</option>
                                <option value="Cleaner">Nhân viên vệ sinh</option>
                                <option value="Accountant">Kế toán</option>
                                <option value="Consultant">Tư vấn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Thêm</button>
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