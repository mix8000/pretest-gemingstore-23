<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-container">
        <h1>Player Login</h1>
        <?php if (isset($_GET['registered'])): ?>
            <p class="success-message">Registration successful! Please login.</p>
        <?php endif; ?>
        <form action="api.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-neon">ENTER WORLD</button>
        </form>
        <div class="login-links">
            <p>New Player? <a href="register.php">Create Character</a></p>
            <p><a href="login_admin.php" class="admin-link">Admin Access</a></p>
        </div>
    </div>
</body>

</html>