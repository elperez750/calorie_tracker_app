<?php
require_once __DIR__ . '/../model/food_helpers.php';

$calories_per_serving = isset($calories_per_serving) ? (float) $calories_per_serving : 0;
$serving_size = isset($serving_size) ? $serving_size : '';
$food_item_id = isset($food_item_id) ? $food_item_id : '';
$image_url = isset($image_url) ? $image_url : null;
$category = isset($category) ? $category : '';
$display_calories = (floor($calories_per_serving) == $calories_per_serving)
    ? (int) $calories_per_serving
    : round($calories_per_serving, 1);
$serving_size_display = $serving_size !== '' ? $serving_size : 'N/A';
?>
<div class="food-result">
    <span class="food-col-img"><?php echo food_thumb_html($food_name, $image_url, $category); ?></span>
    <span class="food-name" title="<?php echo htmlspecialchars($food_name); ?>"><?php echo htmlspecialchars($food_name); ?></span>
    <span class="food-serving-size"><?php echo htmlspecialchars($serving_size_display); ?></span>
    <span class="food-calories"><?php echo $display_calories; ?> cal/serving</span>
    <form class="add-food-form" action="./index.php?action=add_to_goal" method="POST">
        <input type="hidden" name="food_name" value="<?php echo htmlspecialchars($food_name); ?>">
        <input type="hidden" name="calories_per_serving" value="<?php echo $calories_per_serving; ?>">
        <input type="hidden" name="serving_size" value="<?php echo htmlspecialchars($serving_size); ?>">
        <input type="hidden" name="food_item_id" value="<?php echo htmlspecialchars($food_item_id); ?>">
        <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($image_url ?? ''); ?>">
        <label class="servings-label">
            <span class="servings-text">Servings</span>
            <input class="servings-input" type="number" name="servings" value="1" min="0.25" step="0.25" required>
        </label>
        <button type="submit" class="button addTo">Add</button>
    </form>
</div>
