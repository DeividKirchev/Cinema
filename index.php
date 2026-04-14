<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Noir - Начало</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <?php include 'src/templates/header.php'; ?>

    <main>
        <section class="hero" style="min-height: 80vh; background: linear-gradient(0deg, var(--bg-color) 0%, rgba(18, 18, 28, 0.4) 50%, rgba(18, 18, 28, 0.8) 100%), url('public/img/hero.jpg') center/cover no-repeat; display: flex; align-items: center; justify-content: center; text-align: center;">
            <div class="container">
                <h1 style="font-size: 72px; font-weight: 900; margin-bottom: 24px; letter-spacing: -2px; text-transform: uppercase;">Потопи се в мрака</h1>
                <p style="font-size: 22px; margin-bottom: 48px; color: var(--text-secondary); max-width: 600px; margin-left: auto; margin-right: auto;">Елитно кино изживяване с най-новите заглавия и безкомпромисен комфорт.</p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="program.php" class="btn btn-primary">Виж Програмата</a>
                    <a href="archive.php" class="btn btn-outline">Архив на филми</a>
                </div>
            </div>
        </section>

        <section class="movies" style="padding: 80px 0;">
            <div class="container">
                <h2 style="margin-bottom: 40px; font-size: 32px;">Сега Прожектираме</h2>
                <div class="grid-responsive">
                    <?php foreach($movies as $movie): ?>
                    <div class="movie-card" style="background: var(--card-bg); border-radius: 8px; overflow: hidden; transition: 0.3s; border: 1px solid var(--border-color);">
                        <div style="height: 350px; background: #333;"></div> <!-- Placeholder for image -->
                        <div style="padding: 20px;">
                            <h3 style="margin-bottom: 5px;"><?php echo $movie['title']; ?></h3>
                            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 15px;"><?php echo $movie['genre']; ?></p>
                            <a href="movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Купи Билет</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include 'src/templates/footer.php'; ?>
</body>
</html>
