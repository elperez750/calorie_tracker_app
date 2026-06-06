<?php

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ./index.php?action=login');
        exit;
    }
}

function get_current_user_id() {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function login_user_session($user) {
    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['calorie_goal'] = (int) $user['calorie_goal'];
}

function logout_user_session() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['calorie_goal']);
}
