<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "game_shop"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ bảng transactions
$sql = "SELECT id, account_username, account_password, created_at 
        FROM transactions 
        WHERE account_username IS NOT NULL AND account_password IS NOT NULL 
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📜 Lịch Sử Giao Dịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

        .container {
            margin-top: 50px;
            max-width: 1000px;
            animation: fadeInUp 0.5s ease-out;
        }

        .table {
            background: rgba(28, 0, 91, 0.4);
            backdrop-filter: blur(10px);
            color: white;
            border-radius: 10px;
            overflow: hidden;
            font-size: 18px;
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
        }

        .table th {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: #fff;
            text-align: center;
            padding: 15px;
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
        }

        .table td {
            text-align: center;
            vertical-align: middle;
            padding: 12px;
            border-color: rgba(147, 112, 219, 0.2);
        }

        .table-hover tbody tr:hover {
            background: rgba(147, 112, 219, 0.2);
            transition: all 0.3s ease;
        }

        .password {
            font-weight: bold;
            color: #f6d365;
            user-select: none;
            font-size: 20px;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
        }

        .btn-show {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-size: 16px;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        .btn-show:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
        }

        .btn-container {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            font-size: 18px;
            padding: 10px 20px;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            border: none;
            box-shadow: 0 0 10px rgba(106, 17, 203, 0.5);
        }

        .btn-primary:hover {
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.7);
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
        }

        .btn-success:hover {
            box-shadow: 0 0 15px rgba(40, 167, 69, 0.7);
        }

        h2 {
            color: #f6d365;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.5);
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
    </style>
</head>
<body>
    <!-- Video Background -->
    <video class="video-background" autoplay muted loop playsinline>
        <source src="../video/Wuthering Waves.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>

    <div class="container">
        <h2 class="text-center">📜 Lịch Sử Giao Dịch</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tài Khoản</th>
                        <th>Mật Khẩu</th>
                        <th>Ngày Giao Dịch</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['account_username']) ?></td>
                            <td>
                                <span class="password" id="pass-<?= $row['id'] ?>" data-password="<?= htmlspecialchars($row['account_password']) ?>">••••••••</span>
                                <button class="btn-show" onclick="togglePassword(<?= $row['id'] ?>)">👁 Hiện</button>
                            </td>
                            <td><?= date("d/m/Y H:i:s", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-container">
            <a href="../index.php" class="btn btn-primary">🏠 Quay về trang chủ</a>
            <a href="account_vip_genshin.php" class="btn btn-success">🛒 Tiếp tục mua</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(id) {
            let passSpan = document.getElementById("pass-" + id);
            if (passSpan.innerText === "••••••••") {
                passSpan.innerText = passSpan.getAttribute("data-password");
            } else {
                passSpan.innerText = "••••••••";
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
