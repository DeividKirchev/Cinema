<?php include 'src/templates/header.php'; ?>

<main class="container order-finished-main pb-20">
    <div class="text-center mb-6">
        <div class="success-icon-wrapper">
            <span class="material-symbols-outlined">check</span>
        </div>
        <h1 class="text-4xl font-black text-on-surface uppercase tracking-widest mb-2">УСПЕШНА РЕЗЕРВАЦИЯ!</h1>
        <p class="text-muted text-sm">Вашите билети са изпратени на email адрес:</p>
    </div>

    <div class="selection-layout grid lg:grid-cols-12 gap-10 items-start pt-5">
        <div class="lg:col-span-7 ticket-finished-card">
            <div class="ticket-image-header relative h-[360px] overflow-hidden">
                <img src="public/assets/images/img_15.jpg" alt="Gladiator II" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1B1B25] to-transparent"></div>
                <div class="ticket-header-info">
                    <span class="imax-badge mb-2">IMAX EXPERIENCE</span>
                    <h2 class="text-3xl font-black text-white">ГЛАДИАТОР II</h2>
                </div>
            </div>
            <div class="ticket-meta-grid">
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">КОД ЗА РЕЗЕРВАЦИЯ</p>
                    <p class="text-lg font-extrabold text-on-surface">R-78291A</p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">ЗАЛА</p>
                    <p class="text-lg font-extrabold text-on-surface">ЗАЛА 1 IMAX</p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">ДАТА И ЧАС</p>
                    <p class="text-base font-extrabold text-on-surface">25 Януари, 2025 | 18:30 ч.</p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">МЕСТА</p>
                    <p class="text-sm font-extrabold text-on-surface">Ред 2, Място 5<br>Ред 2, Място 6</p>
                </div>
            </div>
            <div class="px-8 pb-8 flex gap-3">
                <button class="btn btn-primary flex-1 h-[52px] text-xs justify-center" onclick="window.print()">
                    <span class="material-symbols-outlined text-lg">download</span>
                    ИЗТЕГЛИ БИЛЕТ
                </button>
                <button class="btn btn-outline flex-1 h-[52px] text-xs justify-center bg-white/5">
                    <span class="material-symbols-outlined text-lg">mail</span>
                    ИЗПРАТИ
                </button>
            </div>
        </div>

        <div class="lg:col-span-5 flex flex-col gap-6">
            <div class="qr-panel">
                <p id="ticket-count-label" class="text-[9px] text-muted font-extrabold uppercase mb-6">СКАНИРАЙТЕ НА ВХОДА (БИЛЕТ 1 ОТ 2)</p>

                <div class="relative mb-8 flex items-center justify-center gap-3">
                    <button id="qr-prev" class="bg-white/5 border-none w-9 h-9 rounded-xl text-white cursor-pointer flex items-center justify-center hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <div class="qr-image-wrapper">
                        <img id="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=CN-78291A-1" alt="QR" class="w-full h-full">
                    </div>
                    <button id="qr-next" class="bg-white/5 border-none w-9 h-9 rounded-xl text-white cursor-pointer flex items-center justify-center hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>

                <div id="qr-dots" class="qr-dots-group">
                    <div class="qr-dot active"></div>
                    <div class="qr-dot"></div>
                </div>

                <div class="bg-white/[0.02] rounded-xl p-4 flex justify-between items-center">
                    <div class="text-left">
                        <p class="text-[8px] text-muted font-extrabold uppercase mb-0.5">БИЛЕТ</p>
                        <p id="seat-label" class="text-sm font-extrabold text-white">Ред 2, Място 5</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">confirmation_number</span>
                </div>
            </div>

            <div class="info-banner">
                <span class="material-symbols-outlined text-primary text-xl">info</span>
                <p class="text-xs text-muted leading-relaxed">Моля, бъдете в киното поне 15 минути преди началото на прожекцията. Можете да покажете QR кода директно от мобилното си устройство.</p>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qrData = [
            { seat: 'Ред 2, Място 5', code: 'CN-78291A-1' },
            { seat: 'Ред 2, Място 6', code: 'CN-78291A-2' }
        ];
        let currentIndex = 0;

        const qrImage = document.getElementById('qr-image');
        const seatLabel = document.getElementById('seat-label');
        const countLabel = document.getElementById('ticket-count-label');
        const dots = document.querySelectorAll('.qr-dot');

        function updateQR(index) {
            qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${qrData[index].code}`;
            seatLabel.textContent = qrData[index].seat;
            countLabel.textContent = `СКАНИРАЙТЕ НА ВХОДА (БИЛЕТ ${index + 1} ОТ ${qrData.length})`;

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        document.getElementById('qr-prev').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + qrData.length) % qrData.length;
            updateQR(currentIndex);
        });

        document.getElementById('qr-next').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % qrData.length;
            updateQR(currentIndex);
        });
    });
</script>


<?php include 'src/templates/footer.php'; ?>