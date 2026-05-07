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

use App\Models\Showtime;

$showtimeModel = new Showtime();

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$movieIdFilter = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : null;

$showtimes = $showtimeModel->getByDate($date);

// Group by movie and hall
$movies = [];
foreach ($showtimes as $st) {
    if ($movieIdFilter && $st['movie_id'] !== $movieIdFilter) continue;

    $mid = $st['movie_id'];
    if (!isset($movies[$mid])) {
        $movies[$mid] = [
            'id' => $mid,
            'title' => $st['title'],
            'duration' => $st['duration'],
            'genre' => $st['genre'],
            'rating' => $st['rating'],
            'user_rating' => $st['user_rating'],
            'poster_path' => $st['poster_path'],
            'halls' => []
        ];
    }

    $hid = $st['hall_id'];
    if (!isset($movies[$mid]['halls'][$hid])) {
        $movies[$mid]['halls'][$hid] = [
            'name' => $st['hall_name'],
            'times' => []
        ];
    }

    $movies[$mid]['halls'][$hid]['times'][] = [
        'id' => $st['id'],
        'time' => date('H:i', strtotime($st['start_time']))
    ];
}

include 'src/templates/header.php'; ?>


<main class="container program-main">
    <header class="program-header">
        <h1 class="hero-title">ПРОГРАМА</h1>
        <p class="program-desc">
            Открийте магията на голямото кино. Изберете филм и час за вашето следващо незабравимо преживяване.
        </p>
    </header>

    <div class="calendar-wrapper">
        <button id="cal-prev" class="btn btn-outline btn-circle">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
        
        <div id="calendar-track" class="calendar-scroll no-scrollbar">
        </div>

        <button id="cal-next" class="btn btn-outline btn-circle">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
    </div>

    <div class="movie-list">
        <?php foreach ($movies as $m): ?>
        <div class="movie-list-item">
            <a href="movie.php?id=<?php echo $m['id']; ?>" class="movie-list-poster">
                <img src="<?php echo $m['poster_path']; ?>" alt="<?php echo $m['title']; ?>">
            </a>
            <div class="movie-list-content">
                <div class="flex items-center gap-3 mb-3">
                    <h2 class="section-title mb-0"><?php echo mb_strtoupper($m['title']); ?></h2>
                    <span class="age-badge"><?php echo $m['rating']; ?></span>
                </div>
                
                <div class="movie-meta">
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">schedule</span>
                        <?php echo $m['duration']; ?> мин.
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">theater_comedy</span>
                        <?php echo $m['genre']; ?>
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon text-gold">star</span>
                        <?php echo number_format($m['user_rating'] ?? 8.5, 1); ?>
                    </div>
                </div>

                <?php foreach ($m['halls'] as $hall): ?>
                <div class="mb-6 last:mb-0">
                    <p class="hall-label"><?php echo mb_strtoupper($hall['name']); ?></p>
                    <div class="movie-card-times">
                        <?php foreach ($hall['times'] as $time): ?>
                        <a href="select-tickets.php?showtime_id=<?php echo $time['id']; ?>" class="text-none">
                            <span class="time-pill"><?php echo $time['time']; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($movies)): ?>
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-6xl opacity-20 mb-4">event_busy</span>
                <p class="text-xl opacity-50">Няма прожекции за избраната дата.</p>
                <?php if ($movieIdFilter): ?>
                    <a href="program.php?date=<?php echo $date; ?>" class="btn btn-outline mt-4">ВИЖ ВСИЧКИ ФИЛМИ</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="public/js/calendar.js"></script>

<?php include 'src/templates/footer.php'; ?>
