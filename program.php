<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Програма - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 60px 0;">
        <h1>Програма за седмицата</h1>
        <p style="color: var(--text-secondary); margin-bottom: 40px;">Изберете удобен за вас час.</p>

        <?php foreach($movies as $movie): ?>
        <div class="program-item" style="background: var(--card-bg); padding: 30px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border-color); flex-wrap: wrap; gap: 20px;">
            <div>
                <h2 style="color: var(--accent-color); margin-bottom: 8px;"><?php echo $movie['title']; ?></h2>
                <p style="font-size: 14px; color: var(--text-secondary);"><?php echo $movie['genre']; ?> | 124 мин.</p>
            </div>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="select-tickets.php" class="btn btn-outline">14:30</a>
                <a href="select-tickets.php" class="btn btn-outline">17:45</a>
                <a href="select-tickets.php" class="btn btn-primary">21:00</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
