<?php
session_start();

$errorMsg = "";

if (isset($_POST["btSubmit"])) {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    require_once("../db/conn.php");

    // Truy vấn thông tin người dùng từ bảng Users dựa trên email
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->errno) {
        $errorMsg = "Lỗi truy vấn: " . $stmt->error;
    } else {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // So sánh mật khẩu trực tiếp (không an toàn!)
            if ($password === $row['password']) {

                // Xác định vai trò của người dùng và chuyển hướng tương ứng
                switch ($row['role']) {
                    case 'Admin':
                        $_SESSION['user'] = $row;
                        header("Location: admin_dashboard.php");
                        exit();
                
                    case 'Consultant':
                        $_SESSION['user'] = $row;
                        header("Location: consultant_dashboard.php");
                        exit();
                    case 'Cleaner':
                        $_SESSION['user'] = $row;
                        header("Location: cleaner_dashboard.php");
                        exit();
                    case 'WarehouseStaff':
                        $_SESSION['user'] = $row;
                        header("Location: warehouse_dashboard.php");
                        exit();
                    case 'Accountant':
                        $_SESSION['user'] = $row;
                        header("Location: accountant_dashboard.php");
                        exit();
                    default:
                        $errorMsg = "Vai trò người dùng không hợp lệ.";
                        break;
                }
            } else {
                $errorMsg = "Mật khẩu không chính xác.";
            }
        } else {
            $errorMsg = "Không tìm thấy tài khoản với email này.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Đăng nhập vào hệ thống</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-8 offset-lg-2">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login Form</h1>
                                        <?php if ($errorMsg): ?>
                                            <h4 class="alert alert-danger"><?php echo $errorMsg; ?></h4>
                                        <?php endif; ?>
                                    </div>
                                    <form class="user" method="post" action="">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user" placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
                                        </div>
                                        <button name="btSubmit" class="btn btn-primary btn-user btn-block">Login</button>
                                        <hr>
                                    </form>
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