<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker</title>
        <meta charset="utf-8"/>
        <link rel="stylesheet" href="./styles/main.css"/>
    </head>

    <body>
        <?php include('./view/header.php');?>
        <?php include('./view/horizontal_nav_bar.php');?>
        <main>
            <section>
                <div class="parent_div">
                    <p>Calorie Goal:</p>
                    <?php
                        if($calorie_goal > 0){
                            $caloriePercent = (289/$calorie_goal) * 100;
                        }else{
                            $caloriePercent = 0;
                        }
                    ?>
                    <div style="background-image: linear-gradient(90deg, #81c979 <?php echo $caloriePercent; ?>%, #413d47 <?php echo $caloriePercent; ?>%);"></div>
                    <p class="first_child_p">289</p>
                    <p class="second_child_p"><?php echo $calorie_goal; ?></p>
                    <a class="third_child_a button" href="./index.php?action=edit_calorie_goal"><?php echo $button_text; ?></a>
                </div>
                <p class="food_history">Food History</p>
                <p>Food Name</p>
                <p>Quantity</p>
                <p>Calories</p><br>
                
                <a class="button addTo" href="./index.php?action=add_to_goal">Add to Goal</a>
                <p class="food">Egg</p>
                <p class="quantity">2</p>
                <p class="calories">148</p><br>

                <a class="button addTo" href="./index.php?action=add_to_goal">Add to Goal</a>
                <p class="food">Bacon</p>
                <p class="quantity">5</p>
                <p class="calories">135</p><br>
                
                <a class="button addTo" href="./index.php?action=add_to_goal">Add to Goal</a>
                <p class="food">Strawberry</p>
                <p class="quantity">1</p>
                <p class="calories">6</p><br>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>