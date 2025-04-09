<?php
session_start();
include '../config.php';

// Lấy tên game từ URL
$game_name = isset($_GET['game']) ? urldecode($_GET['game']) : "Không xác định";

// Các gói dịch vụ Cày Story với mức giá khác nhau
$story_services = [
    ["Hoàn thành cốt truyện chương 1", "500.000 VNĐ"],
    ["Hoàn thành cốt truyện chương 2", "700.000 VNĐ"],
    ["Hoàn thành cốt truyện chương 3", "1.000.000 VNĐ"]
];

// Xử lý khi người dùng thuê dịch vụ
if (isset($_GET['action']) && $_GET['action'] == 'rent' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $game_name = $_GET['game'];
    $story_name = $_GET['story_name'];
    $price = $_GET['price'];
    $status = "pending"; // Mặc định chờ duyệt
    $service_type = "story"; // Xác định dịch vụ là cày Story

    // Kết nối CSDL
    include '../config.php';

    // Thêm dữ liệu vào bảng `user_requests`
    $stmt = $conn->prepare("INSERT INTO user_requests (user_id, game_name, story_name, price, status, created_at, updated_at, service_type) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?)");
    $stmt->bind_param("isssss", $user_id, $game_name, $story_name, $price, $status, $service_type);

    if ($stmt->execute()) {
        echo "<script>alert('Thuê thành công! Đợi duyệt đơn nhé.'); window.location.href='cay_thue.php?game=".urlencode($game_name)."';</script>";
    } else {
        echo "<script>alert('Lỗi khi thuê dịch vụ!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game_name); ?> - Cày Story</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin-top: 50px;
        }

        .card {
            transition: transform 0.3s ease-in-out;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 30px;
            text-align: center;
            border-radius: 15px;
            background-color: #f9f9f9;
        }

        .card-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .card-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 25px;
        }

        .btn-custom {
            background-color: #ffcc00;
            color: #000;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #ffdd44;
            color: #333;
        }

        .btn-back {
            background-color: #007bff;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            padding: 10px 30px;
            border-radius: 25px;
            text-decoration: none;
            margin-bottom: 30px;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .service-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .service-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
        }

        .service-header p {
            font-size: 1.2rem;
            color: #ddd;
        }

        .lead {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #fff;
        }

        .row {
            display: flex;
            justify-content: space-evenly;
            gap: 30px;
        }

        .col-md-4 {
            flex: 1 1 calc(33.333% - 20px);
            max-width: calc(33.333% - 20px);
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                align-items: center;
            }

            .col-md-4 {
                max-width: 100%;
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>

<div class="container text-center mt-5">
    <!-- Nút Trở Về -->
    <a href="javascript:history.back()" class="btn btn-back">↩️ Trở Về</a>

    <div class="service-header">
        <h1 class="text-warning fw-bold">📌 Cày Story cho <?php echo htmlspecialchars($game_name); ?> 📌</h1>
        <p class="lead">Chọn chương bạn muốn cày và thuê ngay</p>
    </div>

    <div class="row mt-4 g-4">
        <?php foreach ($story_services as $story) { ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($story[0]); ?></h5>
                        <p class="text-secondary">Giá: <?php echo htmlspecialchars($story[1]); ?></p>
                        <a href="?action=rent&game=<?php echo urlencode($game_name); ?>&story_name=<?php echo urlencode($story[0]); ?>&price=<?php echo urlencode($story[1]); ?>" class="btn btn-custom w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
