<?php
$game_account_id = $_GET['game_account_id'] ?? NULL;
$service_id = $_GET['service_id'] ?? NULL;

$sql = "SELECT r.rating, r.comment, u.username, r.created_at FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.game_account_id = ? OR r.service_id = ? 
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $game_account_id, $service_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h3>Đánh Giá</h3>
<?php while ($row = $result->fetch_assoc()) { ?>
    <p><strong><?php echo $row['username']; ?></strong> (<?php echo $row['created_at']; ?>)</p>
    <p>⭐ <?php echo $row['rating']; ?>/5</p>
    <p><?php echo $row['comment']; ?></p>
    <hr>
<?php } ?>
