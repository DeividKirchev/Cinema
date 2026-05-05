<?php
session_start();

// Hardcoded credentials
$admin_pass = "cinema123";

if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Грешна парола!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ Панел - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 100px 0;">
        <?php if (!isset($_SESSION['admin'])): ?>
            <div style="max-width: 400px; margin: 0 auto; background: var(--card-bg); padding: 40px; border-radius: 12px; border: 1px solid var(--border-color);">
                <h1 class="mb-20 text-center">Админ Вход</h1>
                <?php if (isset($error)) echo "<p style='color: var(--accent-color); margin-bottom: 15px;'>$error</p>"; ?>
                <form method="POST">
                    <label style="display: block; margin-bottom: 8px; font-size: 14px; color: var(--text-secondary);">Парола за достъп</label>
                    <input type="password" name="password" required placeholder="Въведете парола...">
                    <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Влез в системата</button>
                </form>
            </div>
        <?php else: ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <h1>Добре дошли, Администратор</h1>
                <a href="?logout=1" class="btn btn-outline">Изход от системата</a>
            </div>

            <div class="grid-responsive">
                <div style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h3 class="mb-20">Продажби</h3>
                    <p style="font-size: 32px; font-weight: bold; color: var(--accent-color);">504.00 лв.</p>
                    <p style="color: var(--text-secondary);">Общо за днес</p>
                </div>
                <div style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h3 class="mb-20">Билети</h3>
                    <p style="font-size: 32px; font-weight: bold;">42</p>
                    <p style="color: var(--text-secondary);">Резервирани места</p>
                </div>
                <div style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h3 class="mb-20">Филми</h3>
                    <p style="font-size: 32px; font-weight: bold;">8</p>
                    <p style="color: var(--text-secondary);">Активни прожекции</p>
                </div>
            </div>

            <div class="mt-40" style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                <h3 class="mb-20">Управление на прожекции</h3>
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-secondary);">
                            <th style="padding: 15px 0;">Филм</th>
                            <th>Зала</th>
                            <th>Час</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 15px 0;">The Batman</td>
                            <td>Зала 1</td>
                            <td>18:30</td>
                            <td><a href="#" style="color: var(--accent-color);">Редактирай</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
