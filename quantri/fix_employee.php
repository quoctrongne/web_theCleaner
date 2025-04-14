<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

$userID = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$userID) {
    header("Location: managerment_employee.php");
    exit();
}

// Lấy thông tin từ cả hai bảng users và employees
$sql = "SELECT u.userID, u.fullName, u.email, u.phone, u.address, u.role, 
               e.employeeID, e.hireDate, e.salary, e.gender, e.age, e.specialization, e.experience, e.bio, e.avatar
        FROM users u
        LEFT JOIN employees e ON u.userID = e.userID
        WHERE u.userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $employee = $result->fetch_assoc();
    $employeeID = $employee['employeeID']; // Có thể null nếu chưa có trong bảng employees
} else {
    header("Location: managerment_employee.php");
    exit();
}

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $role = htmlspecialchars(trim($_POST['role']));
    
    // Thông tin bổ sung cho bảng employees
    $hireDate = isset($_POST['hireDate']) ? $_POST['hireDate'] : null;
    $salary = isset($_POST['salary']) ? $_POST['salary'] : 0;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $age = isset($_POST['age']) ? $_POST['age'] : 0;
    $specialization = isset($_POST['specialization']) ? $_POST['specialization'] : '';
    $experience = isset($_POST['experience']) ? $_POST['experience'] : '';
    $bio = isset($_POST['bio']) ? $_POST['bio'] : '';

    // Cập nhật thông tin trong bảng users
    $stmtUser = $conn->prepare("UPDATE users SET fullName=?, email=?, phone=?, address=?, role=? WHERE userID=?");
    $stmtUser->bind_param("sssssi", $fullName, $email, $phone, $address, $role, $userID);
    $userUpdateSuccess = $stmtUser->execute();
    $stmtUser->close();

    // Kiểm tra và cập nhật/thêm mới vào bảng employees
    if ($employeeID) {
        // Cập nhật thông tin nếu đã tồn tại trong bảng employees
        $stmtEmp = $conn->prepare("UPDATE employees SET hireDate=?, salary=?, gender=?, age=?, specialization=?, experience=?, bio=? WHERE employeeID=?");
        $stmtEmp->bind_param("sdsisisi", $hireDate, $salary, $gender, $age, $specialization, $experience, $bio, $employeeID);
    } else {
        // Thêm mới vào bảng employees nếu chưa tồn tại
        $stmtEmp = $conn->prepare("INSERT INTO employees (userID, hireDate, salary, gender, age, specialization, experience, bio, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmtEmp->bind_param("isdsisss", $userID, $hireDate, $salary, $gender, $age, $specialization, $experience, $bio);
    }
    
    $empUpdateSuccess = $stmtEmp->execute();
    $stmtEmp->close();

    if ($userUpdateSuccess && $empUpdateSuccess) {
        $_SESSION['message'] = "Cập nhật thông tin nhân viên thành công!";
        $_SESSION['message_type'] = "success";
        header("Location: managerment_employee.php");
        exit();
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật thông tin!";
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa nhân viên</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
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
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <!-- Thông tin cơ bản -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Họ tên:</label>
                                    <input type="text" name="fullName" class="form-control" value="<?php echo htmlspecialchars($employee['fullName']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email:</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Điện thoại:</label>
                                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($employee['phone']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ:</label>
                                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($employee['address']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Vị trí công việc:</label>
                                    <select name="role" id="role" class="form-control" required onchange="toggleAdditionalInfo()">
                                        <option value="Cleaner" <?= $employee['role']=='Cleaner'?'selected':'' ?>>Cleaner (Nhân viên vệ sinh)</option>
                                        <option value="Consultant" <?= $employee['role']=='Consultant'?'selected':'' ?>>Consultant (Tư vấn viên)</option>
                                        <option value="WarehouseStaff" <?= $employee['role']=='WarehouseStaff'?'selected':'' ?>>WarehouseStaff (Nhân viên kho)</option>
                                        <option value="Accountant" <?= $employee['role']=='Accountant'?'selected':'' ?>>Accountant (Kế toán)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin bổ sung (hiển thị/ẩn dựa trên vai trò) -->
                        <div class="card shadow mb-4" id="additionalInfoCard">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung cho nhân viên vệ sinh</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Ngày thuê:</label>
                                    <input type="date" name="hireDate" class="form-control" value="<?php echo htmlspecialchars($employee['hireDate'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Lương:</label>
                                    <input type="number" name="salary" class="form-control" value="<?php echo htmlspecialchars($employee['salary'] ?? '0'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Giới tính:</label>
                                    <select name="gender" class="form-control">
                                        <option value="">-- Chọn giới tính --</option>
                                        <option value="Nam" <?= ($employee['gender'] ?? '') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                        <option value="Nữ" <?= ($employee['gender'] ?? '') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                        <option value="Khác" <?= ($employee['gender'] ?? '') == 'Khác' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Tuổi:</label>
                                    <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($employee['age'] ?? '0'); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Chuyên môn:</label>
                                    <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($employee['specialization'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Kinh nghiệm:</label>
                                    <input type="text" name="experience" class="form-control" value="<?php echo htmlspecialchars($employee['experience'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Tiểu sử:</label>
                                    <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($employee['bio'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="managerment_employee.php" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    
    <script>
    // JavaScript để hiển thị/ẩn phần thông tin bổ sung
    function toggleAdditionalInfo() {
        var roleSelect = document.getElementById('role');
        var additionalInfoCard = document.getElementById('additionalInfoCard');
        
        if (roleSelect.value === 'Cleaner') {
            additionalInfoCard.style.display = 'block';
        } else {
            additionalInfoCard.style.display = 'none';
        }
    }
    
    // Gọi hàm khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        toggleAdditionalInfo();
    });
    </script>
</body>
</html>