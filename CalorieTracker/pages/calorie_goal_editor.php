<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="./styles/main.css"/>
        <link rel="stylesheet" href="./styles/calorie_goal_editor.css"/>
    </head>

    <body>
        <?php include('./view/header.php');?>
        <?php include('./view/horizontal_nav_bar.php');?>
        <main>
            <section>
                <form action="./index.php?action=update_calorie_goal" method='POST' name='edit_calorie_goal'>
                    <label for='calorie_goal'>Calorie Goal (kcal):</label>
                    <input
                        class="inputBox"
                        type='number'
                        id='calorie_goal'
                        name='calorie_goal'
                        min=1
                        max=50000
                        value="<?php echo $calorie_goal > 0 ? $calorie_goal : ''; ?>"
                        required
                        autofocus
                    >
                    <input class="submitButton" type='submit' id='new_calorie_goal' value='submit'>
                </form>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>
