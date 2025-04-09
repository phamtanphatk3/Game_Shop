<?php
session_start();
include '../config.php';

// Lấy tên game từ URL
$game_name = isset($_GET['game']) ? urldecode($_GET['game']) : "Không xác định";

$services = [
    "Cày Event" => ["description" => "Hoàn thành sự kiện trong game, nhận phần thưởng.", "page" => "cay_event_www.php"],
    "Cày Story" => ["description" => "Hoàn thành cốt truyện chính hoặc mở khóa chapter.", "page" => "cay_story_www.php"],
    "Cày Map" => ["description" => "Mở khóa bản đồ, farm tài nguyên, hoàn thành nhiệm vụ thế giới.", "page" => "cay_map_www.php"]
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game_name); ?> - Dịch Vụ Cày Thuê</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Màu nền sáng, dễ nhìn */
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 1200px;
        }

        .card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            background-color: #ffffff;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); /* Thêm độ nổi khi hover */
        }

        .card-body {
            padding: 25px;
            text-align: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .card-text {
            font-size: 1rem;
            color: #555; /* Màu chữ nhẹ nhàng */
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #FFC107; /* Vàng tươi */
            color: #333;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 30px;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #FFB300;
            color: #fff;
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

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }

        .section-header p {
            font-size: 1.2rem;
            color: #555;
        }

        /* Layout */
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

    <div class="section-header">
        <h1 class="text-warning fw-bold">📌 Dịch Vụ Cày Thuê cho <?php echo htmlspecialchars($game_name); ?> 📌</h1>
        <p>Chọn dịch vụ mà bạn muốn tham gia và thuê ngay!</p>
    </div>

    <div class="row">
        <?php foreach ($services as $service_name => $service) { ?>
            <div class="col-md-4">
                <div class="card border-0">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $service_name; ?></h5>
                        <p class="card-text"><?php echo $service['description']; ?></p>
                        <a href="<?php echo $service['page']; ?>?game=<?php echo urlencode($game_name); ?>" class="btn btn-custom w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
