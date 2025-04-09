<form action="submit_review.php" method="POST">
    <input type="hidden" name="game_account_id" value="<?php echo $game_account_id; ?>">
    <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
    
    <label>Đánh giá (1 - 5 sao):</label>
    <select name="rating" required>
        <option value="1">⭐</option>
        <option value="2">⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="5">⭐⭐⭐⭐⭐</option>
    </select>

    <label>Bình luận:</label>
    <textarea name="comment" required></textarea>

    <button type="submit">Gửi Đánh Giá</button>
</form>
