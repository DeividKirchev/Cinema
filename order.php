<?php include 'src/templates/header.php'; ?>

<main class="container order-layout">
    <section>
        <header class="mb-12">
            <h1 class="hero-title text-5xl">ЗАВЪРШВАНЕ НА ПОРЪЧКАТА</h1>
            <div class="section-line w-12"></div>
        </header>

        <div class="order-section">
            <h3 class="order-section-title">ДАННИ ЗА КОНТАКТ</h3>
            <div class="input-group">
                <span class="material-symbols-outlined input-icon">mail</span>
                <input type="email" class="order-input" placeholder="Имейл адрес (за получаване на билетите)">
            </div>
        </div>

        <div class="order-section">
            <h3 class="order-section-title">МЕТОД НА ПЛАЩАНЕ</h3>
            <div class="payment-grid">
                <div class="payment-card active">
                    <span class="material-symbols-outlined">credit_card</span>
                    <span>КАРТА</span>
                </div>
                <div class="payment-card">
                    <span class="material-symbols-outlined">payments</span>
                    <span>APPLE PAY</span>
                </div>
                <div class="payment-card">
                    <span class="material-symbols-outlined">google</span>
                    <span>GOOGLE PAY</span>
                </div>
            </div>
        </div>

        <div class="order-section">
            <h3 class="order-section-title">ПРОМО КОД</h3>
            <div class="flex gap-4">
                <input type="text" class="order-input pl-5" placeholder="Въведете код...">
                <button class="btn btn-outline whitespace-nowrap">ПРИЛОЖИ</button>
            </div>
        </div>
    </section>

    <aside class="booking-panel">
        <h2 class="panel-title">РЕЗЮМЕ НА РЕЗЕРВАЦИЯТА</h2>
        
        <div class="flex gap-5 mb-8">
            <div class="summary-poster">
                <img src="public/assets/images/img_1.jpg" alt="Poster" class="w-full h-full object-cover">
            </div>
            <div class="summary-details">
                <h3 class="text-lg font-black mb-1">ГЛАДИАТОР II</h3>
                <p class="imax-badge mb-1">ЗАЛА 1 IMAX</p>
                <p class="text-sm text-muted">25 Януари, 18:30 ч.</p>
            </div>
        </div>

        <div class="pt-6 border-t border-white/5 mb-6">
            <h3 class="order-section-title mb-6">РЕЗЮМЕ НА БИЛЕТИТЕ</h3>
            <div class="summary-item">
                <span>2x Стандартен</span>
                <span class="font-bold">30.00 лв.</span>
            </div>
            <div class="summary-item">
                <span>1x Учащ / Пенсионер</span>
                <span class="font-bold">12.00 лв.</span>
            </div>
        </div>

        <div class="pt-6 border-t border-white/5">
            <div class="flex justify-between items-baseline mb-6">
                <span class="text-xs text-muted font-black">ОБЩА СУМА:</span>
                <div class="text-right">
                    <span class="total-price">42.00</span>
                    <span class="text-sm text-muted font-black ml-1">ЛВ.</span>
                </div>
            </div>
            <a href="order-finished.php" class="btn btn-primary w-full py-4 text-lg justify-center">
                ПЛАТИ И РЕЗЕРВИРАЙ
            </a>
            <p class="terms-text">
                С натискане на бутона се съгласявате с Общите условия на КИНО НОАР.
            </p>
        </div>
    </aside>
</main>

<?php include 'src/templates/footer.php'; ?>
