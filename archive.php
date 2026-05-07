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

$movieModel = new Movie();
$movies = $movieModel->getAll();

include 'src/templates/header.php'; ?>


<main class="container archive-main">
    <!-- Hero Section -->
    <header class="archive-hero">
        <h1 class="archive-title">Филми</h1>
        <p class="archive-desc">
            Открийте магията на голямата сцена. Разгледайте най-новите блокбъстъри и вечни класики в нашия селектиран каталог.
        </p>
    </header>

    <!-- Filters Bar -->
    <div class="archive-filters">
        <div class="status-tabs desktop-only">
            <button class="tab-btn active">Всички</button>
            <button class="tab-btn">Минали</button>
            <button class="tab-btn">Предстоящи</button>
            <button class="tab-btn">Актуални</button>
        </div>

        <div class="search-filter-group">
            <div class="select-wrapper mobile-only">
                <select class="custom-select">
                    <option>Всички филми</option>
                    <option>Минали</option>
                    <option>Предстоящи</option>
                    <option>Актуални</option>
                </select>
                <span class="material-symbols-outlined select-icon">expand_more</span>
            </div>
            <div class="search-wrapper">
                <span class="material-symbols-outlined">search</span>
                <input type="text" placeholder="Търси филм...">
            </div>
            <div class="select-wrapper">
                <select class="custom-select">
                    <option>Всички жанрове</option>
                    <option>Екшън</option>
                    <option>Драма</option>
                    <option>Комедия</option>
                    <option>Sci-Fi</option>
                </select>
                <span class="material-symbols-outlined select-icon">expand_more</span>
            </div>
        </div>
    </div>

    <!-- Movie Grid -->
    <div class="movie-grid">
        <?php foreach($movies as $movie): ?>
        <a href="movie.php?id=<?php echo $movie['id']; ?>" class="movie-card text-on-surface">
            <div class="movie-card-img-container">
                <img class="movie-card-img" src="<?php echo $movie['poster_path']; ?>" alt="<?php echo $movie['title']; ?>">
            </div>
            <div class="movie-card-info">
                <div class="movie-card-header">
                    <h3 class="movie-card-title"><?php echo $movie['title']; ?></h3>
                    <div class="movie-card-rating">
                        <span class="material-symbols-outlined icon-fill">star</span>
                        8.9
                    </div>
                </div>
                <p class="text-muted text-xs uppercase tracking-widest">
                    <?php echo $movie['genre']; ?> • <?php echo date('Y', strtotime($movie['release_date'])); ?>
                </p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <a href="#" class="page-link active">1</a>
        <a href="#" class="page-link">2</a>
        <a href="#" class="page-link">3</a>
        <span class="text-muted mx-2">...</span>
        <a href="#" class="page-link">12</a>
    </div>
</main>

<?php include 'src/templates/footer.php'; ?>

