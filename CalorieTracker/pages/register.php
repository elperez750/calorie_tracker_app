<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Calorie Tracker - Register</title>
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
                <h1 class="auth-title">Register</h1>
                <?php if (!empty($auth_error)): ?>
                    <p class="auth-error"><?php echo htmlspecialchars($auth_error); ?></p>
                <?php endif; ?>
                <form action="./index.php?action=register_user" method="POST">
                    <label for="email">Email</label>
                    <input class="auth-input" type="email" id="email" name="email" required autofocus>

                    <label for="password">Password</label>
                    <input class="auth-input" type="password" id="password" name="password" minlength="6" required>

                    <label for="confirm_password">Confirm Password</label>
                    <input class="auth-input" type="password" id="confirm_password" name="confirm_password" minlength="6" required>

                    <input class="auth-button" type="submit" value="Create Account">
                </form>
                <p class="auth-switch">
                    Already have an account?
                    <a href="./index.php?action=login">Login here</a>
                </p>
            </section>
        </main>
        <?php include('./view/footer.php');?>
    </body>
</html>
