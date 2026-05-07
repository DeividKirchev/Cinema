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

// Filters & Pagination logic
$filters = [];
if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
if (!empty($_GET['genre'])) $filters['genre'] = $_GET['genre'];
if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12; // movies per page
$filters['limit'] = $limit;
$filters['offset'] = ($page - 1) * $limit;

$movies = $movieModel->getAll($filters);
$totalMovies = $movieModel->getCount($filters);
$totalPages = ceil($totalMovies / $limit);

$activeStatus = $filters['status'] ?? '';
$activeGenre = $filters['genre'] ?? '';
$searchQuery = $filters['search'] ?? '';

// Helper to build URL with current params
function buildUrl($params) {
    $current = $_GET;
    $merged = array_merge($current, $params);
    return '?' . http_build_query($merged);
}


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
            <a href="archive.php<?php echo buildUrl(['status'=>'', 'page'=>1]); ?>" class="tab-btn <?php echo $activeStatus=='' ? 'active' : ''; ?>">Всички</a>
            <a href="archive.php<?php echo buildUrl(['status'=>'past', 'page'=>1]); ?>" class="tab-btn <?php echo $activeStatus=='past' ? 'active' : ''; ?>">Минали</a>
            <a href="archive.php<?php echo buildUrl(['status'=>'coming_soon', 'page'=>1]); ?>" class="tab-btn <?php echo $activeStatus=='coming_soon' ? 'active' : ''; ?>">Предстоящи</a>
            <a href="archive.php<?php echo buildUrl(['status'=>'now_playing', 'page'=>1]); ?>" class="tab-btn <?php echo $activeStatus=='now_playing' ? 'active' : ''; ?>">Актуални</a>
        </div>

        <form method="GET" action="archive.php" class="search-filter-group">
            <?php if(!empty($activeStatus)): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($activeStatus); ?>"><?php endif; ?>
            <div class="select-wrapper mobile-only">
                <select class="custom-select" name="status" onchange="this.form.submit()">
                    <option value="" <?php echo $activeStatus==''?'selected':''; ?>>Всички филми</option>
                    <option value="past" <?php echo $activeStatus=='past'?'selected':''; ?>>Минали</option>
                    <option value="coming_soon" <?php echo $activeStatus=='coming_soon'?'selected':''; ?>>Предстоящи</option>
                    <option value="now_playing" <?php echo $activeStatus=='now_playing'?'selected':''; ?>>Актуални</option>
                </select>
                <span class="material-symbols-outlined select-icon">expand_more</span>
            </div>
            <div class="search-wrapper">
                <span class="material-symbols-outlined">search</span>
                <input type="text" name="search" placeholder="Търси филм..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="select-wrapper">
                <select class="custom-select" name="genre" onchange="this.form.submit()">
                    <option value="">Всички жанрове</option>
                    <option value="Екшън" <?php echo $activeGenre=='Екшън'?'selected':''; ?>>Екшън</option>
                    <option value="Драма" <?php echo $activeGenre=='Драма'?'selected':''; ?>>Драма</option>
                    <option value="Комедия" <?php echo $activeGenre=='Комедия'?'selected':''; ?>>Комедия</option>
                    <option value="Sci-Fi" <?php echo $activeGenre=='Sci-Fi'?'selected':''; ?>>Sci-Fi</option>
                    <option value="Ужаси" <?php echo $activeGenre=='Ужаси'?'selected':''; ?>>Ужаси</option>
                    <option value="Анимация" <?php echo $activeGenre=='Анимация'?'selected':''; ?>>Анимация</option>
                </select>
                <span class="material-symbols-outlined select-icon">expand_more</span>
            </div>
        </form>
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
                        <?php echo number_format($movie['user_rating'] ?? 8.5, 1); ?>
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
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="archive.php<?php echo buildUrl(['page' => $i]); ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</main>

<?php include 'src/templates/footer.php'; ?>

