<?php

session_start();

require_once __DIR__ . '/model/database.php';
require_once __DIR__ . '/model/food_entry_db.php';
require_once __DIR__ . '/model/food_item_db.php';
require_once __DIR__ . '/model/user_db.php';
require_once __DIR__ . '/model/auth.php';
require_once __DIR__ . '/model/request_helpers.php';
require_once __DIR__ . '/model/user_recent_food_db.php';
require_once __DIR__ . '/model/food_helpers.php';

$action = filter_input(INPUT_POST, 'action');
if ($action == NULL) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == NULL) {
        $action = 'home';
    }
}

$public_actions = ['login', 'register', 'login_user', 'register_user'];
if (!in_array($action, $public_actions, true) && !is_logged_in()) {
    header('Location: ./index.php?action=login');
    exit;
}

function load_home_vars() {
    global $calorie_goal, $button_text, $foods, $total_calories, $flash_error;
    $foods = [];
    $total_calories = 0;
    $calorie_goal = 0;
    $button_text = 'Create Calorie Goal';

    if (!is_logged_in()) {
        return;
    }

    $db = get_db_connection();
    if ($db === false) {
        $calorie_goal = (int) ($_SESSION['calorie_goal'] ?? 0);
        $button_text = ($calorie_goal == 0) ? 'Create Calorie Goal' : 'Edit Calorie Goal';
        $flash_error = 'Could not connect to the database.';
        return;
    }

    $user = get_user_by_id($db, get_current_user_id());
    if ($user) {
        $calorie_goal = (int) $user['calorie_goal'];
        $_SESSION['calorie_goal'] = $calorie_goal;
    } else {
        $calorie_goal = (int) ($_SESSION['calorie_goal'] ?? 0);
    }

    $button_text = ($calorie_goal == 0) ? 'Create Calorie Goal' : 'Edit Calorie Goal';
    $foods = get_todays_food_entries($db, get_current_user_id());
    $total_calories = get_todays_total_calories($db, get_current_user_id());
}

if ($action == 'login') {
    if (is_logged_in()) {
        header('Location: ./index.php?action=home');
        exit;
    }
    include('./pages/login.php');
}

else if ($action == 'register') {
    if (is_logged_in()) {
        header('Location: ./index.php?action=home');
        exit;
    }
    include('./pages/register.php');
}

else if ($action == 'login_user') {
    $db = require_db_connection();
    $email = post_string('email');
    $password = post_string('password');

    if ($email !== null && filter_var($email, FILTER_VALIDATE_EMAIL) && $password !== null) {
        $user = verify_user_login($db, $email, $password);
        if ($user) {
            login_user_session($user);
            header('Location: ./index.php?action=home');
            exit;
        }
    }

    $auth_error = 'Invalid email or password.';
    include('./pages/login.php');
}

else if ($action == 'register_user') {
    $db = require_db_connection();
    $email = post_string('email');
    $password = post_string('password');
    $confirm_password = post_string('confirm_password');

    if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $auth_error = 'Please enter a valid email address.';
        include('./pages/register.php');
        exit;
    }

    if ($password === null || strlen($password) < 6) {
        $auth_error = 'Password must be at least 6 characters.';
        include('./pages/register.php');
        exit;
    }

    if ($password !== $confirm_password) {
        $auth_error = 'Passwords do not match.';
        include('./pages/register.php');
        exit;
    }

    if (get_user_by_email($db, $email)) {
        $auth_error = 'An account with that email already exists.';
        include('./pages/register.php');
        exit;
    }

    $user_id = create_user($db, $email, $password);
    $user = get_user_by_id($db, $user_id);
    login_user_session($user);

    header('Location: ./index.php?action=home');
    exit;
}

else if ($action == 'logout') {
    logout_user_session();
    header('Location: ./index.php?action=login');
    exit;
}

else if ($action == 'home') {
    $flash_error = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_error']);
    load_home_vars();
    include('./home.php');
}

else if ($action == 'edit_calorie_goal') {
    $db = get_db_connection();
    if ($db !== false && is_logged_in()) {
        $user = get_user_by_id($db, get_current_user_id());
        $calorie_goal = $user ? (int) $user['calorie_goal'] : 0;
    } else {
        $calorie_goal = (int) ($_SESSION['calorie_goal'] ?? 0);
    }
    include('./pages/calorie_goal_editor.php');
}

