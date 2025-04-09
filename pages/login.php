<?php
session_start();
include '../config.php';
require_once '../config/social_login_config.php';
require_once '../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

$error = "";
$success = "";

try {
    // Khởi tạo Google Client
    $google_client = new Google_Client();
    $google_client->setClientId(GOOGLE_CLIENT_ID);
    $google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $google_client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $google_client->addScope('email');
    $google_client->addScope('profile');
    
    // Debug thông tin
    error_log("Client ID: " . GOOGLE_CLIENT_ID);
    error_log("Redirect URI: " . GOOGLE_REDIRECT_URI);
    
    $google_login_url = $google_client->createAuthUrl();
    error_log("Login URL: " . $google_login_url);
} catch (Exception $e) {
    error_log("Google login initialization error: " . $e->getMessage());
    $google_login_url = "#";
    $error = "❌ Lỗi kết nối với Google: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    // Truy vấn lấy thông tin người dùng
    $sql = "SELECT id, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashedPassword, $role);

    if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
        // Lưu thông tin vào SESSION
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;

        // Lưu thông tin đăng nhập nếu "Ghi nhớ" được chọn
        if ($remember) {
            setcookie("email", $email, time() + (86400 * 30), "/");
        } else {
            setcookie("email", "", time() - 3600, "/");
        }

        $success = "✅ Đăng nhập thành công! Đang chuyển hướng...";
        echo "<script>
                setTimeout(function() {
                    window.location.href = '../index.php';
                }, 2000);
              </script>";
    } else {
        $error = "❌ Sai email hoặc mật khẩu!";
    }

    $stmt->close();
    $conn->close();
}

