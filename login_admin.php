<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Gaming Store</title>
    <link rel="stylesheet" href="style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
</head>

<body class="login-page admin-login">
    <div class="login-container">
        <h1 class="admin-title">SYSTEM <span class="accent-red">ACCESS</span></h1>
        <form action="api.php" method="POST">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="role_target" value="admin">
            <div class="form-group">
                <label>Admin ID</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Passcode</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-neon btn-red">AUTHENTICATE</button>
        </form>
        <div class="login-links">
            <p><a href="login.php">Return to Store</a></p>
        </div>
    </div>
</body>

</html>