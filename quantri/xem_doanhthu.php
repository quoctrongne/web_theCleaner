<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Lọc doanh thu theo ngày hoặc tháng
$dateFilter = isset($_GET['date']) ? $_GET['date'] : date('Y-m');
$sql = "SELECT SUM(totalAmount) AS revenue FROM bookings WHERE DATE_FORMAT(bookingDate, '%Y-%m') = '$dateFilter'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$totalRevenue = $row['revenue'] ?? 0;
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem doanh thu</title>
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
                    <h2 class="mb-4">Xem doanh thu</h2>
                    <form method="GET" class="mb-3 d-flex">
                        <input type="month" name="date" class="form-control w-25 me-2" value="<?php echo $dateFilter; ?>">
                        <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> Lọc</button>
                    </form>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tổng doanh thu tháng <?php echo $dateFilter; ?></h6>
                        </div>
                        <div class="card-body text-center">
                            <h3 class="text-success font-weight-bold"><?php echo number_format($totalRevenue); ?> VND</h3>
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
