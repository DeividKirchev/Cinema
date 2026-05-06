<?php include 'src/templates/header.php'; ?>

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
        <?php 
        $movies = [
            ['title' => 'Гладиатор II', 'genre' => 'Екшън', 'year' => '2024', 'rating' => '8.9', 'img' => 'img_15.jpg'],
            ['title' => 'Дюн: Част втора', 'genre' => 'Sci-Fi', 'year' => '2024', 'rating' => '9.1', 'img' => 'img_2.jpg'],
            ['title' => 'Опенхаймер', 'genre' => 'Драма', 'year' => '2023', 'rating' => '8.6', 'img' => 'img_3.jpg'],
            ['title' => 'Барби', 'genre' => 'Комедия', 'year' => '2023', 'rating' => '7.2', 'img' => 'img_4.jpg'],
            ['title' => 'Интерстелар', 'genre' => 'Sci-Fi', 'year' => '2014', 'rating' => '8.7', 'img' => 'img_5.jpg'],
            ['title' => 'Батман', 'genre' => 'Екшън', 'year' => '2022', 'rating' => '8.0', 'img' => 'img_6.jpg'],
            ['title' => 'Генезис', 'genre' => 'Трилър', 'year' => '2010', 'rating' => '8.8', 'img' => 'img_7.jpg'],
            ['title' => 'Жокера', 'genre' => 'Драма', 'year' => '2019', 'rating' => '8.4', 'img' => 'img_8.jpg'],
        ];

        foreach($movies as $movie): ?>
        <a href="movie.php" class="movie-card text-on-surface">
            <div class="movie-card-img-container">
                <img class="movie-card-img" src="public/assets/images/<?php echo $movie['img']; ?>" alt="<?php echo $movie['title']; ?>">
            </div>
            <div class="movie-card-info">
                <div class="movie-card-header">
                    <h3 class="movie-card-title"><?php echo $movie['title']; ?></h3>
                    <div class="movie-card-rating">
                        <span class="material-symbols-outlined icon-fill">star</span>
                        <?php echo $movie['rating']; ?>
                    </div>
                </div>
                <p class="text-muted text-xs uppercase tracking-widest">
                    <?php echo $movie['genre']; ?> • <?php echo $movie['year']; ?>
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

