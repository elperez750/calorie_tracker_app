<!DOCTYPE html>
<nav>
    <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="./index.php?action=home">Home</a></li>
            <li><a href="./index.php?action=search">Search</a></li>
            <li><a href="./index.php?action=logout">Log Out</a></li>
        <?php else: ?>
            <li><a href="./index.php?action=login">Login</a></li>
            <li><a href="./index.php?action=register">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
