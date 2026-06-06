<?php

function food_thumb_html($food_name, $image_url = null, $category = '') {
    $name = htmlspecialchars($food_name);
    if ($image_url) {
        return '<img src="' . htmlspecialchars($image_url) . '" alt="" class="food-thumb" loading="lazy">';
    }

    $initial = strtoupper(substr($food_name, 0, 1));
    $class = 'food-thumb food-thumb-placeholder';
    if ($category !== '') {
        $class .= ' food-thumb-cat-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($category));
    }

    return '<span class="' . $class . '" aria-hidden="true">' . htmlspecialchars($initial) . '</span>';
}
