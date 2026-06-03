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
                    <div></div>
                    <p class="first_child_p">289</p>
                    <p class="second_child_p">850</p>
                    <ul class = "third_child_ul">
                        <li><a href="./index.php?action=create_calorie_goal">Create Calorie Goal</a></li>
                    </ul>
                </div>
                
                <p class="food_history">Food History</p>
                <p>Food Name</p>
                <p>Quantity</p>
                <p>Calories</p><br>
                <p class="food">Egg</p>
                <p class="quantity">2</p>
                <p class="calories">148</p><br>

                <p class="food">Bacon</p>
                <p class="quantity">5</p>
                <p class="calories">135</p><br>
                
                <p class="food">Strawberry</p>
                <p class="quantity">1</p>
                <p class="calories">6</p><br>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>