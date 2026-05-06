<?php include 'src/templates/header.php'; ?>

<main class="relative pb-32">
    <!-- Hero Section -->
    <section class="movie-hero">
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover" alt="Гладиатор II" src="public/assets/images/img_23.jpg" />
            <div class="absolute inset-0 hero-gradient"></div>
            <div class="absolute inset-0 hero-overlay-side opacity-80">
            </div>
        </div>
        <div class="movie-hero-content container mx-auto px-6">
            <div class="max-w-4xl space-y-6">
                <div class="flex items-center gap-4 animate-fade-in">
                    <span
                        class="px-3 py-1 bg-primary-container text-on-primary-container font-bold text-xs rounded uppercase tracking-widest">Премиера</span>
                    <div class="flex items-center gap-1 text-secondary">
                        <span class="material-symbols-outlined text-sm icon-fill">star</span>
                        <span class="font-bold text-lg">8.0</span>
                        <span class="text-slate-400 text-xs ml-1 font-medium">IMDb</span>
                    </div>
                    <span class="text-slate-300 text-sm font-medium border-l border-white/20 pl-4">150 мин</span>
                </div>
                <h1
                    class="text-4xl md:text-8xl font-black font-headline tracking-tighter text-on-surface uppercase text-shadow-cinematic leading-none">
                    Гладиатор II
                </h1>
                <div class="flex flex-wrap gap-4 pt-4">
                    <button
                        class="w-full md:w-auto px-10 py-4 cta-gradient text-on-primary-container font-bold rounded-xl text-lg flex items-center justify-center gap-3 transition-transform hover:scale-105 active:scale-95 shadow-[0_0_20px_rgba(229,9,20,0.3)]">
                        Резервирай билет
                        <span class="material-symbols-outlined">confirmation_number</span>
                    </button>
                    <button
                        class="w-full md:w-auto px-10 py-4 bg-surface-variant/20 backdrop-blur-md border border-white/10 text-white font-bold rounded-xl text-lg hover:bg-surface-variant/40 transition-all flex items-center justify-center gap-3">
                        Трейлър
                        <span class="material-symbols-outlined">play_circle</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Grid -->
    <section class="container mx-auto px-6 mt-8 md:-mt-8 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Left Column: Details -->
            <div class="lg:col-span-8 space-y-12">
                <!-- Synopsis -->
                <div class="p-8 rounded-3xl bg-surface-container-low/40 backdrop-blur-xl">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-red-500 mb-6">Синопсис</h3>
                    <p class="text-xl leading-relaxed text-slate-300 line-clamp-3 transition-all duration-500" id="synopsis-text">
                        Години след като е станал свидетел на смъртта на почитания герой Максимус от ръцете на чичо си,
                        Луций е принуден да влезе в Колизеума, след като домът му е покорен от тираничните императори,
                        които сега управляват Рим с железен юмрук. С гняв в сърцето и бъдещето на Империята на карта,
                        Луций трябва да погледне към миналото си, за да намери сила и чест.
                    </p>
                    <button id="synopsis-toggle" class="btn-text mt-4 flex items-center gap-2 text-red-500 font-bold text-sm uppercase tracking-wider hover:text-red-400 transition-colors">
                        <span>Виж още</span>
                        <span class="material-symbols-outlined transition-transform duration-300">expand_more</span>
                    </button>
                </div>

                <!-- Cast Bento -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-red-500 mb-8">Актьорски
                        състав</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div
                            class="group relative overflow-hidden rounded-2xl bg-surface-container aspect-square transition-all">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 group-hover:scale-110"
                                alt="Пол Мескал" src="public/assets/images/img_24.jpg" />
                            <div class="actor-overlay">
                                <p class="font-bold text-white">Пол Мескал</p>
                                <p class="text-xs text-slate-400">Луций</p>
                            </div>
                        </div>
                        <div
                            class="group relative overflow-hidden rounded-2xl bg-surface-container aspect-square transition-all">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 group-hover:scale-110"
                                alt="Педро Паскал" src="public/assets/images/img_25.jpg" />
                            <div class="actor-overlay">
                                <p class="font-bold text-white">Педро Паскал</p>
                                <p class="text-xs text-slate-400">Марк Акаций</p>
                            </div>
                        </div>
                        <div
                            class="group relative overflow-hidden rounded-2xl bg-surface-container aspect-square transition-all">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 group-hover:scale-110"
                                alt="Дензъл Уошингтън" src="public/assets/images/img_26.jpg" />
                            <div class="actor-overlay">
                                <p class="font-bold text-white">Дензъл Уошингтън</p>
                                <p class="text-xs text-slate-400">Макрин</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Info -->
            <div class="lg:col-span-4 space-y-6">
                <div class="p-8 rounded-3xl bg-surface-container-high space-y-8">
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Режисьор</h4>
                        <p class="text-lg font-bold text-white">Ридли Скот</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Жанр</h4>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Екшън</span>
                            <span
                                class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Драма</span>
                            <span
                                class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300">Приключенски</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Продукция</h4>
                        <p class="text-sm text-slate-300">Paramount Pictures, Scott Free Productions</p>
                    </div>
                    <div class="pt-6 border-t border-white/5">
                        <a class="flex items-center justify-between group" href="https://www.imdb.com/title/tt9218128/"
                            target="_blank">
                            <div class="flex items-center gap-3">
                                <span class="imdb-tag">IMDb</span>
                                <span class="text-slate-300 group-hover:text-white transition-colors">Виж в IMDb</span>
                            </div>
                            <span
                                class="material-symbols-outlined text-slate-500 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Promo Slot -->
                <div class="relative group overflow-hidden rounded-3xl aspect-[4/5]">
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        alt="IMAX Experience" src="public/assets/images/img_27.jpg" />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-primary-container/80 via-black/20 to-transparent flex flex-col justify-end p-8">
                        <h4 class="text-2xl font-black font-headline text-white mb-2 uppercase leading-tight">Преживейте
                            го в IMAX</h4>
                        <p class="text-sm text-white/80 mb-6">Най-големият екран за най-великия епос.</p>
                        <button
                            class="w-full py-3 bg-white text-primary-container font-black rounded-xl uppercase tracking-tighter text-sm hover:bg-slate-100 transition-colors">
                            Избери IMAX зала
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('synopsis-toggle').addEventListener('click', function() {
    const text = document.getElementById('synopsis-text');
    const icon = this.querySelector('.material-symbols-outlined');
    const label = this.querySelector('span');
    
    if (text.classList.contains('line-clamp-3')) {
        text.classList.remove('line-clamp-3');
        text.classList.add('line-clamp-none');
        icon.classList.add('rotate-180');
        label.textContent = 'Виж по-малко';
    } else {
        text.classList.add('line-clamp-3');
        text.classList.remove('line-clamp-none');
        icon.classList.remove('rotate-180');
        label.textContent = 'Виж още';
    }
});
</script>


<?php include 'src/templates/footer.php'; ?>