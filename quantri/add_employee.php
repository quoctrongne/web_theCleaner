<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra đăng nhập và quyền truy cập Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Xử lý form thêm nhân viên
if (isset($_POST['add_employee'])) {
    // Lấy dữ liệu từ form
    $fullName         = trim($_POST['fullName']);
    $email            = trim($_POST['email']);
    $password         = trim($_POST['password']);
    $phone            = trim($_POST['phone']);
    $address          = trim($_POST['address']);
    $role             = trim($_POST['role']);
    $hireDate         = trim($_POST['hireDate'] ?? '');
    $salary           = isset($_POST['salary']) ? $_POST['salary'] : 0;
    $gender           = trim($_POST['gender'] ?? '');
    $age              = isset($_POST['age']) ? $_POST['age'] : 0;
    $specialization   = trim($_POST['specialization'] ?? '');
    $experience       = trim($_POST['experience'] ?? '');
    $bio              = trim($_POST['bio'] ?? '');

    // Kiểm tra các trường bắt buộc
    if (empty($fullName) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['message'] = "Vui lòng điền đầy đủ các trường bắt buộc.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Kiểm tra xem email đã tồn tại chưa
        $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();
        
        if ($result->num_rows > 0) {
            // Email đã tồn tại, hiển thị thông báo lỗi
            $_SESSION['message'] = "Email '$email' đã tồn tại trong hệ thống. Vui lòng sử dụng email khác.";
            $_SESSION['message_type'] = "danger";
        } else {
            // Email chưa tồn tại, tiếp tục quá trình thêm nhân viên
            
            // Xử lý upload ảnh đại diện
            $avatar = "img/avatars/default.png";
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName    = $_FILES['avatar']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = 'img/avatars/';
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $avatar = $dest_path;
                    } else {
                        $_SESSION['message'] = "Lỗi khi di chuyển file upload.";
                        $_SESSION['message_type'] = "danger";
                    }
                } else {
                    $_SESSION['message'] = "Đuôi file không hợp lệ.";
                    $_SESSION['message_type'] = "danger";
                }
            }

            // Thêm vào bảng users
            $stmtUser = $conn->prepare("INSERT INTO users (fullName, password, email, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtUser->bind_param("ssssss", $fullName, $password, $email, $phone, $address, $role);

            if ($stmtUser->execute()) {
                $userID = $conn->insert_id;

                // Thêm vào bảng employees nếu cần thông tin bổ sung
                if (!empty($hireDate) || !empty($specialization) || !empty($experience)) {
                    $stmtEmp = $conn->prepare("INSERT INTO employees 
                        (userID, hireDate, salary, gender, age, specialization, experience, bio, avatar, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

                    $stmtEmp->bind_param("isdssisss", $userID, $hireDate, $salary, $gender, $age, $specialization, $experience, $bio, $avatar);

                    if ($stmtEmp->execute()) {
                        $_SESSION['message'] = "Thêm nhân viên thành công!";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Đã thêm tài khoản nhưng lỗi khi thêm thông tin bổ sung: " . $stmtEmp->error;
                        $_SESSION['message_type'] = "warning";
                    }
                    $stmtEmp->close();
                } else {
                    $_SESSION['message'] = "Thêm nhân viên thành công!";
                    $_SESSION['message_type'] = "success";
                }
            } else {
                $_SESSION['message'] = "Lỗi khi thêm tài khoản nhân viên: " . $stmtUser->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmtUser->close();
        }
        $checkEmail->close();
    }
    header("Location: managerment_employee.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm nhân viên</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sử dụng sidebar của Admin -->
        <?php require("includes/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar của Admin -->
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Thêm nhân viên</h1>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="add_.php" enctype="multipart/form-data">
                        <!-- Thông tin cơ bản -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin cơ bản</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="fullName">Họ và tên:</label>
                                    <input type="text" name="fullName" id="fullName" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Mật khẩu:</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Số điện thoại:</label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="address">Địa chỉ:</label>
                                    <textarea name="address" id="address" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="role">Vị trí công việc:</label>
                                    <select name="role" id="role" class="form-control" required onchange="toggleAdditionalInfo()">
                                        <option value="">Chọn vị trí</option>
                                        <option value="Cleaner">Cleaner (Nhân viên vệ sinh)</option>
                                        <option value="Consultant">Consultant (Tư vấn viên)</option>
                                        <option value="WarehouseStaff">WarehouseStaff (Nhân viên kho)</option>
                                        <option value="Accountant">Accountant (Kế toán)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin bổ sung cho Cleaner (ban đầu ẩn) -->
                        <div class="card shadow mb-4" id="additionalInfoCard" style="display: none;">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung cho nhân viên vệ sinh</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="hireDate">Ngày thuê:</label>
                                    <input type="date" name="hireDate" id="hireDate" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="salary">Lương:</label>
                                    <input type="number" name="salary" id="salary" class="form-control" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Giới tính:</label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">Chọn giới tính</option>
                                        <option value="Nam">Nam</option>
                                        <option value="Nữ">Nữ</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="age">Tuổi:</label>
                                    <input type="number" name="age" id="age" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="specialization">Chuyên môn:</label>
                                    <input type="text" name="specialization" id="specialization" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="experience">Kinh nghiệm:</label>
                                    <input type="text" name="experience" id="experience" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="bio">Tiểu sử / Giới thiệu:</label>
                                    <textarea name="bio" id="bio" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="avatar">Ảnh đại diện:</label>
                                    <input type="file" name="avatar" id="avatar" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Nút submit -->
                        <button type="submit" name="add_employee" class="btn btn-primary">Thêm nhân viên</button>
                    </form>
                </div> <!-- /.container-fluid -->
            </div> <!-- End of Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Bản quyền &copy; Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div> <!-- End of Content Wrapper -->
    </div> <!-- End of Page Wrapper -->
    
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