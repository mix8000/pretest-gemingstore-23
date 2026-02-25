<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ร้านเกม</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-container">
        <h1>เข้าสู่ระบบผู้เล่น</h1>
        <?php if (isset($_GET['error'])): ?>
            <p class="success-message" style="border-color: red; color: red; background: rgba(255,0,0,0.1);">
                <?= htmlspecialchars($_GET['error']) ?>
            </p>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <p class="success-message">ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ</p>
        <?php endif; ?>
        <form action="api.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-neon">เข้าสู่ระบบ</button>
        </form>
        <div class="login-links">
            <p>ผู้เล่นใหม่? <a href="register.php">สมัครสมาชิก</a></p>

        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>