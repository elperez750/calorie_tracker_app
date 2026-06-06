<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="./styles/main.css"/>
        <link rel="stylesheet" href="./styles/search.css"/>
    </head>

    <body>
        <?php include('./view/header.php');?>
        <?php include('./view/horizontal_nav_bar.php');?>
        <main>
            <section>
                <form>
                    <label class="minCalLabel" for='minCal'>From:</label>
                    <input class="minCalInputBox" type='number' id='minCal' name='minCal' min=0 max=50000>
                    <p>to</p>
                    <input class="maxCalInputBox" type='number' id='maxCal' name='maxCal' min=0 max=50000>
                    <label class="maxCalLabel" for='maxCal'>Calories</label>
                </form>
                <form action="./index.php?action=search_food" method='POST' name='search_food'>
                    <label for='search_box'>Search:</label>
                    <input class="searchInputBox" type='text' id='search_box' name='search_box' required autofocus>
                    <input class="submitButton" type='submit' id='submit_search' value='submit'>
                </form>
            </section>

            <div id="recent-foods-section" class="recent-foods-section"<?php echo empty($recently_eaten) ? ' hidden' : ''; ?>>
                <h2 class="recent-foods-title">Recently Eaten</h2>
                <p class="recent-foods-note">Quickly add foods you have logged before.</p>
                <div id="recent-foods-list">
                    <?php foreach ($recently_eaten as $food): ?>
                        <?php
                            $food_name = $food['food_name'];
                            $calories_per_serving = $food['calories_per_serving'];
                            $serving_size = $food['serving_size'] ?? '';
                            $food_item_id = $food['food_item_id'] ?? '';
                            $image_url = $food['image_url'] ?? null;
                            $category = '';
                            include('./view/food_result_row.php');
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="search-results"></div>
        </main>
        <script src="./scripts/APIcall.js"></script>
        <?php include('./view/footer.php');?>
    </body>
</html>
