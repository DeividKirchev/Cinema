<?php include 'src/templates/header.php'; ?>

<main class="container selection-layout">
    <section class="seats-section">
        <header class="seats-section-header">
            <h1 class="hero-title text-3xl md:text-6xl">ГЛАДИАТОР II</h1>
            <div class="flex gap-6 text-secondary text-sm font-bold uppercase flex-wrap">
                <span>25 Януари, 2025</span>
                <span>18:30 ч.</span>
                <span class="text-primary-container">Зала 1 IMAX</span>
            </div>
        </header>

        <div class="mobile-hint">
            <span class="material-symbols-outlined">swipe_left</span>
            ПЛЪЗНЕТЕ ЗА ИЗБОР НА МЕСТА
            <span class="material-symbols-outlined">swipe_right</span>
        </div>

        <div class="seat-map-wrapper" id="seat-map-wrapper">
            <div class="seat-inner">
                <div class="screen-container">
                    <div class="screen-line"></div>
                    <span class="screen-label">ЕКРАН</span>
                </div>
                <div class="screen-gap"></div>
                <div class="seat-map" id="seat-map">
                    <?php for ($r = 1; $r <= 6; $r++): ?>
                        <div class="seat-row">
                            <span class="row-label"><?php echo $r; ?></span>
                            <?php for ($s = 1; $s <= 12; $s++): ?>
                                <?php if ($s == 7) echo '<div class="seat-gap"></div>'; ?>
                                <div class="seat <?php if ($r == 1 && $s <= 2) echo 'occupied'; ?>" data-row="<?php echo $r; ?>" data-seat="<?php echo $s; ?>"></div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>

                    <div class="mb-4 h-5"></div>

                    <div class="seat-row">
                        <span class="row-label text-gold">7</span>
                        <div class="seat vip" data-row="7" data-seat="1">VIP</div>
                        <div class="seat vip" data-row="7" data-seat="2">VIP</div>
                        <div class="seat vip" data-row="7" data-seat="3">VIP</div>
                        <div class="seat-gap"></div>
                        <div class="seat vip" data-row="7" data-seat="4">VIP</div>
                        <div class="seat vip" data-row="7" data-seat="5">VIP</div>
                        <div class="seat vip" data-row="7" data-seat="6">VIP</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="seat-legend">
            <div class="legend-item">
                <div class="seat cursor-default"></div>
                <span>СВОБОДНО</span>
            </div>
            <div class="legend-item">
                <div class="seat selected cursor-default"></div>
                <span>ИЗБРАНО</span>
            </div>
            <div class="legend-item">
                <div class="seat occupied cursor-default"></div>
                <span>ЗАЕТО</span>
            </div>
            <div class="legend-item">
                <div class="seat vip cursor-default">VIP</div>
                <span>VIP ЛОЖА</span>
            </div>
        </div>
    </section>

    <aside class="booking-panel">
        <h2 class="panel-title">ДЕТАЙЛИ ЗА РЕЗЕРВАЦИЯ</h2>

        <div class="mb-8 rounded-2xl overflow-hidden aspect-video relative">
            <img src="public/assets/images/img_15.jpg" alt="Scene" class="w-full h-full object-cover">
            <div class="absolute inset-0 hero-gradient"></div>
            <div class="absolute bottom-4 left-4">
                <p class="text-xs text-primary-container font-black mb-1">ФИЛМ</p>
                <p class="text-lg font-black">ГЛАДИАТОР II</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="order-section-title">ИЗБОР НА БИЛЕТИ</h3>
            <div class="flex flex-col gap-3">
                <div class="ticket-type" data-type="standard" data-price="15">
                    <div class="flex-1">
                        <p class="text-xs font-black">СТАНДАРТЕН</p>
                        <p class="text-xs text-muted">15.00 ЛВ.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="qty-btn minus"><span class="material-symbols-outlined">remove</span></button>
                        <span class="qty-val">0</span>
                        <button class="qty-btn plus"><span class="material-symbols-outlined">add</span></button>
                    </div>
                </div>
                <div class="ticket-type" data-type="discount" data-price="12">
                    <div class="flex-1">
                        <p class="text-xs font-black">УЧАЩ / ПЕНСИОНЕР</p>
                        <p class="text-xs text-muted">12.00 ЛВ.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="qty-btn minus"><span class="material-symbols-outlined">remove</span></button>
                        <span class="qty-val">0</span>
                        <button class="qty-btn plus"><span class="material-symbols-outlined">add</span></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs text-muted font-black">ИЗБРАНИ МЕСТА</span>
                <span class="bg-red-500/10 text-primary-container py-1 px-3 rounded-lg text-xs font-black" id="count-status">0 / 0</span>
            </div>
            <div class="flex flex-col gap-2" id="selected-seats-list"></div>
        </div>

        <div class="pt-6 border-t border-white/5">
            <div class="flex justify-between items-baseline mb-6">
                <span class="text-xs text-muted font-black">ОБЩА СУМА:</span>
                <div class="text-right">
                    <span class="total-price" id="total-price">0.00</span>
                    <span class="text-sm text-muted font-black ml-1">ЛВ.</span>
                </div>
            </div>
            <a href="order.php" class="btn btn-primary w-full py-4 text-lg justify-center gap-3 opacity-50 pointer-events-none" id="btn-order">
                ПРОДЪЛЖИ
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </aside>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let totalTickets = 0;
        let selectedSeats = [];
        let totalPrice = 0;

        const qtyBtns = document.querySelectorAll('.qty-btn');
        const seats = document.querySelectorAll('.seat:not(.occupied)');
        const countStatus = document.getElementById('count-status');
        const totalPriceEl = document.getElementById('total-price');
        const btnOrder = document.getElementById('btn-order');
        const list = document.getElementById('selected-seats-list');

        qtyBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const typeRow = btn.closest('.ticket-type');
                const valEl = typeRow.querySelector('.qty-val');
                let val = parseInt(valEl.textContent);
                const price = parseInt(typeRow.dataset.price);

                if (btn.classList.contains('plus')) {
                    val++;
                    totalTickets++;
                    totalPrice += price;
                } else if (val > 0) {
                    val--;
                    totalTickets--;
                    totalPrice -= price;
                    if (selectedSeats.length > totalTickets) {
                        const last = selectedSeats.pop();
                        const lastSeatEl = document.querySelector(`.seat[data-row="${last.row}"][data-seat="${last.seat}"]`);
                        lastSeatEl.classList.remove('selected');
                    }
                }

                valEl.textContent = val;
                updateUI();
            });
        });

        seats.forEach(seat => {
            seat.addEventListener('click', () => {
                if (totalTickets === 0) {
                    alert('Моля, първо изберете брой билети!');
                    return;
                }

                const row = seat.dataset.row;
                const num = seat.dataset.seat;

                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => !(s.row === row && s.seat === num));
                } else {
                    if (selectedSeats.length < totalTickets) {
                        seat.classList.add('selected');
                        selectedSeats.push({ row, seat: num });
                    } else {
                        alert(`Можете да изберете максимум ${totalTickets} места.`);
                    }
                }
                updateUI();
            });
        });

        function updateUI() {
            countStatus.textContent = `${selectedSeats.length} / ${totalTickets}`;
            totalPriceEl.textContent = totalPrice.toFixed(2);

            list.innerHTML = '';
            selectedSeats.forEach(s => {
                const item = document.createElement('div');
                item.className = 'selected-seat-item';
                item.innerHTML = `<p class="selected-seat-text">РЕД ${s.row}, МЯСТО ${s.seat}</p>`;
                list.appendChild(item);
            });

            if (totalTickets > 0 && selectedSeats.length === totalTickets) {
                btnOrder.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                btnOrder.classList.add('opacity-50', 'pointer-events-none');
            }
        }

        // Center seat map on load (mobile only)
        const centerSeatMap = () => {
            const wrapper = document.getElementById('seat-map-wrapper');
            if (wrapper && wrapper.scrollWidth > wrapper.clientWidth) {
                wrapper.scrollLeft = (wrapper.scrollWidth - wrapper.clientWidth) / 2;
            }
        };

        window.addEventListener('load', centerSeatMap);
        setTimeout(centerSeatMap, 150);
    });
</script>

<?php include 'src/templates/footer.php'; ?>