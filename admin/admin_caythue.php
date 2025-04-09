<?php
session_start();
include '../config.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch pending requests from the database
$query = "SELECT ur.id, ur.user_id, ur.game_name, ur.service_type, ur.status, ur.created_at, ur.image_path, u.username
          FROM user_requests ur
          JOIN users u ON ur.user_id = u.id
          WHERE ur.status = 'pending'";


// Execute the query and check for errors
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle request approval or rejection
if (isset($_GET['approve'])) {
    $request_id = $_GET['approve'];
    $update_query = "UPDATE user_requests SET status = 'approved' WHERE id = $request_id";
    $conn->query($update_query);
    header("Location: admin_CayThue.php"); // Refresh the page after update
    exit;
}

if (isset($_GET['reject'])) {
    $request_id = $_GET['reject'];
    $update_query = "UPDATE user_requests SET status = 'rejected' WHERE id = $request_id";
    $conn->query($update_query);
    header("Location: admin_CayThue.php"); // Refresh the page after update
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Duyệt Cày Thuê</title>
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

        .table img {
            border-radius: 10px;
            border: 2px solid rgba(147, 112, 219, 0.2);
            transition: all 0.3s ease;
        }

        .table img:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(76, 0, 159, 0.5);
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

        .btn-back {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: #fff;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 0 15px rgba(106, 17, 203, 0.3);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(106, 17, 203, 0.5);
            color: #fff;
        }

        h1 {
            color: #f6d365;
            font-size: 2.5rem;
            text-shadow: 0 0 15px rgba(246, 211, 101, 0.5);
            margin: 30px 0;
        }

        .lead {
            color: #fff;
            font-size: 1.3rem;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            margin-bottom: 40px;
            opacity: 0.9;
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
        <!-- Nút Trở Về -->
        <a href="../index.php" class="btn-back">🏠 Trở Về Home</a>
        
        <h1 class="fw-bold floating">📌 Duyệt Yêu Cầu Cày Thuê</h1>
        <p class="lead">Danh sách các yêu cầu thuê cày game đang chờ duyệt.</p>

        <!-- Pending Requests Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Người dùng</th>
                        <th>Tên Game</th>
                        <th>Dịch vụ</th>
                        <th>Ảnh</th>
                        <th>Ngày yêu cầu</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo htmlspecialchars($row['game_name']); ?></td>
                                <td><?php echo ucfirst($row['service_type']); ?></td>
                                <td>
                                    <?php if (!empty($row['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Ảnh dịch vụ" width="100">
                                    <?php else: ?>
                                        Không có ảnh
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <a href="admin_CayThue.php?approve=<?php echo $row['id']; ?>" class="btn btn-success btn-custom">Duyệt</a>
                                    <a href="admin_CayThue.php?reject=<?php echo $row['id']; ?>" class="btn btn-danger btn-custom">Từ chối</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Không có yêu cầu nào đang chờ duyệt.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
