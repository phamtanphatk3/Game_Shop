<?php
include '../config.php';

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM game_accounts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$game = $result->fetch_assoc();

if (!$game) {
    die("Tài khoản game không tồn tại.");
}

echo "<h2>Chi Tiết Tài Khoản Game</h2>";
echo "<p>Tên game: {$game['game_name']}</p>";
echo "<p>Level: {$game['level']}</p>";
echo "<p>Rank: {$game['rank']}</p>";
echo "<p>Giá: {$game['price']} VND</p>";
echo "<p>Đánh giá trung bình: ⭐ {$game['average_rating']}/5</p>";

echo "<h3>Đánh Giá Của Người Dùng</h3>";

$review_stmt = $conn->prepare("SELECT users.username, reviews.rating, reviews.comment FROM reviews JOIN users ON reviews.user_id = users.id WHERE reviews.game_account_id = ?");
$review_stmt->bind_param("i", $id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();

while ($review = $reviews->fetch_assoc()) {
    echo "<p><strong>{$review['username']}</strong> - ⭐ {$review['rating']}/5</p>";
    echo "<p>{$review['comment']}</p>";
}

echo "<h3>Viết Đánh Giá</h3>";
echo "<form action='submit_review.php' method='POST'>
    <input type='hidden' name='user_id' value='1'> 
    <input type='hidden' name='game_account_id' value='$id'> 
    <label>Đánh giá (1-5):</label>
    <input type='number' name='rating' min='1' max='5' required>
    <label>Bình luận:</label>
    <textarea name='comment'></textarea>
    <button type='submit'>Gửi</button>
</form>";
?>
