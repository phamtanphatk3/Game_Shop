<?php
include '../config.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Lỗi: Bạn không có quyền truy cập.");
}

// Xử lý duyệt hoặc từ chối giao dịch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'], $_POST['action'])) {
    $transaction_id = $_POST['transaction_id'];
    $action = $_POST['action'];

    // Lấy thông tin giao dịch
    $sql = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    $stmt->close();

    if (!$transaction) {
        die("Giao dịch không tồn tại.");
    }

    $user_id = $transaction['user_id'];
    $amount = $transaction['amount'];

    if ($action == 'approve') {
        // Cập nhật trạng thái giao dịch thành "completed"
        $sql = "UPDATE transactions SET status = 'completed' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transaction_id);
        if (!$stmt->execute()) {
            die("Lỗi khi cập nhật trạng thái: " . $stmt->error);
        }
        $stmt->close();

        // Cộng tiền vào tài khoản của người dùng
        $sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $amount, $user_id);
        if (!$stmt->execute()) {
            die("Lỗi khi cập nhật số dư: " . $stmt->error);
        }
        $stmt->close();

        $_SESSION['success_message'] = 'Giao dịch đã được duyệt và số dư đã được cập nhật.';
        header('Location: admin_transactions.php');
        exit();
    } elseif ($action == 'reject') {
        // Cập nhật trạng thái giao dịch thành "rejected"
        $sql = "UPDATE transactions SET status = 'rejected' WHERE id = ? AND status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $transaction_id);
        if (!$stmt->execute()) {
            die("Lỗi khi cập nhật trạng thái: " . $stmt->error);
        }
        $stmt->close();

        echo "<script>alert('Giao dịch đã bị từ chối.'); window.location.href='admin_transactions.php';</script>";
        exit();
    }
}

// Lấy danh sách giao dịch
$sql = "SELECT t.id, u.username, t.payment_method, t.amount, t.status, t.created_at
        FROM transactions t
        JOIN users u ON t.user_id = u.id
        ORDER BY t.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý giao dịch</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
            background: transparent;
            min-height: 100vh;
        }

        .container {
            margin-top: 50px;
            margin-bottom: 50px;
            animation: fadeInUp 0.5s ease-out;
        }

        .table {
            background: rgba(28, 0, 91, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 0 20px rgba(76, 0, 159, 0.3);
            border-radius: 15px;
            overflow: hidden;
            color: white;
        }

        .table thead th {
            background: rgba(76, 0, 159, 0.6);
            color: #f6d365;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(246, 211, 101, 0.3);
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
            padding: 15px;
        }

        .table tbody td {
            border-color: rgba(147, 112, 219, 0.2);
            padding: 12px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(147, 112, 219, 0.1);
        }

        .btn-custom {
            border: none !important;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .btn-success.btn-custom {
            background: linear-gradient(45deg, #28a745, #20c997);
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        .btn-danger.btn-custom {
            background: linear-gradient(45deg, #dc3545, #c81e1e);
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        }

        .btn-custom:hover {
            transform: translateY(-2px);
        }

        .btn-success.btn-custom:hover {
            box-shadow: 0 0 15px rgba(40, 167, 69, 0.5);
        }

        .btn-danger.btn-custom:hover {
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.5);
        }

        .navbar {
            background: rgba(28, 0, 91, 0.6) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(147, 112, 219, 0.2);
            box-shadow: 0 4px 30px rgba(76, 0, 159, 0.2);
        }

        h2 {
            color: #f6d365;
            font-size: 2.5rem;
            text-shadow: 0 0 15px rgba(246, 211, 101, 0.5);
            margin: 30px 0;
            animation: fireAnimation 3s ease infinite;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 15px;
            font-weight: bold;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }

        .badge.bg-success {
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        }

        .badge.bg-danger {
            background: linear-gradient(45deg, #dc3545, #c81e1e) !important;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        }

        .badge.bg-warning {
            background: linear-gradient(45deg, #ffc107, #ff9800) !important;
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
        }

        @keyframes fireAnimation {
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

    <!-- Thanh điều hướng -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-home"></i> Trang chủ
            </a>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center">
            <i class="fas fa-exchange-alt"></i> Quản Lý Giao Dịch
        </h2>
        <div class="table-responsive">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Hình thức</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo ucfirst($row['payment_method']); ?></td>
                            <td>
                            <?php 
                            echo isset($row['amount']) && is_numeric($row['amount']) 
                                ? number_format($row['amount'], 0, ',', '.') . " VND" 
                                : 'N/A'; 
                            ?>
                            </td>
                            <td>
                                <span class="badge <?php 
                                    if ($row['status'] === 'completed') {
                                        echo 'bg-success';
                                    } elseif ($row['status'] === 'rejected') {
                                        echo 'bg-danger';
                                    } else {
                                        echo 'bg-warning text-dark';
                                    }
                                ?>">
                                    <?php 
                                    if ($row['status'] === 'completed') {
                                        echo '✅ Hoàn thành';
                                    } elseif ($row['status'] === 'rejected') {
                                        echo '❌ Từ chối';
                                    } else {
                                        echo '⏳ Chờ duyệt';
                                    }
                                    ?>
                                </span>
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success btn-custom">
                                            <i class="fas fa-check-circle"></i> Duyệt
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-custom">
                                            <i class="fas fa-times-circle"></i> Từ chối
                                        </button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted">Đã xử lý</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Nút quay lại trang chủ -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-custom btn-success">
                <i class="fas fa-home"></i> Quay lại Trang Chủ
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
