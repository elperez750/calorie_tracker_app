<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker - Login</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="./styles/main.css"/>
        <link rel="stylesheet" href="./styles/auth.css"/>
    </head>

    <body>
        <?php include('./view/header.php');?>
        <?php include('./view/horizontal_nav_bar.php');?>
        <main>
            <section class="auth-section">
                <h1 class="auth-title">Login</h1>
                <?php if (!empty($auth_error)): ?>
                    <p class="auth-error"><?php echo htmlspecialchars($auth_error); ?></p>
                <?php endif; ?>
                <form action="./index.php?action=login_user" method="POST">
                    <label for="email">Email</label>
                    <input class="auth-input" type="email" id="email" name="email" required autofocus>

                    <label for="password">Password</label>
                    <input class="auth-input" type="password" id="password" name="password" required>

                    <input class="auth-button" type="submit" value="Login">
                </form>
                <p class="auth-switch">
                    Don't have an account?
                    <a href="./index.php?action=register">Register here</a>
                </p>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>
