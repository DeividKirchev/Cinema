<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Завършване на поръчка - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 80px 0;">
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px;">
            <div>
                <h2 class="mb-20">Данни за плащане</h2>
                <div style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <form action="order-finished.php" method="POST">
                        <label>Име и Фамилия</label>
                        <input type="text" placeholder="Иван Иванов" required>
                        
                        <label>Имейл</label>
                        <input type="email" placeholder="ivan@example.com" required>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div>
                                <label>Карта №</label>
                                <input type="text" placeholder="0000 0000 0000 0000" required>
                            </div>
                            <div>
                                <label>CVC</label>
                                <input type="text" placeholder="000" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; font-size: 18px;">Плати 24.00 лв.</button>
                    </form>
                </div>
            </div>
            
            <div>
                <h2 class="mb-20">Резервация</h2>
                <div style="background: var(--card-bg); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 20px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                        <div style="width: 80px; height: 120px; background: #333; border-radius: 4px;"></div>
                        <div>
                            <h4 style="margin-bottom: 5px;">The Batman</h4>
                            <p style="font-size: 13px; color: var(--text-secondary);">18:30 | Зала 1</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Билети (2x)</span>
                        <span>24.00 лв.</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Такса обслужване</span>
                        <span>0.00 лв.</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color); font-weight: bold; font-size: 20px;">
                        <span>Общо</span>
                        <span style="color: var(--accent-color);">24.00 лв.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
