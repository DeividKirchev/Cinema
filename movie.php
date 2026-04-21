<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детайли за филма - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 80px 0;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 60px;">
            <div style="height: 500px; background: var(--card-bg); border-radius: 12px;"></div>
            <div>
                <h1 style="font-size: 48px; margin-bottom: 10px;">The Batman</h1>
                <div style="display: flex; gap: 20px; color: var(--text-secondary); margin-bottom: 30px;">
                    <span>2022</span>
                    <span>Action / Crime</span>
                    <span>2h 56m</span>
                </div>
                <p style="font-size: 18px; margin-bottom: 40px;">
                    Втората година от борбата на Брус Уейн срещу престъпността го отвежда дълбоко в корупцията на Готъм Сити, където той се изправя срещу сериен убиец, известен като Гатанката.
                </p>
                <div style="margin-bottom: 40px;">
                    <h3 style="margin-bottom: 15px;">Актьорски състав</h3>
                    <p style="color: var(--text-secondary);">Robert Pattinson, Zoë Kravitz, Paul Dano, Jeffrey Wright</p>
                </div>
                <a href="select-tickets.php" class="btn btn-primary" style="font-size: 20px; padding: 15px 40px;">Резервирай Билет</a>
            </div>
        </div>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
