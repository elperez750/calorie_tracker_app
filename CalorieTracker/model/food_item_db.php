<?php

require_once __DIR__ . '/FoodItem.php';

function upsert_food_item($db, $food_item_id, $name, $calories_per_serving, $serving_size = null, $category = null, $image_url = null) {
    if ($food_item_id === null || $food_item_id === '') {
        return;
    }

    $stmt = $db->prepare(
        'INSERT INTO food_items (food_item_id, name, calories_per_serving, serving_size, category, image_url)
         VALUES (:food_item_id, :name, :calories_per_serving, :serving_size, :category, :image_url)
         ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            calories_per_serving = VALUES(calories_per_serving),
            serving_size = VALUES(serving_size),
            category = VALUES(category),
            image_url = COALESCE(VALUES(image_url), image_url)'
    );
    $stmt->execute([
        ':food_item_id' => (string) $food_item_id,
        ':name' => $name,
        ':calories_per_serving' => $calories_per_serving,
        ':serving_size' => $serving_size,
        ':category' => $category,
        ':image_url' => ($image_url !== null && $image_url !== '') ? $image_url : null,
    ]);
}

function upsert_food_items_from_search($db, $foods) {
    foreach ($foods as $food) {
        $food_id = isset($food['food_id']) ? $food['food_id'] : null;
        $name = isset($food['food_name']) ? trim($food['food_name']) : '';
        if ($food_id === null || $name === '') {
            continue;
        }

        $calories = isset($food['calories']) && $food['calories'] !== null
            ? (float) $food['calories']
            : 0;
        $serving_size = isset($food['serving_size']) ? $food['serving_size'] : null;
        $category = isset($food['category']) ? $food['category'] : null;
        $image_url = isset($food['image_url']) ? $food['image_url'] : null;

        upsert_food_item($db, $food_id, $name, $calories, $serving_size, $category, $image_url);
    }
}

function get_food_item_image($db, $food_item_id) {
    if ($food_item_id === null || $food_item_id === '') {
        return null;
    }

    $stmt = $db->prepare(
        'SELECT image_url, category, name
         FROM food_items
         WHERE food_item_id = :food_item_id'
    );
    $stmt->execute([':food_item_id' => (string) $food_item_id]);
    return $stmt->fetch();
}
