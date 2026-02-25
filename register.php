<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - ร้านเกม</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-container">
        <h1>สร้างบัญชีใหม่</h1>
        <?php if (isset($_GET['error'])): ?>
            <p class="success-message" style="border-color: red; color: red; background: rgba(255,0,0,0.1);">
                <?= htmlspecialchars($_GET['error']) ?>
            </p>
        <?php endif; ?>
        <form action="api.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>อีเมล</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="tel" name="mobile" required>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-neon">สมัครสมาชิก</button>
        </form>
        <div class="login-links">
            <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a></p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>