// Thêm thông báo lỗi từ Google callback
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'google_error':
            $error = "❌ Lỗi đăng nhập Google: Vui lòng thử lại sau";
            break;
        case 'no_code':
            $error = "❌ Không nhận được mã xác thực từ Google";
            break;
        case 'facebook_error':
            $error = "❌ Lỗi đăng nhập Facebook: Vui lòng thử lại sau";
            break;
        case 'facebook_response_error':
            $error = "❌ Không thể kết nối với Facebook";
            break;
        case 'facebook_sdk_error':
            $error = "❌ Lỗi kết nối Facebook SDK";
            break;
        case 'no_token':
            $error = "❌ Không nhận được token xác thực";
            break;
        default:
            $error = "❌ Có lỗi xảy ra: " . $_GET['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(76, 0, 159, 0.7), rgba(28, 0, 91, 0.4));
            z-index: -1;
        }
        .card {
            background: rgba(28, 0, 91, 0.4) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
        }
        .card h2 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }
        .form-label {
            color: #f6d365;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(147, 112, 219, 0.2);
            color: #fff;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f6d365;
            color: #fff;
            box-shadow: 0 0 15px rgba(246, 211, 101, 0.3);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .form-check-label {
            color: #f6d365;
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
        a {
            color: #f6d365 !important;
            transition: all 0.3s ease;
        }
        a:hover {
            color: #fff !important;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
        }
        .text-center span {
            color: #f6d365;
        }
        .loading-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .loading-title {
            color: #f6d365;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-transform: uppercase;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
            letter-spacing: 2px;
        }
        .loading-bar-container {
            width: 60%;
            max-width: 500px;
            height: 35px;
            background: rgba(28, 0, 91, 0.4);
            border: 3px solid rgba(147, 112, 219, 0.2);
            border-radius: 20px;
            position: relative;
            box-shadow: 0 0 15px rgba(76, 0, 159, 0.3);
            padding: 3px;
            overflow: visible;
        }
        .loading-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border-radius: 15px;
            transition: width 2s cubic-bezier(0.1, 0.7, 1.0, 0.1);
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.7);
            position: relative;
        }
        .loading-character {
            position: absolute;
            top: -80px;
            left: calc(100% - 25px);
            width: 80px;
            height: 80px;
            background-image: url('../images/changli.png');
            background-size: contain;
            background-repeat: no-repeat;
            transform-origin: bottom;
            z-index: 2;
            filter: drop-shadow(2px 4px 6px rgba(0, 0, 0, 0.5));
            animation: float 1s ease-in-out infinite alternate;
        }
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }
            100% {
                transform: translateY(-10px) rotate(5deg);
            }
        }
        .loading-character::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            width: 2px;
            height: 30px;
            background: linear-gradient(to bottom, rgba(246, 211, 101, 0.8), transparent);
            transform: translateX(-50%);
            animation: stretchLine 1s ease-in-out infinite alternate;
        }
        @keyframes stretchLine {
            0% {
                height: 30px;
                opacity: 0.8;
            }
            100% {
                height: 40px;
                opacity: 0.4;
            }
        }
        .loading-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shine 1.5s infinite;
        }
        .loading-percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 16px;
            z-index: 1;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }
        .btn-google {
            background: #dd4b39;
            color: white !important;
            margin-bottom: 10px;
        }
        .btn-facebook {
            background: #3b5998;
            color: white !important;
            margin-bottom: 10px;
        }
        .btn-social {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-social:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .btn-social i {
            margin-right: 10px;
            font-size: 20px;
        }
        .btn-facebook {
            background-color: #3b5998;
            color: white;
            margin-top: 10px;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-facebook:hover {
            background-color: #2d4373;
            color: white;
        }
        .btn-facebook i {
            margin-right: 10px;
        }
        .social-login {
            margin-top: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <video class="video-background" autoplay loop muted playsinline>
        <source src="../images/video/background.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>

    <div class="loading-container">
        <div class="loading-title">HƯỚNG DẪN TRANG CHỦ</div>
        <div class="loading-bar-container">
            <div class="loading-bar">
                <div class="loading-character"></div>
            </div>
            <div class="loading-percentage">0%</div>
        </div>
    </div>

    <div class="card p-4 shadow-lg" style="width: 350px; border-radius: 10px;">
        <h2 class="text-center text-primary">🔑 Đăng Nhập</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Nhập email" value="<?php echo $_COOKIE['email'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember" <?php if (isset($_COOKIE['email'])) echo "checked"; ?>>
                    <label class="form-check-label" for="remember">Ghi nhớ</label>
                </div>
                <a href="forgot_password.php" class="text-decoration-none">Quên mật khẩu?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            
            <div class="text-center my-3">
                <span style="color: #fff;">Hoặc đăng nhập với</span>
            </div>
            
            <a href="<?php echo $google_login_url; ?>" class="btn-social btn-google">
                <i class="fab fa-google"></i> Đăng nhập với Google
            </a>

            <div class="social-login">
                <button type="button" class="btn btn-facebook" onclick="loginWithFacebook()">
                    <i class="fab fa-facebook-f"></i> Đăng nhập bằng Facebook
                </button>
            </div>

            <div class="text-center mt-3">
                <span>Chưa có tài khoản?</span>
                <a href="register.php" class="text-decoration-none">Đăng ký ngay</a>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const loadingContainer = document.querySelector('.loading-container');
        const loadingBar = document.querySelector('.loading-bar');
        const percentage = document.querySelector('.loading-percentage');
        const character = document.querySelector('.loading-character');
        
        // Hiển thị loading screen
        loadingContainer.style.display = 'flex';
        
        // Animation loading từ 0% đến 100%
        let progress = 0;
        const interval = setInterval(() => {
            progress += 1;
            loadingBar.style.width = progress + '%';
            percentage.textContent = progress + '%';
            
            // Di chuyển nhân vật theo tiến độ
            character.style.left = `calc(${progress}% - 40px)`;
            
            if (progress >= 100) {
                clearInterval(interval);
                // Chuyển hướng sau khi hoàn thành
                setTimeout(() => {
                    this.submit();
                }, 500);
            }
        }, 20); // 20ms * 100 = 2 giây để hoàn thành
    });

    // Khởi tạo Facebook SDK
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo FACEBOOK_APP_ID; ?>',
            cookie: true,
            xfbml: true,
            version: 'v19.0'
        });
    };

    function loginWithFacebook() {
        window.location.href = "<?php 
            $fb = new Facebook([
                'app_id' => FACEBOOK_APP_ID,
                'app_secret' => FACEBOOK_APP_SECRET,
                'default_graph_version' => FACEBOOK_GRAPH_VERSION,
            ]);
            $helper = $fb->getRedirectLoginHelper();
            $permissions = ['email'];
            echo $helper->getLoginUrl(FACEBOOK_REDIRECT_URI, $permissions);
        ?>";
    }
    </script>

    <!-- Thêm Font Awesome cho icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Facebook SDK -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" 
        src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v19.0&appId=<?php echo FACEBOOK_APP_ID; ?>&autoLogAppEvents=1">
    </script>
</body>
</html>
