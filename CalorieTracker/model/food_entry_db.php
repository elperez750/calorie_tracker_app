<?php

require_once __DIR__ . '/FoodEntry.php';
require_once __DIR__ . '/food_item_db.php';

function get_todays_food_entries($db, $user_id) {
    $stmt = $db->prepare(
        'SELECT fe.food_entry_id, fe.user_id, fe.food_item_id, fe.food_name, fe.calories,
                fe.calories_per_serving, fe.servings, fe.serving_size, fe.date_logged, fe.logged_at,
                fi.image_url, fi.category
         FROM food_entries fe
         LEFT JOIN food_items fi ON fe.food_item_id = fi.food_item_id
         WHERE fe.user_id = :user_id AND fe.date_logged = CURDATE()
         ORDER BY fe.logged_at ASC'
    );
    $stmt->execute([
        ':user_id' => (int) $user_id,
    ]);

    $entries = [];
    foreach ($stmt->fetchAll() as $row) {
        $entries[] = FoodEntry::fromRow($row);
    }
    return $entries;
}

function add_food_entry($db, $user_id, $food_name, $calories_per_serving, $servings, $serving_size = null, $food_item_id = null, $image_url = null) {
    if ($food_item_id !== null && $food_item_id !== '') {
        upsert_food_item($db, $food_item_id, $food_name, $calories_per_serving, $serving_size, null, $image_url);
    }

    $calories = (int) round($calories_per_serving * $servings);
    $item_id = ($food_item_id !== null && $food_item_id !== '') ? (string) $food_item_id : null;
    $size = ($serving_size !== null && $serving_size !== '') ? $serving_size : null;

    try {
        $stmt = $db->prepare(
            'INSERT INTO food_entries
                (user_id, food_item_id, food_name, calories, calories_per_serving, servings, serving_size, date_logged)
             VALUES
                (:user_id, :food_item_id, :food_name, :calories, :calories_per_serving, :servings, :serving_size, CURDATE())'
        );
        $stmt->execute([
            ':user_id' => (int) $user_id,
            ':food_item_id' => $item_id,
            ':food_name' => $food_name,
            ':calories' => $calories,
            ':calories_per_serving' => $calories_per_serving,
            ':servings' => $servings,
            ':serving_size' => $size,
        ]);

        return (int) $db->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

function delete_food_entry($db, $entry_id, $user_id) {
    $stmt = $db->prepare(
        'DELETE FROM food_entries
         WHERE food_entry_id = :entry_id AND user_id = :user_id'
    );
    $stmt->execute([
        ':entry_id' => (int) $entry_id,
        ':user_id' => (int) $user_id,
    ]);
    return $stmt->rowCount() > 0;
}

function update_food_entry_servings($db, $entry_id, $user_id, $servings) {
    $stmt = $db->prepare(
        'SELECT calories_per_serving
         FROM food_entries
         WHERE food_entry_id = :entry_id AND user_id = :user_id'
    );
    $stmt->execute([
        ':entry_id' => (int) $entry_id,
        ':user_id' => (int) $user_id,
    ]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }

    $calories = (int) round($row['calories_per_serving'] * $servings);
    $update = $db->prepare(
        'UPDATE food_entries
         SET servings = :servings, calories = :calories
         WHERE food_entry_id = :entry_id AND user_id = :user_id'
    );
    $update->execute([
        ':servings' => $servings,
        ':calories' => $calories,
        ':entry_id' => (int) $entry_id,
        ':user_id' => (int) $user_id,
    ]);

    return $calories;
}

function get_todays_total_calories($db, $user_id) {
    $stmt = $db->prepare(
        'SELECT COALESCE(SUM(calories), 0) AS total
         FROM food_entries
         WHERE user_id = :user_id AND date_logged = CURDATE()'
    );
    $stmt->execute([':user_id' => (int) $user_id]);
    $row = $stmt->fetch();
    return (int) $row['total'];
}
