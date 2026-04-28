<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избор на места - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .screen {
            width: 100%;
            height: 10px;
            background: #fff;
            margin: 50px 0;
            box-shadow: 0 3px 10px rgba(255,255,255,0.7);
            border-radius: 5px;
        }
        .seats-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 10px;
            justify-content: center;
        }
        .seat {
            width: 35px;
            height: 35px;
            background: #444451;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            cursor: pointer;
        }
        .seat.selected { background: var(--accent-color); }
        .seat.occupied { background: #1c1c27; cursor: not-allowed; opacity: 0.3; }
    </style>
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 60px 0;">
        <h1 style="text-align: center;" class="mb-20">Избор на места</h1>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 50px;">The Batman | Зала 1 | 18:30</p>
        
        <div style="max-width: 600px; margin: 0 auto; background: var(--card-bg); padding: 40px; border-radius: 12px; border: 1px solid var(--border-color);">
            <div style="text-align: center; margin-bottom: 10px; color: var(--text-secondary); font-size: 12px; letter-spacing: 2px;">ЕКРАН</div>
            <div class="screen"></div>
            
            <div class="seats-grid">
                <?php for($i=0; $i<60; $i++): ?>
                    <div class="seat <?php echo ($i % 7 == 0) ? 'occupied' : ''; ?>" title="Място <?php echo $i+1; ?>"></div>
                <?php endfor; ?>
            </div>

            <div style="margin-top: 50px; display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <div style="display: flex; gap: 15px; margin-bottom: 15px; font-size: 13px;">
                        <span style="display: flex; align-items: center; gap: 5px;"><div class="seat" style="width: 15px; height: 15px; cursor: default;"></div> Свободно</span>
                        <span style="display: flex; align-items: center; gap: 5px;"><div class="seat selected" style="width: 15px; height: 15px; cursor: default;"></div> Избрано</span>
                        <span style="display: flex; align-items: center; gap: 5px;"><div class="seat occupied" style="width: 15px; height: 15px; cursor: default;"></div> Заето</span>
                    </div>
                    <p style="font-size: 14px;">Брой места: <strong id="count">0</strong></p>
                    <p style="font-size: 18px; color: var(--accent-color);">Сума: <strong id="total">0.00</strong> лв.</p>
                </div>
                <a href="order.php" class="btn btn-primary">Потвърди резервация</a>
            </div>
        </div>
    </div>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
