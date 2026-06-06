<?php

function post_string($key) {
    if (!isset($_POST[$key])) {
        return null;
    }
    $value = trim((string) $_POST[$key]);
    return $value === '' ? null : $value;
}

function post_float($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') {
        return null;
    }
    if (!is_numeric($_POST[$key])) {
        return null;
    }
    return (float) $_POST[$key];
}

function post_int($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') {
        return null;
    }
    if (!is_numeric($_POST[$key])) {
        return null;
    }
    return (int) $_POST[$key];
}

function get_string($key) {
    if (!isset($_GET[$key])) {
        return null;
    }
    $value = trim((string) $_GET[$key]);
    return $value === '' ? null : $value;
}
