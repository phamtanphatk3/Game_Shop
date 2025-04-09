<?php
session_start();
include '../config.php';

// Lấy tên game từ URL và kiểm tra nếu không có giá trị, gán mặc định là "Không xác định"
$game_name = isset($_GET['game']) ? urldecode($_GET['game']) : "Không xác định";

// Các gói dịch vụ Cày Event với mức giá khác nhau
$event_services = [
    ["Hoàn thành sự kiện 1", "300.000 VNĐ"],
    ["Hoàn thành sự kiện 2", "500.000 VNĐ"],
    ["Hoàn thành sự kiện 3", "800.000 VNĐ"]
];

// Kiểm tra nếu người dùng bấm "Thuê Ngay"
if (isset($_GET['action']) && $_GET['action'] == 'rent') {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Bạn cần đăng nhập trước khi thuê dịch vụ!'); window.location.href='login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $service_name = isset($_GET['service_name']) ? urldecode($_GET['service_name']) : null;
    $price = isset($_GET['price']) ? urldecode($_GET['price']) : null;

    if (!$service_name || !$price) {
        echo "<script>alert('Thiếu thông tin dịch vụ. Vui lòng thử lại!');</script>";
        exit();
    }

    $is_bot = ($user_id == 0) ? 1 : 0;

    if ($conn->connect_error) {
        die("Lỗi kết nối database: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO user_requests (user_id, game_name, service_name, price, status, is_bot) VALUES (?, ?, ?, ?, ?, ?)");
    $status = 'pending';
    $stmt->bind_param("issssi", $user_id, $game_name, $service_name, $price, $status, $is_bot);

    if ($stmt->execute()) {
        echo "<script>alert('Yêu cầu của bạn đã được gửi đi và đang chờ duyệt.'); window.location.href='cay_event_www.php?game=" . urlencode($game_name) . "';</script>";
    } else {
        echo "<script>alert('Lỗi khi gửi yêu cầu: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game_name); ?> - Cày Event</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container text-center mt-5">
    <a href="javascript:history.back()" class="btn btn-primary">↩️ Trở Về</a>
    <h1 class="text-warning fw-bold">📌 Cày Event cho <?php echo htmlspecialchars($game_name); ?> 📌</h1>
    <p class="lead">Chọn sự kiện bạn muốn hoàn thành và thuê ngay</p>

    <div class="row mt-4 g-4">
        <?php foreach ($event_services as $service) { ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $service[0]; ?></h5>
                        <p class="text-secondary">Giá: <?php echo $service[1]; ?></p>
                        <a href="?action=rent&game=<?php echo urlencode($game_name); ?>&service_name=<?php echo urlencode($service[0]); ?>&price=<?php echo urlencode($service[1]); ?>" class="btn btn-warning w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>