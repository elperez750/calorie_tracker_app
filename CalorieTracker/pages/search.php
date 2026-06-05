<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker</title>
        <meta charset="utf-8"/>
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
            <div id="search-results"></div>
        </main>
        <script src="./scripts/APIcall.js"></script>
        <?php include('./view/footer.php');?>
    </body>
</html>

