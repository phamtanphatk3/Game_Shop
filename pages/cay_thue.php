<?php
session_start();
include '../config.php';

// Kiểm tra nếu người dùng đã đăng nhập
$username = null;
$role = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Truy vấn lấy thông tin người dùng
    $sql = "SELECT username, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch Vụ Cày Thuê</title>
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
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
        }

        .card img {
            height: 200px;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .card:hover img {
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
            font-size: 1.4rem;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
            margin-bottom: 15px;
        }

        .text-secondary {
            color: #fff !important;
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .btn-custom {
            background: linear-gradient(45deg, #f6d365, #fda085);
            color: #000;
            font-weight: bold;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(246, 211, 101, 0.5);
            background: linear-gradient(45deg, #fda085, #f6d365);
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
        }

        .home-btn {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 0 20px rgba(106, 17, 203, 0.4);
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 50px;
        }

        .home-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 30px rgba(106, 17, 203, 0.6);
            color: #fff;
        }

        .service-section {
            padding: 20px 0;
        }

        .game-card {
            margin-bottom: 30px;
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
        <h1 class="fw-bold floating">🎮 DỊCH VỤ CÀY THUÊ UY TÍN 🎮</h1>
        <p class="lead">Cày thuê chuyên nghiệp - Giá cả hợp lý - Bảo mật an toàn</p>

        <!-- Nút Home -->
        <a href="../index.php" class="home-btn">🏠 Trang Chủ</a>

        <!-- Dịch Vụ Cày Event -->
        <div class="service-section">
            <div class="row g-4">
                <?php
                $games_event = [
                    ["Genshin Impact", "../images/genshin.jpg", "3.214", "cay_thue_game_www.php"],
                    ["Honkai: Star Rail", "../images/honkai.jpg", "1.125", "cay_thue_game_honkai.php"],
                    ["Zenless Zone Zero", "../images/zzz.jpg", "278", "cay_thue_game_zenless.php"]
                ];

                foreach ($games_event as $game) {
                    echo '<div class="col-md-4 game-card">
                            <div class="card">
                                <img src="' . $game[1] . '" class="card-img-top" alt="' . $game[0] . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . $game[0] . '</h5>
                                    <p class="text-secondary">Khách đã thuê: ' . $game[2] . '</p>
                                    <a href="' . $game[3] . '?game=' . urlencode($game[0]) . '" class="btn btn-custom w-100">🔹 Thuê Ngay</a>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
