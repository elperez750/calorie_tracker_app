<?php

function save_user_recent_food($db, $user_id, $food_name, $calories_per_serving, $serving_size = null, $food_item_id = null, $image_url = null) {
    $stmt = $db->prepare(
        'INSERT INTO user_recent_foods
            (user_id, food_item_id, food_name, calories_per_serving, serving_size, image_url, last_used_at)
         VALUES
            (:user_id, :food_item_id, :food_name, :calories_per_serving, :serving_size, :image_url, NOW())
         ON DUPLICATE KEY UPDATE
            food_item_id = VALUES(food_item_id),
            image_url = VALUES(image_url),
            last_used_at = NOW()'
    );
    $stmt->execute([
        ':user_id' => (int) $user_id,
        ':food_item_id' => ($food_item_id !== null && $food_item_id !== '') ? (string) $food_item_id : null,
        ':food_name' => $food_name,
        ':calories_per_serving' => $calories_per_serving,
        ':serving_size' => ($serving_size !== null && $serving_size !== '') ? $serving_size : null,
        ':image_url' => ($image_url !== null && $image_url !== '') ? $image_url : null,
    ]);
}

function get_user_recent_foods($db, $user_id, $limit = 15) {
    $stmt = $db->prepare(
        'SELECT food_item_id, food_name, calories_per_serving, serving_size, image_url, last_used_at
         FROM user_recent_foods
         WHERE user_id = :user_id
         ORDER BY last_used_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':user_id', (int) $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
