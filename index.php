<?php
// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/config/database.php';

use App\Models\Movie;
use App\Models\Showtime;

$movieModel = new Movie();
$showtimeModel = new Showtime();
$db = Database::getInstance();

$newsletterMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_email'])) {
    $email = filter_var($_POST['newsletter_email'], FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = $db->prepare("INSERT IGNORE INTO newsletters (email) VALUES (:email)");
            $stmt->execute(['email' => $email]);
            $newsletterMsg = 'Успешно се абонирахте за нашия бюлетин!';
        } catch (Exception $e) {
            $newsletterMsg = 'Възникна грешка при абонамента.';
        }
    } else {
        $newsletterMsg = 'Моля, въведете валиден имейл адрес.';
    }
}

// Fetch featured movie
$featuredMovie = $movieModel->getFeatured();

// Fallback if no featured movie
if (!$featuredMovie) {
    $allMovies = $movieModel->getAll(['status' => 'now playing']);
    if (!empty($allMovies)) {
        $featuredMovie = $allMovies[0];
    } else {
        $featuredMovie = [
            'id' => 0,
            'title' => 'Няма активни филми',
            'description' => 'В момента няма филми в програмата.',
            'poster_path' => 'public/assets/images/default_poster.jpg',
            'trailer_url' => ''
        ];
    }
}

// Fetch today's movies and their showtimes
$dateParam = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$todayShowtimes = $showtimeModel->getByDate($dateParam);

// Group showtimes by movie
$moviesToday = [];
foreach ($todayShowtimes as $st) {
    $mid = $st['movie_id'];
    if (!isset($moviesToday[$mid])) {
        $moviesToday[$mid] = [
            'id' => $mid,
            'title' => $st['title'],
            'poster_path' => $st['poster_path'],
            'showtimes' => []
        ];
    }
    $moviesToday[$mid]['showtimes'][] = [
        'id' => $st['id'],
        'time' => date('H:i', strtotime($st['start_time']))
    ];
}

// Fetch trending movies
$trendingMovies = $movieModel->getTrending(6);

include 'src/templates/header.php'; ?>


