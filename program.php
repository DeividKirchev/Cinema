<?php include 'src/templates/header.php'; ?>

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
        <div class="movie-list-item">
            <a href="select-tickets.php" class="movie-list-poster">
                <img src="public/assets/images/img_15.jpg" alt="Gladiator II">
            </a>
            <div class="movie-list-content">
                <div class="flex items-center gap-3 mb-3">
                    <h2 class="section-title mb-0">ГЛАДИАТОР II</h2>
                    <span class="age-badge">16+</span>
                </div>
                
                <div class="movie-meta">
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">schedule</span>
                        180 мин.
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">theater_comedy</span>
                        Драма, Биографичен
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon text-gold">star</span>
                        8.9
                    </div>
                </div>

                <div>
                    <p class="hall-label">ЗАЛА 1 • IMAX</p>
                    <div class="movie-card-times">
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">14:30</span></a>
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">18:15</span></a>
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">21:00</span></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="movie-list-item">
            <a href="select-tickets.php" class="movie-list-poster">
                <img src="public/assets/images/img_16.jpg" alt="Dune 2">
            </a>
            <div class="movie-list-content">
                <div class="flex items-center gap-3 mb-3">
                    <h2 class="section-title mb-0">ДЮН: ЧАСТ ВТОРА</h2>
                    <span class="age-badge">12+</span>
                </div>
                
                <div class="movie-meta">
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">schedule</span>
                        166 мин.
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon">rocket_launch</span>
                        Фантастика, Екшън
                    </div>
                    <div class="meta-item">
                        <span class="material-symbols-outlined meta-icon text-gold">star</span>
                        9.1
                    </div>
                </div>

                <div>
                    <p class="hall-label">ЗАЛА 3 • LUXE</p>
                    <div class="movie-card-times">
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">13:00</span></a>
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">16:45</span></a>
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">20:30</span></a>
                        <a href="select-tickets.php" class="text-none"><span class="time-pill">23:15</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="public/js/calendar.js"></script>

<?php include 'src/templates/footer.php'; ?>
