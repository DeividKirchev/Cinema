<?php include 'src/templates/header.php'; ?>

<main>
    <section class="hero">
        <div class="hero-bg">
            <img class="hero-img" src="public/assets/images/img_15.jpg" alt="Gladiator II">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <span class="tagline">В КИНАТА ОТ ТАЗИ СЕДМИЦА</span>
                <h1 class="hero-title">ГЛАДИАТОР II</h1>
                <p class="hero-desc">
                    Епичното продължение на легендарната сага. Години след като става свидетел на смъртта на почитания герой Максимус, Луций е принуден да влезе в Колизеума, за да върне славата на Рим на неговия народ.
                </p>
                <div class="hero-btns">
                    <a href="select-tickets.php" class="btn btn-primary">
                        <span class="material-symbols-outlined">confirmation_number</span>
                        КУПИ БИЛЕТ
                    </a>
                    <a href="#" class="btn btn-outline">ТРЕЙЛЪР</a>
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
                    <button class="btn tab-item active">ДНЕС</button>
                    <button class="btn btn-outline tab-item border-none opacity-50">УТРЕ</button>
                    <button class="btn btn-outline tab-item border-none opacity-50">ВДРУГИДЕН</button>
                </div>
            </div>
        </div>

        <div class="carousel-container">
            <div class="carousel-track no-scrollbar">
                <a href="program.php" class="movie-card text-on-surface">
                    <div class="movie-card-img-container">
                        <img class="movie-card-img" src="public/assets/images/img_15.jpg" alt="Gladiator II">
                    </div>
                    <div class="movie-card-info">
                        <h3 class="movie-card-title">ГЛАДИАТОР II</h3>
                        <div class="movie-card-times">
                            <span class="time-pill">14:30</span>
                            <span class="time-pill">17:15</span>
                            <span class="time-pill">20:00</span>
                            <span class="time-pill">22:45</span>
                        </div>
                    </div>
                </a>
                <a href="program.php" class="movie-card text-on-surface">
                    <div class="movie-card-img-container">
                        <img class="movie-card-img" src="public/assets/images/img_16.jpg" alt="Dune 2">
                    </div>
                    <div class="movie-card-info">
                        <h3 class="movie-card-title">ДЮН: ЧАСТ ВТОРА</h3>
                        <div class="movie-card-times">
                            <span class="time-pill">15:00</span>
                            <span class="time-pill">18:30</span>
                            <span class="time-pill">21:45</span>
                        </div>
                    </div>
                </a>
                <a href="program.php" class="movie-card text-on-surface">
                    <div class="movie-card-img-container">
                        <img class="movie-card-img" src="public/assets/images/img_17.jpg" alt="The Wild Robot">
                    </div>
                    <div class="movie-card-info">
                        <h3 class="movie-card-title">ДИВИЯТ РОБОТ</h3>
                        <div class="movie-card-times">
                            <span class="time-pill">11:00</span>
                            <span class="time-pill">13:15</span>
                            <span class="time-pill">16:45</span>
                        </div>
                    </div>
                </a>
                <a href="program.php" class="movie-card text-on-surface">
                    <div class="movie-card-img-container">
                        <img class="movie-card-img" src="public/assets/images/img_18.jpg" alt="Smile 2">
                    </div>
                    <div class="movie-card-info">
                        <h3 class="movie-card-title">УСМИВКА 2</h3>
                        <div class="movie-card-times">
                            <span class="time-pill">19:30</span>
                            <span class="time-pill">22:00</span>
                        </div>
                    </div>
                </a>
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

    <section class="container section-padding-bottom">
        <div class="section-header">
            <div>
                <h2 class="section-title">НАЙ-ГЛЕДАНИ ФИЛМИ</h2>
                <div class="section-line"></div>
            </div>
            <a href="archive.php" class="text-secondary font-bold flex items-center gap-2">
                ВИЖ ВСИЧКИ <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>

        <div class="movie-grid-mini">
            <a href="program.php" class="movie-card text-on-surface">
                <div class="movie-card-img-container">
                    <img class="movie-card-img" src="public/assets/images/img_16.jpg" alt="Dune 2">
                </div>
                <div class="movie-card-info">
                    <h3 class="movie-card-title text-lg">ДЮН: ЧАСТ ВТОРА</h3>
                    <p class="text-muted text-xs uppercase tracking-widest">Фантастика, Екшън</p>
                </div>
            </a>
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
                    <form class="newsletter-form">
                        <input type="email" placeholder="Вашият имейл..." class="newsletter-input">
                        <button class="btn btn-primary px-12 rounded-xl font-black tracking-widest">АБОНИРАЙ СЕ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="public/js/carousel.js"></script>

<?php include 'src/templates/footer.php'; ?>