<main>
    <section class="hero">
        <div class="hero-bg">
            <img class="hero-img" src="<?php echo $featuredMovie['poster_path']; ?>" alt="<?php echo $featuredMovie['title']; ?>">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <span class="tagline">В КИНАТА ОТ ТАЗИ СЕДМИЦА</span>
                <h1 class="hero-title"><?php echo mb_strtoupper($featuredMovie['title']); ?></h1>
                <p class="hero-desc">
                    <?php echo $featuredMovie['description']; ?>
                </p>
                <div class="hero-btns">
                    <a href="program.php?movie_id=<?php echo $featuredMovie['id']; ?>" class="btn btn-primary">
                        <span class="material-symbols-outlined">confirmation_number</span>
                        КУПИ БИЛЕТ
                    </a>
                    <?php if (!empty($featuredMovie['trailer_url'])): ?>
                        <a href="<?php echo $featuredMovie['trailer_url']; ?>" target="_blank" class="btn btn-outline">ТРЕЙЛЪР</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding overflow-visible">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">ПРОГРАМА</h2>
                    <div class="section-line"></div>
                </div>
                <div class="tab-group">
                    <a href="index.php?date=<?php echo date('Y-m-d'); ?>" class="btn tab-item <?php echo $dateParam === date('Y-m-d') ? 'active' : 'btn-outline border-none opacity-50'; ?>">ДНЕС</a>
                    <a href="index.php?date=<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="btn tab-item <?php echo $dateParam === date('Y-m-d', strtotime('+1 day')) ? 'active' : 'btn-outline border-none opacity-50'; ?>">УТРЕ</a>
                    <a href="index.php?date=<?php echo date('Y-m-d', strtotime('+2 days')); ?>" class="btn tab-item <?php echo $dateParam === date('Y-m-d', strtotime('+2 days')) ? 'active' : 'btn-outline border-none opacity-50'; ?>">ВДРУГИДЕН</a>
                </div>
            </div>
        </div>

        <div class="carousel-container">
            <div class="carousel-track no-scrollbar">
                <?php foreach ($moviesToday as $m): ?>
                <div class="movie-card text-on-surface">
                    <a href="movie.php?id=<?php echo $m['id']; ?>" class="movie-card-img-container">
                        <img class="movie-card-img" src="<?php echo $m['poster_path']; ?>" alt="<?php echo $m['title']; ?>">
                    </a>
                    <div class="movie-card-info">
                        <a href="movie.php?id=<?php echo $m['id']; ?>">
                            <h3 class="movie-card-title"><?php echo mb_strtoupper($m['title']); ?></h3>
                        </a>
                        <div class="movie-card-times">
                            <?php foreach ($m['showtimes'] as $st): ?>
                                <a href="select-tickets.php?showtime_id=<?php echo $st['id']; ?>" class="time-pill"><?php echo $st['time']; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($moviesToday)): ?>
                    <p class="text-center w-full py-10 opacity-50">Няма прожекции за днес.</p>
                <?php endif; ?>
            </div>
            
            <div class="carousel-nav-overlay container">
                <div class="carousel-nav prev">
                    <span class="material-symbols-outlined">chevron_left</span>
                </div>
                <div class="carousel-nav next">
                    <span class="material-symbols-outlined">chevron_right</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding-bottom">
        <div class="section-header container">
            <div>
                <h2 class="section-title">НАЙ-ГЛЕДАНИ ФИЛМИ</h2>
                <div class="section-line"></div>
            </div>
            <a href="archive.php" class="text-secondary font-bold flex items-center gap-2">
                ВИЖ ВСИЧКИ <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>

        <div class="carousel-container">
            <div class="carousel-track no-scrollbar">
                <?php foreach ($trendingMovies as $tm): ?>
                <div class="movie-card text-on-surface">
                    <a href="movie.php?id=<?php echo $tm['id']; ?>" class="movie-card-img-container">
                        <img class="movie-card-img" src="<?php echo $tm['poster_path']; ?>" alt="<?php echo $tm['title']; ?>">
                    </a>
                    <div class="movie-card-info">
                        <a href="movie.php?id=<?php echo $tm['id']; ?>">
                            <h3 class="movie-card-title text-lg"><?php echo mb_strtoupper($tm['title']); ?></h3>
                        </a>
                        <p class="text-muted text-xs uppercase tracking-widest"><?php echo $tm['genre']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="carousel-nav-overlay container">
                <div class="carousel-nav prev">
                    <span class="material-symbols-outlined">chevron_left</span>
                </div>
                <div class="carousel-nav next">
                    <span class="material-symbols-outlined">chevron_right</span>
                </div>
            </div>
        </div>
    </section>

    <section class="experience-section">
        <div class="container">
            <div class="experience-card">
                <div>
                    <div class="flex items-center gap-3 text-white mb-6">
                        <span class="material-symbols-outlined text-4xl text-primary-light">theater_comedy</span>
                        <h2>ПРЕМИУМ КИНО ИЗЖИВЯВАНЕ</h2>
                    </div>
                    <p>
                        Открийте магията на голямото кино с нашите LUXE и VIP зали. Звук от последно поколение и комфорт без компромиси.
                    </p>
                </div>
                <div class="experience-grid">
                    <div class="exp-item">
                        <span class="exp-name">4DX</span>
                        <span class="exp-label">Изживяване</span>
                    </div>
                    <div class="exp-item">
                        <span class="exp-name">IMAX</span>
                        <span class="exp-label">Визия</span>
                    </div>
                    <div class="exp-item">
                        <span class="exp-name">VIP</span>
                        <span class="exp-label">Комфорт</span>
                    </div>
                    <div class="exp-item">
                        <span class="exp-name">ATMOS</span>
                        <span class="exp-label">Звук</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-banner">
                <img src="public/assets/images/newsletter_bg.png" alt="Cinema" class="newsletter-bg">
                <div class="newsletter-overlay"></div>
                <div class="newsletter-content">
                    <h2>НЕ ПРОПУСКАЙТЕ НИТО ЕДНА ПРЕМИЕРА</h2>
                    <p>
                        Абонирайте се за нашия бюлетин и получавайте първи новини за най-новите филми и ексклузивни оферти.
                    </p>
                    <?php if ($newsletterMsg): ?>
                        <p style="color: #4ade80; font-weight: bold; margin-bottom: 10px;"><?php echo $newsletterMsg; ?></p>
                    <?php endif; ?>
                    <form class="newsletter-form" method="POST" action="index.php">
                        <input type="email" name="newsletter_email" placeholder="Вашият имейл..." class="newsletter-input" required>
                        <button type="submit" class="btn btn-primary px-12 rounded-xl font-black tracking-widest">АБОНИРАЙ СЕ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="public/js/carousel.js"></script>

<?php include 'src/templates/footer.php'; ?>
