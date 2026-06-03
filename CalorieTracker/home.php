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
                    <p class="first_child_p">34</p>
                    <p class="second_child_p">100</p>
                    <ul class = "third_child_ul">
                        <li><a href="/CalorieTracker/index.php?action=create_calorie_goal">Create Calorie Goal</a></li>
                    </ul>
                </div>
                
                <p class="food_history">Food History</p>
                <p>Food name</p>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>