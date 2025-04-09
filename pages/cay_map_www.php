<?php
session_start();
include '../config.php';

// Lấy tên game từ URL và kiểm tra nếu không có giá trị, gán mặc định là "Không xác định"
$game_name = isset($_GET['game']) ? urldecode($_GET['game']) : "Không xác định";

// Các gói dịch vụ Cày Map với mức giá khác nhau
$map_services = [
    ["Mở khóa bản đồ khu vực 1", "300.000 VNĐ", "/images/map/norfall_barrens.png"],
    ["Mở khóa bản đồ khu vực 2", "500.000 VNĐ", "/images/map/desorock_highland.png"],
    ["Mở khóa bản đồ khu vực 3", "800.000 VNĐ", "/images/map/dim_forest.png"],
    ["Mở khóa bản đồ khu vực 4", "1.000.000 VNĐ", "/images/map/gorges_of_sprits.png"],
    ["Mở khóa bản đồ khu vực 5", "1.500.000 VNĐ", "/images/map/jinzshou.png"],
    ["Mở khóa bản đồ khu vực 6", "2.000.000 VNĐ", "/images/map/whining_aix's_mine.png"],
    ["Mở khóa bản đồ khu vực 7", "2.500.000 VNĐ", "/images/map/port_city_of_guixu.png"],
    ["Mở khóa bản đồ khu vực 8", "3.000.000 VNĐ", "/images/map/wuming_bay.png"],
    ["Mở khóa bản đồ khu vực 9", "3.500.000 VNĐ", "/images/map/tiger's_maw.png"],
    ["Mở khóa bản đồ khu vực 10", "4.000.000 VNĐ", "/images/map/central_plains.png"],
    ["Mở khóa bản đồ khu vực 11", "4.000.000 VNĐ", "/images/map/eternal_caverns.png"],
    ["Mở khóa bản đồ khu vực 12", "4.000.000 VNĐ", "/images/map/eternal_caverns.png"]
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
    $image_path = isset($_GET['image']) ? urldecode($_GET['image']) : null; // Lưu đường dẫn ảnh

    if (!$service_name || !$price || !$image_path) {
        echo "<script>alert('Thiếu thông tin dịch vụ. Vui lòng thử lại!');</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO user_requests (user_id, game_name, service_type, price, status, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $status = 'pending';
    $stmt->bind_param("isssss", $user_id, $game_name, $service_name, $price, $status, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Yêu cầu của bạn đã được gửi đi.'); window.location.href='cay_thue.php?game=" . urlencode($game_name) . "';</script>";
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
    <title><?php echo htmlspecialchars($game_name); ?> - Cày Map</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            object-fit: cover;
        }

        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(76, 0, 159, 0.7), rgba(28, 0, 91, 0.4));
            z-index: -1;
        }

        body {
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: transparent;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            animation: fadeInUp 0.5s ease-out;
        }

        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
        }

        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .card-body {
            background: rgba(28, 0, 91, 0.6);
            border-top: 1px solid rgba(147, 112, 219, 0.2);
            padding: 20px;
        }

        .card-title {
            color: #f6d365;
            font-size: 1.3rem;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
            margin-bottom: 15px;
        }

        .text-secondary {
            color: #fff !important;
            opacity: 0.8;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6d365, #fda085);
            color: #000;
            font-weight: bold;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(246, 211, 101, 0.5);
            background: linear-gradient(45deg, #fda085, #f6d365);
        }

        .btn-primary {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: #fff;
            font-weight: bold;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(106, 17, 203, 0.5);
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            color: #fff;
        }

        h1 {
            color: #f6d365;
            font-size: 2.5rem;
            text-shadow: 0 0 15px rgba(246, 211, 101, 0.5);
            margin-bottom: 20px;
        }

        .lead {
            color: #fff;
            font-size: 1.3rem;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            margin-bottom: 40px;
            opacity: 0.9;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../video/Wuthering Waves.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <div class="container text-center">
        <a href="javascript:history.back()" class="btn btn-primary">↩️ Trở Về</a>
        <h1 class="fw-bold floating">📌 Cày Map cho <?php echo htmlspecialchars($game_name); ?> 📌</h1>
        <p class="lead">Chọn khu vực bạn muốn mở khóa và cày thuê ngay</p>

        <div class="row g-4">
            <?php foreach ($map_services as $service) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($service[2]); ?>" class="card-img-top" alt="Bản đồ <?php echo htmlspecialchars($service[0]); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $service[0]; ?></h5>
                            <p class="text-secondary">Giá: <?php echo $service[1]; ?></p>
                            <a href="?action=rent&game=<?php echo urlencode($game_name); ?>&service_name=<?php echo urlencode($service[0]); ?>&price=<?php echo urlencode($service[1]); ?>&image=<?php echo urlencode($service[2]); ?>" class="btn btn-warning w-100">🔹 Thuê Ngay</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
