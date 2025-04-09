<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "game_shop";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xóa tài khoản nếu có yêu cầu
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM accounts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: manage_accounts.php");
    exit();
}

// Lấy loại game từ tham số URL
$game_type = isset($_GET['game']) ? $_GET['game'] : 'genshin';

// Xác định tiêu đề và điều kiện SQL dựa trên loại game
switch($game_type) {
    case 'genshin':
        $title = "Genshin Impact";
        $condition = "game_type = 'genshin'";
        break;
    case 'honkai':
        $title = "Honkai: Star Rail";
        $condition = "game_type = 'honkai'";
        break;
    case 'zzz':
        $title = "Zenless Zone Zero";
        $condition = "game_type = 'zzz'";
        break;
    case 'www':
        $title = "Wuthering Waves";
        $condition = "game_type = 'www'";
        break;
    default:
        $title = "Genshin Impact";
        $condition = "game_type = 'genshin'";
}

// Lấy danh sách tài khoản theo loại game
$sql = "SELECT * FROM accounts WHERE $condition AND type IN ('VIP', 'STARTER', 'REROLL', 'RANDOM')";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài khoản <?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Background động */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #1a1a2e, #16213e, #0f3460, #533483);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            color: white;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Hiệu ứng bảng */
        table {
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
        }

        table.table-dark thead {
            background: rgba(0, 0, 0, 0.5);
        }

        table.table-dark tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transition: 0.3s;
        }

        /* Hiệu ứng nút */
        .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.2);
        }

        /* Container bo góc */
        .container {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.3);
        }

        /* Ảnh tài khoản */
        .account-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ffcc00;
        }

        /* Mô tả tài khoản nổi bật */
        .description {
        display: block;
        max-width: 120px; /* Độ rộng tương đương cột VIP */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 14px;
        font-weight: bold;
        color: #00ffff;
        text-shadow: 2px 2px 5px rgba(0, 255, 255, 0.5);
        background: rgba(0, 255, 255, 0.1);
        padding: 8px;
        border-radius: 8px;
    }

    /* Thêm style cho nút chọn game */
    .game-selector {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .game-selector .btn {
        margin: 0 5px;
        padding: 10px 20px;
        font-weight: bold;
    }

    .game-selector .btn.active {
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-warning text-center flex-grow-1">🔥 Quản lý tài khoản <?= $title ?> 🔥</h2>
            <a href="../index.php" class="btn btn-primary">
                <i class="bi bi-house-door-fill"></i> Trang chủ
            </a>
        </div>

        <!-- Thanh chọn game -->
        <div class="game-selector text-center">
            <a href="?game=genshin" class="btn btn-primary <?= $game_type == 'genshin' ? 'active' : '' ?>">
                Genshin Impact
            </a>
            <a href="?game=honkai" class="btn btn-primary <?= $game_type == 'honkai' ? 'active' : '' ?>">
                Honkai: Star Rail
            </a>
            <a href="?game=zzz" class="btn btn-primary <?= $game_type == 'zzz' ? 'active' : '' ?>">
                Zenless Zone Zero
            </a>
            <a href="?game=www" class="btn btn-primary <?= $game_type == 'www' ? 'active' : '' ?>">
                Wuthering Waves
            </a>
        </div>

        <!-- Form Thêm Mới -->
        <form action="add_account.php" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="price" class="form-control" placeholder="Giá tiền" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="image" class="form-control" placeholder="Ảnh (tên file)">
                </div>
                <div class="col-md-2 d-flex">
                    <select name="type" class="form-control me-2" required>
                        <option value="VIP">VIP</option>
                        <option value="STARTER">STARTER</option>
                        <option value="REROLL">REROLL</option>
                        <option value="RANDOM">RANDOM</option>
                    </select>
                    <input type="hidden" name="game_type" value="<?= $game_type ?>">
                    <button type="submit" class="btn btn-success">Thêm</button>
                </div>
                <div class="col-md-12 mt-2">
                    <textarea name="description" class="form-control" placeholder="Mô tả tài khoản" rows="2"></textarea>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách tài khoản -->
        <table class="table table-dark table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Giá</th>
                    <th>Loại</th>
                    <th>Ảnh</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['password']) ?></td>
                    <td><?= number_format($row['price']) ?> VNĐ</td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="../images/<?= htmlspecialchars($row['image']) ?>" class="account-image" alt="Ảnh">
                        <?php else: ?>
                            <span class="text-muted">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="description"><?= nl2br(htmlspecialchars($row['description'])) ?></span></td>
                    <td>
                        <a href="edit_account.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="manage_accounts.php?delete=<?= $row['id'] ?>&game=<?= $game_type ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
