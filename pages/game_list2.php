<?php
session_start();
include '../config.php';

// Kiểm tra người dùng có đăng nhập không
$username = null;
$role = null;
$balance = 0;

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

    // Xử lý username để bỏ @gmail.com nếu là tài khoản Google
    if ($username && strpos($username, '@gmail.com') !== false) {
        $username = substr($username, 0, strpos($username, '@gmail.com'));
    }

    if ($balance === null) {
        $balance = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Acc Honkai: Star Rail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        }

        .navbar {
            background: rgba(28, 0, 91, 0.6) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 4px 30px rgba(76, 0, 159, 0.2);
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
        }

        .btn-custom {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.7);
        }

        h2 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }

        .card h5 {
            color: #f6d365;
            text-shadow: 0 0 5px rgba(246, 211, 101, 0.3);
        }

        footer {
            background: rgba(28, 0, 91, 0.6) !important;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(147, 112, 219, 0.2);
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../images/video/background.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a href="../index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Trang Chủ
            </a>
        </div>
    </nav>

    <div class="container text-center mt-5">
        <h2>🎮 HONKAI: STAR RAIL ACCOUNTS 🎮</h2>
        <p class="text-light mb-5">Chất lượng cao - Giao dịch an toàn - Giá tốt nhất</p>

        <div class="row mt-4">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="../images/honkai.jpg" class="card-img-top" alt="VIP Account">
                    <div class="card-body text-center">
                        <h5>Account VIP</h5>
                        <p>Số lượng: 120</p>
                        <a href="account_vip_honkai.php" class="btn btn-custom w-100">🔹 Xem Ngay</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="../images/honkai.jpg" class="card-img-top" alt="Starter Account">
                    <div class="card-body text-center">
                        <h5>Account Starter</h5>
                        <p>Số lượng: 342</p>
                        <a href="account_starter_honkai.php" class="btn btn-custom w-100">🔹 Xem Ngay</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="../images/honkai.jpg" class="card-img-top" alt="Reroll Account">
                    <div class="card-body text-center">
                        <h5>Account Reroll</h5>
                        <p>Số lượng: 587</p>
                        <a href="account_reroll_honkai.php" class="btn btn-custom w-100">🔹 Xem Ngay</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="../images/honkai.jpg" class="card-img-top" alt="Random Account">
                    <div class="card-body text-center">
                        <h5>Account Random</h5>
                        <p>Số lượng: 215</p>
                        <a href="account_random_honkai.php" class="btn btn-custom w-100">🔹 Xem Ngay</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 text-light pt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về SHOP TU TIÊN</h5>
                    <p>Shop game uy tín hàng đầu, chuyên cung cấp tài khoản chất lượng với dịch vụ tốt nhất!</p>
                    <p>© 2025 tutienshop.com</p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <a href="https://www.facebook.com/tai.ha.545849/"><img src="../images/face_book.png" width="30" class="me-2"></a>
                    <a href="#"><img src="../images/youtube.png" width="30"></a>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>📞 Hotline: 0339590149 (Hà Thanh Tài)</p>
                    <p>🕘 Giờ làm việc: 24/24h</p>
                    <p>📍 Địa chỉ: CamPuChia</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center mt-3 p-2">
            <p class="mb-0">© 2025 Shop Game - All rights reserved. Designed by <b>ShopGame Team</b></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
