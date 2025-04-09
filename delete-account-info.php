<?php
session_start();
require_once 'config.php';
require_once 'config/social_login_config.php';

header('Content-Type: application/json');

// Xác thực yêu cầu từ Facebook
$signed_request = $_POST['signed_request'];
if (!$signed_request) {
    http_response_code(400);
    echo json_encode(['error' => 'No signed request received']);
    exit;
}

// Giải mã signed_request
list($encoded_sig, $payload) = explode('.', $signed_request, 2);
$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

// Xác thực user_id
if (!isset($data['user_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No user ID in signed request']);
    exit;
}

try {
    // Kiểm tra xem người dùng có tồn tại trong database không
    $stmt = $conn->prepare("SELECT id FROM users WHERE facebook_id = ?");
    $stmt->bind_param("s", $data['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Xóa hoặc ẩn danh hóa dữ liệu người dùng
        $user = $result->fetch_assoc();
        
        // Cập nhật thông tin người dùng thành dữ liệu ẩn danh
        $update = $conn->prepare("UPDATE users SET 
            username = 'Deleted User',
            email = NULL,
            facebook_id = NULL,
            google_id = NULL,
            avatar = 'default.jpg',
            is_deleted = 1,
            deleted_at = NOW()
            WHERE id = ?");
        $update->bind_param("i", $user['id']);
        $update->execute();

        // Phản hồi thành công cho Facebook
        echo json_encode([
            'url' => 'http://localhost/game_shop/privacy-policy.php',
            'confirmation_code' => md5($data['user_id'] . time())
        ]);
    } else {
        // Người dùng không tồn tại trong database
        echo json_encode([
            'url' => 'http://localhost/game_shop/privacy-policy.php',
            'confirmation_code' => 'user_not_found'
        ]);
    }
} catch (Exception $e) {
    error_log("Delete account error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?> 