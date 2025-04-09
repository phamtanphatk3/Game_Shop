<?php
// Thêm các header cần thiết
header('Feature-Policy: autoplay *; microphone *;');
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' * blob: data:; media-src * blob: data:;");
header('Permissions-Policy: autoplay=(self)');

session_start();
include 'config.php';

// Kiểm tra người dùng có đăng nhập không
$username = null;
$role = null;
$balance = 0; // Gán giá trị mặc định

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Truy vấn lấy thông tin người dùng
    $sql = "SELECT username, role, balance, avatar FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role, $balance, $avatar);
    $stmt->fetch();
    $stmt->close();

    // Đảm bảo balance không bị null
    if ($balance === null) {
        $balance = 0;
    }

    // Xử lý username để bỏ @gmail.com
    if ($username && strpos($username, '@gmail.com') !== false) {
        $username = substr($username, 0, strpos($username, '@gmail.com'));
    }

    // Lưu avatar vào session nếu có
    if (!empty($avatar)) {
        $_SESSION['avatar'] = $avatar;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Shop Nạp Game - Mihoyo</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="preload" href="images/audio/Music.mp3" as="audio">
    <style>
        /* Style cho video background */
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
            position: relative;
        }

        .navbar {
            background: rgba(28, 0, 91, 0.6) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 4px 30px rgba(76, 0, 159, 0.2);
        }

        .navbar-brand {
            position: relative;
            padding: 0;
            margin: 10px;
        }

        .navbar-brand::before,
        .navbar-brand::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 15px;
            background: linear-gradient(45deg, 
                #ff0000, #ff6b6b, #ffa07a,
                #ff4500, #ff0000, #ff6b6b
            );
            background-size: 400% 400%;
            animation: fireAnimation 3s ease infinite,
                       borderRotate 4s linear infinite;
            z-index: -1;
        }

        .navbar-brand::after {
            filter: blur(15px);
            opacity: 0.7;
        }

        .navbar-brand img {
            height: 50px;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 0 10px rgba(255, 0, 0, 0.5));
            transition: all 0.3s ease;
        }

        @keyframes fireAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
                filter: hue-rotate(20deg);
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes borderRotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Thêm hiệu ứng hover */
        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .navbar-brand:hover::before,
        .navbar-brand:hover::after {
            animation: fireAnimation 2s ease infinite,
                       borderRotate 3s linear infinite;
            filter: blur(20px);
            opacity: 0.9;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 10px rgba(147, 112, 219, 0.8);
        }

        .btn-primary {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.7);
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6d365, #fda085);
            border: none;
            box-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.7);
        }

        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 30px rgba(76, 0, 159, 0.5);
            background: rgba(28, 0, 91, 0.6) !important;
        }

        .card-img-top {
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
            filter: brightness(0.8);
            transition: all 0.3s ease;
        }

        .card:hover .card-img-top {
            filter: brightness(1);
        }

        .card-title {
            color: #fff;
            text-shadow: 0 0 10px rgba(147, 112, 219, 0.5);
        }

        .text-warning {
            color: #f6d365 !important;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        footer {
            background: rgba(76, 0, 159, 0.4);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(147, 112, 219, 0.2);
        }

        .footer-bottom {
            background: rgba(76, 0, 159, 0.6) !important;
            backdrop-filter: blur(10px);
        }

        /* Style cho dropdown */
        .dropdown-menu {
            background: rgba(28, 0, 91, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(147, 112, 219, 0.2);
            color: #fff !important;
            transform: translateX(5px);
        }

        /* Hiệu ứng hover cho các link */
        a:not(.btn) {
            transition: all 0.3s ease;
        }

        a:not(.btn):hover {
            color: #fff;
            text-shadow: 0 0 10px rgba(147, 112, 219, 0.8);
        }

        /* Style cho nút outline */
        .btn-outline-warning {
            border: 1px solid rgba(246, 211, 101, 0.5);
            color: #f6d365 !important;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-outline-warning:hover {
            background: linear-gradient(45deg, rgba(246, 211, 101, 0.2), rgba(253, 160, 133, 0.2));
            border-color: #f6d365;
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
        }

        /* Animation cho các phần tử */
        .card, .btn, .nav-link {
            animation: fadeInUp 0.5s ease-out;
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

        /* Hiệu ứng lá rơi */
        .leaf {
            position: fixed;
            pointer-events: none;
            z-index: 1000;
            display: block;
            animation: fall 10s linear infinite;
            opacity: 0;
        }

        @keyframes fall {
            0% {
                opacity: 1;
                top: -10%;
                transform: translateX(0) rotate(0deg) scale(0.8);
            }
            25% {
                opacity: 0.8;
                transform: translateX(100px) rotate(90deg) scale(0.9);
            }
            50% {
                opacity: 0.6;
                transform: translateX(-100px) rotate(180deg) scale(1);
            }
            75% {
                opacity: 0.4;
                transform: translateX(100px) rotate(270deg) scale(0.9);
            }
            100% {
                opacity: 0;
                top: 110%;
                transform: translateX(-100px) rotate(360deg) scale(0.8);
            }
        }

        /* Hiệu ứng lửa cho text */
        .fire-text {
            font-weight: bold;
            background: linear-gradient(120deg, #ff6b6b, #feca57, #ff9f43);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fireAnimation 3s ease infinite, textShadowPulse 2s ease infinite;
        }

        @keyframes fireAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes textShadowPulse {
            0% {
                text-shadow: 0 0 10px rgba(255, 107, 107, 0.5),
                           0 0 20px rgba(255, 107, 107, 0.3),
                           0 0 30px rgba(255, 107, 107, 0.2);
            }
            50% {
                text-shadow: 0 0 15px rgba(254, 202, 87, 0.5),
                           0 0 25px rgba(254, 202, 87, 0.3),
                           0 0 35px rgba(254, 202, 87, 0.2);
            }
            100% {
                text-shadow: 0 0 10px rgba(255, 159, 67, 0.5),
                           0 0 20px rgba(255, 159, 67, 0.3),
                           0 0 30px rgba(255, 159, 67, 0.2);
            }
        }

        /* Hiệu ứng lửa cho button */
        .btn-fire {
            background: linear-gradient(45deg, #ff6b6b, #feca57, #ff9f43);
            background-size: 200% 200%;
            animation: fireAnimation 3s ease infinite;
            border: none;
            color: white;
            text-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
            transition: all 0.3s ease;
        }

        .btn-fire:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(254, 202, 87, 0.5);
        }

        /* Hiệu ứng lửa cho card */
        .card-fire {
            border: 2px solid transparent;
            background: linear-gradient(45deg, rgba(255, 107, 107, 0.1), rgba(254, 202, 87, 0.1));
            animation: cardFireBorder 3s ease infinite;
        }

        @keyframes cardFireBorder {
            0% {
                border-color: rgba(255, 107, 107, 0.5);
                box-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
            }
            50% {
                border-color: rgba(254, 202, 87, 0.5);
                box-shadow: 0 0 20px rgba(254, 202, 87, 0.3);
            }
            100% {
                border-color: rgba(255, 159, 67, 0.5);
                box-shadow: 0 0 20px rgba(255, 159, 67, 0.3);
            }
        }
    </style>
</head>
<body>
    <!-- Thêm Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Thêm video background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="images/video/background.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="video-overlay"></div>

    <!-- Thêm iframe autoplay ẩn -->
    <iframe src="silence.mp3" allow="autoplay" id="audioFrame" style="display:none"></iframe>

    <!-- Thẻ audio chính -->
    <audio id="bgMusic" loop autoplay muted playsinline style="display: none;">
        <source src="images/audio/Music.mp3" type="audio/mp3">
    </audio>

    <!-- Thanh Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" height="50" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../pages/nap_tien.php">Nạp Tiền</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Nạp Game</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/history.php">Lịch Sử Mua</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Bán Acc</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="scrollToCayThue()">Cày Thuê</a></li>
                </ul>
                
                <?php if (!empty($username)): ?>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle ms-2 d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <?php if (!empty($_SESSION['avatar'])): ?>
                                <img src="pages/uploads/<?php echo basename(htmlspecialchars($_SESSION['avatar'])); ?>" alt="Avatar" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: linear-gradient(45deg, #6a11cb, #2575fc); color: white;">
                                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($username); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="pages/profile.php">👤 Trang cá nhân</a></li>
                            <?php if ($role === 'admin'): ?>
                                <li><a class="dropdown-item text-danger fw-bold" href="admin/admin_users.php">🔧 Quản lý Thông Tin</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="admin/manage_accounts.php">🎮 Quản lý Tài Khoản Game</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="admin/admin_transactions.php">💰 Quản lý Giao Dịch</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="admin/admin_doanhthu.php">📊 Quản Lý Doanh Thu</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="admin/admin_caythue.php">🎮 Quản Lý Cày Thuê</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger">💵 <?php echo number_format($balance, 2); ?> VNĐ</a></li>
                            <li><a class="dropdown-item text-danger" href="pages/logout.php">🚪 Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="pages/login.php" class="btn btn-warning ms-2">Đăng Nhập</a>
                    <a href="pages/register.php" class="btn btn-primary ms-2">Đăng Ký</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container text-center mt-4">
        <h1 class="fire-text">🔥 Shop Game Uy Tín - Nạp, Bán Acc, Cày Thuê 🔥</h1>
        <p class="text-light">Chất lượng - Nhanh chóng - An toàn</p>
    </div>

    <!-- Danh sách tài khoản game -->
    <div class="container text-center mt-4">
        <h2 class="text-warning" style="font-size: 2.5rem;">🎮 TÀI KHOẢN GAME 🎮</h2>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-3 mb-4">
                <div class="card bg-dark text-white shadow" style="border-radius: 15px; overflow: hidden;">
                    <img src="images/genshin.jpg" class="card-img-top" alt="Genshin Impact" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">Tài Khoản Genshin</h5>
                        <p class="card-text" style="height: 50px;">Rank 60, 15 nhân vật 5 sao, vũ khí 5 sao.</p>
                        <a href="pages/game_list1.php" class="btn btn-warning w-100" style="background: linear-gradient(45deg, #ff8f00, #ff5722); border: none;">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-dark text-white shadow" style="border-radius: 15px; overflow: hidden;">
                    <img src="images/honkai.jpg" class="card-img-top" alt="Honkai: Star Rail" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">Tài Khoản Star Rail</h5>
                        <p class="card-text" style="height: 50px;">Rank 55, nhiều nhân vật S-tier, relic max.</p>
                        <a href="pages/game_list2.php" class="btn btn-warning w-100" style="background: linear-gradient(45deg, #ff8f00, #ff5722); border: none;">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-dark text-white shadow" style="border-radius: 15px; overflow: hidden;">
                    <img src="images/zzz.jpg" class="card-img-top" alt="Zenless Zone Zero" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">Tài Khoản ZZZ</h5>
                        <p class="card-text" style="height: 50px;">Full nhân vật mạnh, vũ khí tối ưu.</p>
                        <a href="pages/game_list3.php" class="btn btn-warning w-100" style="background: linear-gradient(45deg, #ff8f00, #ff5722); border: none;">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card bg-dark text-white shadow" style="border-radius: 15px; overflow: hidden;">
                    <img src="images/www.jpg" class="card-img-top" alt="Wuthering Waves" style="height: 180px; object-fit: cover;">
                    <div class="card-body text-center">
                        <h5 class="card-title text-warning">Tài Khoản Wuthering Waves</h5>
                        <p class="card-text" style="height: 50px;">Tài khoản VIP, nhân vật hiếm, vũ khí tối thượng.</p>
                        <a href="pages/game_list4.php" class="btn btn-warning w-100" style="background: linear-gradient(45deg, #ff8f00, #ff5722); border: none;">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần SALE riêng biệt -->
    <div class="container text-center mt-5">
        <h2 class="fire-text">🔥 SALE CỰC SỐC 🔥</h2>
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card bg-dark text-white shadow">
                    <img src="../images/sale1.png" class="card-img-top" alt="Acc Sale">
                    <div class="card-body">
                        <h5 class="card-title">1</h5>
                        <p class="card-text">VIP | Full 5★</p>
                        <p class="text-warning">1.000.000.000₫ <del class="text-light">2.000.000.000₫</del></p>
                        <a href="#" class="btn btn-primary">Mua ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white shadow">
                    <img src="../images/sale1.png" class="card-img-top" alt="Acc Sale">
                    <div class="card-body">
                        <h5 class="card-title">2</h5>
                        <p class="card-text">VIP | Full 5★</p>
                        <p class="text-warning">1.000.000.000₫ <del class="text-light">2.000.000.000₫</del></p>
                        <a href="#" class="btn btn-primary">Mua ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white shadow">
                    <img src="../images/sale1.png" class="card-img-top" alt="Acc Sale">
                    <div class="card-body">
                        <h5 class="card-title">3</h5>
                        <p class="card-text">VIP | Full 5★</p>
                        <p class="text-warning">1.000.000.000₫ <del class="text-light">2.000.000.000₫</del></p>
                        <a href="#" class="btn btn-primary">Mua ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark text-white shadow">
                    <img src="../images/sale1.png" class="card-img-top" alt="Acc Sale">
                    <div class="card-body">
                        <h5 class="card-title">4</h5>
                        <p class="card-text">VIP | Full 5★</p>
                        <p class="text-warning">1.000.000.000₫ <del class="text-light">2.000.000.000₫</del></p>
                        <a href="#" class="btn btn-primary">Mua ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Phần Nạp Game -->
    <div class="container text-center mt-5" id="topup-section">
        <h2 class="fire-text">💎 NẠP GAMES NHANH CHÓNG 💎</h2>
        <p class="text-light">Nạp game uy tín - Giao dịch an toàn - Giá cả hợp lý</p>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/zzz.jpg" class="card-img-top rounded" alt="Zenless Zone Zero">
                    <div class="card-body text-center">
                        <h5 class="card-title">Zenless Zone Zero</h5>
                        <p class="text-secondary">Đã nạp: 413</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Nạp Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/www.jpg" class="card-img-top rounded" alt="Wuthering Waves">
                    <div class="card-body text-center">
                        <h5 class="card-title">Wuthering Waves</h5>
                        <p class="text-secondary">Đã nạp: 933</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Nạp Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/honkai.jpg" class="card-img-top rounded" alt="Honkai: Star Rail">
                    <div class="card-body text-center">
                        <h5 class="card-title">Honkai: Star Rail</h5>
                        <p class="text-secondary">Đã nạp: 3.864</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Nạp Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/genshin.jpg" class="card-img-top rounded" alt="Genshin Impact">
                    <div class="card-body text-center">
                        <h5 class="card-title">Genshin Impact</h5>
                        <p class="text-secondary">Đã nạp: 10.381</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Nạp Ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Phần Cày Game -->
    <div class="container text-center mt-5" id="caythue-section">
        <h2 class="fire-text">🎮 DỊCH VỤ CÀY THUÊ UY TÍN 🎮</h2>
        <p class="text-light">Cày thuê chuyên nghiệp - Giá cả hợp lý - Bảo mật an toàn</p>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/zzz.jpg" class="card-img-top rounded" alt="Zenless Zone Zero">
                    <div class="card-body text-center">
                        <h5 class="card-title">Zenless Zone Zero</h5>
                        <p class="text-secondary">Khách đã thuê: 278</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/www.jpg" class="card-img-top rounded" alt="Wuthering Waves">
                    <div class="card-body text-center">
                        <h5 class="card-title">Wuthering Waves</h5>
                        <p class="text-secondary">Khách đã thuê: 502</p>
                        <a href="../pages/cay_thue.php" class="btn btn-outline-warning w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/honkai.jpg" class="card-img-top rounded" alt="Honkai: Star Rail">
                    <div class="card-body text-center">
                        <h5 class="card-title">Honkai: Star Rail</h5>
                        <p class="text-secondary">Khách đã thuê: 1.125</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg bg-dark text-white">
                    <img src="images/genshin.jpg" class="card-img-top rounded" alt="Genshin Impact">
                    <div class="card-body text-center">
                        <h5 class="card-title">Genshin Impact</h5>
                        <p class="text-secondary">Khách đã thuê: 3.214</p>
                        <a href="#" class="btn btn-outline-warning w-100">🔹 Thuê Ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5 text-light pt-4" style="background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(10px);">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="text-info">Thông tin shop</h5>
                    <p>Shop game uy tín hàng đầu, chuyên cung cấp tài khoản chất lượng với dịch vụ tốt nhất!</p>
                    <p></p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-info">Theo dõi chúng tôi</h5>
                    <a href=""><img src="../images/face_book.png" width="30" class="me-2"></a>
                    <a href="#"><img src="../images/youtube.png" width="30"></a>
                </div>
                <div class="col-md-4">
                    <h5 class="text-info">Liên hệ</h5>
                    <p>📞 Hotline </p> 
                    <p>🕘 Giờ làm việc: 24/24h</p>
                    <p>📍 Địa chỉ: </p>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center mt-3 p-2" style="background: rgba(0, 0, 0, 0.4);">
            <p class="mb-0">© 2025 Shop Game - All rights reserved. Designed by <b>ShopGame Team</b></p>
        </div>
    </footer>
    <!-- JavaScript -->
    <script>
    function scrollToTopup() {
        var topupSection = document.getElementById("topup-section");
        if (topupSection) {
            topupSection.scrollIntoView({ behavior: "smooth" });
        }
    }
    </script>
    <script>
    function scrollToCayThue() {
        var cayThueSection = document.getElementById("caythue-section");
        if (cayThueSection) {
            cayThueSection.scrollIntoView({ behavior: "smooth" });
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/67cd7a0517609a190a8a3c09/1ilta9a12';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
    </script>

    <!-- Thêm script cho lá rơi vào cuối body trước </body> -->
    <script>
        function createLeaf() {
            const leaf = document.createElement('div');
            leaf.className = 'leaf';
            
            // Tạo các kiểu lá khác nhau
            const leafTypes = [
                '🍁', '🍂', '🌸', '✨', '💫', '❄️', '⭐'
            ];
            
            // Random style cho lá
            leaf.style.left = Math.random() * 100 + 'vw';
            leaf.style.animationDuration = Math.random() * 3 + 7 + 's'; // 7-10s
            leaf.style.fontSize = Math.random() * 15 + 15 + 'px'; // 15-30px
            
            // Random loại lá
            leaf.innerHTML = leafTypes[Math.floor(Math.random() * leafTypes.length)];
            
            // Thêm hiệu ứng phát sáng
            leaf.style.filter = 'drop-shadow(0 0 5px rgba(255, 255, 255, 0.7))';
            
            document.body.appendChild(leaf);
            
            // Xóa lá sau khi animation kết thúc
            setTimeout(() => {
                leaf.remove();
            }, parseFloat(leaf.style.animationDuration) * 1000);
        }

        // Tạo lá mới mỗi 300ms
        setInterval(createLeaf, 300);

        // Tạo một số lá ban đầu
        for(let i = 0; i < 10; i++) {
            setTimeout(createLeaf, 300 * i);
        }
    </script>

    <!-- Giữ nguyên phần script xử lý phát nhạc -->
    <script>
    // Đăng ký service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js').then(registration => {
            console.log('Service Worker registered');
        });
    }

    // Hàm phát nhạc tự động
    function initAudio() {
        const audio = document.getElementById('bgMusic');
        audio.muted = false; // Bật âm thanh sau khi tải
        
        const playAttempt = setInterval(() => {
            audio.play()
                .then(() => {
                    console.log('Đã bật nhạc thành công');
                    clearInterval(playAttempt);
                })
                .catch(error => {
                    console.log('Đang chờ kích hoạt...');
                });
        }, 1000);
    }

    // Khởi tạo khi trang tải xong
    window.addEventListener('load', initAudio);
    </script>

</body>
</html>
