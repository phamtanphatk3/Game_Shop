<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendResetEmail($email, $token) {
    try {
        $mail = new PHPMailer(true);

        // Bật debug để xem lỗi
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'taiha80999@gmail.com';
        $mail->Password = 'sltl ynre mxqx eitx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Cấu hình bổ sung
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;
        
        // Thêm các tùy chọn SSL
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Thông tin gửi/nhận
        $mail->setFrom('taiha80999@gmail.com', 'Shop Game');
        $mail->addAddress($email);
        
        // Nội dung
        $mail->isHTML(true);
        $mail->Subject = '=?UTF-8?B?'.base64_encode('Đặt Lại Mật Khẩu - Shop Game').'?=';
        
        $reset_link = "http://localhost/game_shop/pages/reset_password.php?token=" . $token;
        
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #6a11cb;">Đặt Lại Mật Khẩu</h2>
            <p>Xin chào,</p>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            <p>Vui lòng click vào link bên dưới để đặt lại mật khẩu:</p>
            <p><a href="' . $reset_link . '" style="display: inline-block; padding: 10px 20px; background: linear-gradient(45deg, #6a11cb, #2575fc); color: white; text-decoration: none; border-radius: 5px;">Đặt Lại Mật Khẩu</a></p>
            <p>Link này sẽ hết hạn sau 15 phút.</p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
            <hr>
            <p style="color: #666; font-size: 12px;">Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>';

        // Gửi email và kiểm tra kết quả
        if (!$mail->send()) {
            error_log("Lỗi gửi mail: " . $mail->ErrorInfo);
            throw new Exception($mail->ErrorInfo);
        }

        error_log("Đã gửi email thành công đến: " . $email);
        return true;

    } catch (Exception $e) {
        error_log("Chi tiết lỗi SMTP: " . $e->getMessage());
        return false;
    }
}
?>
