<?php
session_start();
require_once '../config.php';
require_once '../config/social_login_config.php';
require_once '../vendor/autoload.php';

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

try {
    $fb = new Facebook([
        'app_id' => FACEBOOK_APP_ID,
        'app_secret' => FACEBOOK_APP_SECRET,
        'default_graph_version' => FACEBOOK_GRAPH_VERSION,
    ]);

    $helper = $fb->getRedirectLoginHelper();
    
    try {
        $accessToken = $helper->getAccessToken();
        if (!$accessToken) {
            throw new Exception('Failed to get access token');
        }

        // Get user data
        $response = $fb->get('/me?fields=id,name,email', $accessToken);
        $user = $response->getGraphUser();
        
        $facebook_id = $user->getId();
        $name = $user->getName();
        $email = $user->getEmail();

        // Kiểm tra user đã tồn tại
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE email = ? OR facebook_id = ?");
        $stmt->bind_param("ss", $email, $facebook_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User đã tồn tại
            $user_data = $result->fetch_assoc();
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['role'] = $user_data['role'];

            // Cập nhật facebook_id nếu chưa có
            $update = $conn->prepare("UPDATE users SET facebook_id = ? WHERE id = ? AND (facebook_id IS NULL OR facebook_id = '')");
            $update->bind_param("si", $facebook_id, $user_data['id']);
            $update->execute();
        } else {
            // Tạo user mới
            $password = bin2hex(random_bytes(8));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (email, username, password, role, facebook_id) VALUES (?, ?, ?, 'user', ?)");
            $stmt->bind_param("ssss", $email, $name, $hashed_password, $facebook_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['role'] = 'user';
            } else {
                throw new Exception("Không thể tạo tài khoản mới");
            }
        }

        header('Location: ../index.php');
        exit();

    } catch(FacebookResponseException $e) {
        error_log('Facebook Response Exception: ' . $e->getMessage());
        header('Location: login.php?error=facebook_response_error');
        exit();
    } catch(FacebookSDKException $e) {
        error_log('Facebook SDK Exception: ' . $e->getMessage());
        header('Location: login.php?error=facebook_sdk_error');
        exit();
    }

} catch(Exception $e) {
    error_log('Facebook login error: ' . $e->getMessage());
    header('Location: login.php?error=facebook_error');
    exit();
}
?> 