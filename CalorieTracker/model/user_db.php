<?php

function get_user_by_email($db, $email) {
    $stmt = $db->prepare(
        'SELECT user_id, email, password_hash, calorie_goal
         FROM users
         WHERE email = :email'
    );
    $stmt->execute([':email' => $email]);
    return $stmt->fetch();
}

function get_user_by_id($db, $user_id) {
    $stmt = $db->prepare(
        'SELECT user_id, email, calorie_goal
         FROM users
         WHERE user_id = :user_id'
    );
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch();
}

function create_user($db, $email, $password) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare(
        'INSERT INTO users (email, password_hash)
         VALUES (:email, :password_hash)'
    );
    $stmt->execute([
        ':email' => $email,
        ':password_hash' => $password_hash,
    ]);
    return (int) $db->lastInsertId();
}

function verify_user_login($db, $email, $password) {
    $user = get_user_by_email($db, $email);
    if (!$user) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    return $user;
}

function update_user_calorie_goal($db, $user_id, $calorie_goal) {
    $stmt = $db->prepare(
        'UPDATE users
         SET calorie_goal = :calorie_goal, goal_updated_at = NOW()
         WHERE user_id = :user_id'
    );
    $stmt->execute([
        ':calorie_goal' => $calorie_goal,
        ':user_id' => $user_id,
    ]);
}

function get_user_calorie_goal($db, $user_id) {
    $stmt = $db->prepare(
        'SELECT user_id, calorie_goal, goal_updated_at
         FROM users
         WHERE user_id = :user_id'
    );
    $stmt->execute([':user_id' => $user_id]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    require_once __DIR__ . '/CalorieGoal.php';
    return CalorieGoal::fromUserRow($row);
}
