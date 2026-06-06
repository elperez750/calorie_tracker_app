<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="./styles/main.css"/>
    </head>

    <body>
        <?php include('./view/header.php');?>
        <?php include('./view/horizontal_nav_bar.php');?>
        <main>
            <section>
                <div class="calorie-goal-block" data-calorie-goal="<?php echo $calorie_goal; ?>">
                    <p class="calorie-goal-label">Calorie Goal:</p>
                    <?php
                        if ($calorie_goal > 0) {
                            $caloriePercent = min(100, ($total_calories / $calorie_goal) * 100);
                        } else {
                            $caloriePercent = 0;
                        }
                    ?>
                    <div id="progress-bar" class="progress-bar" style="background-image: linear-gradient(90deg, #81c979 <?php echo $caloriePercent; ?>%, #413d47 <?php echo $caloriePercent; ?>%);"></div>
                    <div class="calorie-goal-meta">
                        <p id="total-calories" class="calorie-current"><?php echo $total_calories; ?></p>
                        <a class="calorie-goal-button button" href="./index.php?action=edit_calorie_goal"><?php echo $button_text; ?></a>
                        <p class="calorie-target"><?php echo $calorie_goal; ?></p>
                    </div>
                </div>

                <?php if (!empty($flash_error)): ?>
                    <p class="home-error"><?php echo htmlspecialchars($flash_error); ?></p>
                <?php endif; ?>

                <div class="food-history-block">
                    <p class="food_history">Food History</p>
                    <?php if (empty($foods)): ?>
                        <p class="food-empty">No foods added yet. Search for food and click Add.</p>
                    <?php else: ?>
                        <div class="food-list" id="food-list">
                            <div class="food-row food-row-header">
                                <span class="food-col-img" aria-hidden="true"></span>
                                <span class="food-col-name">Food Name</span>
                                <span class="food-col-qty">Servings</span>
                                <span class="food-col-cal">Calories</span>
                                <span class="food-col-delete" aria-hidden="true"></span>
                            </div>
                            <?php foreach ($foods as $food):
                                $qty_display = ($food->servings == (int) $food->servings)
                                    ? (int) $food->servings
                                    : $food->servings;
                            ?>
                                <div class="food-row" data-food-entry-id="<?php echo $food->id; ?>">
                                    <span class="food-col-img">
                                        <?php echo food_thumb_html($food->foodName, $food->imageUrl, $food->category); ?>
                                    </span>
                                    <span class="food-col-name" title="<?php echo htmlspecialchars($food->foodName); ?>">
                                        <span class="food-name-text"><?php echo htmlspecialchars($food->foodName); ?></span>
                                        <?php if ($food->servingSize !== ''): ?>
                                            <span class="food-serving-size-text">Serving: <?php echo htmlspecialchars($food->servingSize); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="food-col-qty">
                                        <input
                                            type="number"
                                            class="servings-edit"
                                            data-entry-id="<?php echo $food->id; ?>"
                                            data-calories-per-serving="<?php echo $food->caloriesPerServing; ?>"
                                            value="<?php echo $qty_display; ?>"
                                            min="0.25"
                                            step="0.25"
                                            aria-label="Servings for <?php echo htmlspecialchars($food->foodName); ?>"
                                        >
                                    </span>
                                    <span class="food-col-cal food-calories-value"><?php echo $food->calories; ?></span>
                                    <span class="food-col-delete">
                                        <button type="button" class="delete-entry-btn" data-entry-id="<?php echo $food->id; ?>" aria-label="Remove <?php echo htmlspecialchars($food->foodName); ?>">×</button>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        <?php if (!empty($foods)): ?>
            <script src="./scripts/homeFood.js"></script>
        <?php endif; ?>
        <?php include('./view/footer.php');?>
    </body>
</html>
