<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Успешна резервация - Cinema Noir</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://cdnjs.cloudflare.com/script/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <div class="container" style="padding: 100px 0; text-align: center;">
        <div style="background: var(--card-bg); padding: 60px; border-radius: 12px; max-width: 600px; margin: 0 auto;">
            <div style="color: #4BB543; font-size: 64px; margin-bottom: 20px;">✓</div>
            <h1 class="mb-20">Успешна резервация!</h1>
            <p class="mb-20" style="color: var(--text-secondary);">Благодарим ви, че избрахте Cinema Noir. Вашият билет е готов.</p>
            
            <div id="qrcode" style="display: inline-block; padding: 20px; background: white; border-radius: 8px; margin: 30px 0;"></div>
            
            <p style="margin-top: 10px; font-size: 14px; color: var(--text-secondary);">Покажете този QR код на входа на залата.</p>
            
            <div class="mt-40">
                <a href="index.php" class="btn btn-outline">Към началната страница</a>
            </div>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "TICKET-NOIR-" + Math.random().toString(36).substr(2, 9).toUpperCase(),
            width: 200,
            height: 200,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
