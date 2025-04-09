<?php
session_start();
require_once '../config.php';
require_once '../config/social_login_config.php';
require_once '../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;

try {
    // Debug thông tin
    error_log("Starting Google callback process");
    error_log("Client ID: " . GOOGLE_CLIENT_ID);
    error_log("Redirect URI: " . GOOGLE_REDIRECT_URI);

    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope("email");
    $client->addScope("profile");

    if (isset($_GET['code'])) {
        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if(isset($token['error'])) {
                error_log("Token error: " . $token['error']);
                throw new Exception($token['error_description'] ?? $token['error']);
            }
            $client->setAccessToken($token);

            // Get user profile data from Google
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            
            $email = $google_account_info->email;
            $name = $google_account_info->name;
            $google_id = $google_account_info->id;
            
            // Kiểm tra email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ? OR google_id = ?");
            $stmt->bind_param("ss", $email, $google_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Email đã tồn tại - đăng nhập
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                
                // Cập nhật google_id nếu chưa có
                $update = $conn->prepare("UPDATE users SET google_id = ? WHERE id = ? AND (google_id IS NULL OR google_id = '')");
                $update->bind_param("si", $google_id, $user['id']);
                $update->execute();
            } else {
                // Email chưa tồn tại - tạo tài khoản mới
                $password = bin2hex(random_bytes(8));
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (email, username, password, role, google_id) VALUES (?, ?, ?, 'user', ?)");
                $stmt->bind_param("ssss", $email, $name, $hashed_password, $google_id);
                
                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $stmt->insert_id;
                    $_SESSION['role'] = 'user';
                } else {
                    throw new Exception("Không thể tạo tài khoản mới");
                }
            }
            
            header('Location: ../index.php');
            exit();
        } catch (Exception $e) {
            error_log("Token fetch error: " . $e->getMessage());
            header('Location: login.php?error=' . urlencode($e->getMessage()));
            exit();
        }
    } else {
        error_log("No code received from Google");
        header("Location: login.php?error=no_code");
        exit();
    }
} catch (Exception $e) {
    error_log("Google callback error: " . $e->getMessage());
    header('Location: login.php?error=' . urlencode($e->getMessage()));
    exit();
}
?> 