<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Архив - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 60px 0;">
        <h1>Минали Прожекции</h1>
        <p style="color: var(--text-secondary); margin-bottom: 40px;">Вижте филмите, които вече преминаха.</p>

        <div class="grid-responsive">
            <?php for($i=1; $i<=6; $i++): ?>
            <div style="background: var(--card-bg); border-radius: 12px; overflow: hidden; opacity: 0.6; border: 1px solid var(--border-color);">
                <div style="height: 250px; background: #222;"></div>
                <div style="padding: 20px;">
                    <h4 class="mb-20">Минал Филм <?php echo $i; ?></h4>
                    <p style="font-size: 13px; color: var(--text-secondary);">Януари 2026</p>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