else if ($action == 'update_calorie_goal') {
    $new_goal = post_int('calorie_goal');
    if ($new_goal !== null && $new_goal > 0) {
        $_SESSION['calorie_goal'] = $new_goal;
        $db = get_db_connection();
        if ($db !== false) {
            update_user_calorie_goal($db, get_current_user_id(), $new_goal);
        }
    }
    header('Location: ./index.php?action=home');
    exit;
}

else if ($action == 'search') {
    $db = require_db_connection();
    $recently_eaten = get_user_recent_foods($db, get_current_user_id());
    include('./pages/search.php');
}

else if ($action == 'save_food_items') {
    header('Content-Type: application/json');

    $db = get_db_connection();
    if ($db === false) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $foods = isset($payload['foods']) && is_array($payload['foods']) ? $payload['foods'] : [];

    if (empty($foods)) {
        echo json_encode(['error' => 'No foods provided']);
        exit;
    }

    upsert_food_items_from_search($db, $foods);
    echo json_encode(['success' => true]);
    exit;
}

else if ($action == 'add_to_goal') {
    $db = require_db_connection();
    $user_id = get_current_user_id();

    $food_name = post_string('food_name');
    if ($food_name === null) {
        $food_name = get_string('food_name');
    }

    $calories_per_serving = post_float('calories_per_serving');
    if ($calories_per_serving === null) {
        $calories_per_serving = post_float('calories');
    }

    $servings = post_float('servings');
    if ($servings === null || $servings <= 0) {
        $servings = 1;
    }

    $serving_size = post_string('serving_size');
    if ($serving_size === null) {
        $serving_size = get_string('serving_size');
    }

    $food_item_id = post_string('food_item_id');
    if ($food_item_id === null) {
        $food_item_id = get_string('food_item_id');
    }

    $image_url = post_string('image_url');
    if ($image_url === null) {
        $image_url = get_string('image_url');
    }

    if ($food_name !== null && $calories_per_serving !== null) {
        $entry_id = add_food_entry(
            $db,
            $user_id,
            $food_name,
            $calories_per_serving,
            $servings,
            $serving_size,
            $food_item_id,
            $image_url
        );

        if ($entry_id === false) {
            $_SESSION['flash_error'] = 'Could not add food. Please try again.';
        } else {
            save_user_recent_food(
                $db,
                $user_id,
                $food_name,
                $calories_per_serving,
                $serving_size,
                $food_item_id,
                $image_url
            );
        }
    } else {
        $_SESSION['flash_error'] = 'Missing food information. Please search again and click Add.';
    }

    header('Location: ./index.php?action=home');
    exit;
}

else if ($action == 'delete_entry') {
    header('Content-Type: application/json');

    $entry_id = post_int('entry_id');
    $db = get_db_connection();

    if ($db === false || $entry_id === null) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    if (!delete_food_entry($db, $entry_id, get_current_user_id())) {
        echo json_encode(['error' => 'Could not delete food entry']);
        exit;
    }

    $total_calories = get_todays_total_calories($db, get_current_user_id());
    $goal = (int) ($_SESSION['calorie_goal'] ?? 0);
    $percent = ($goal > 0) ? min(100, ($total_calories / $goal) * 100) : 0;

    echo json_encode([
        'total_calories' => $total_calories,
        'percent' => $percent
    ]);
    exit;
}

else if ($action == 'update_servings') {
    header('Content-Type: application/json');

    $entry_id = post_int('entry_id');
    $servings = post_float('servings');

    $db = get_db_connection();
    if ($db === false || $entry_id === null || $servings === null || $servings <= 0) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $calories = update_food_entry_servings($db, $entry_id, get_current_user_id(), $servings);
    if ($calories === false) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $total_calories = get_todays_total_calories($db, get_current_user_id());
    $goal = (int) ($_SESSION['calorie_goal'] ?? 0);
    $percent = ($goal > 0) ? min(100, ($total_calories / $goal) * 100) : 0;

    echo json_encode([
        'calories' => $calories,
        'total_calories' => $total_calories,
        'percent' => $percent
    ]);
    exit;
}

else {
    load_home_vars();
    include('./home.php');
}
