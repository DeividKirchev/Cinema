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

$showtime_id = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 1;
$showtime = $showtimeModel->getById($showtime_id);

if (!$showtime) {
    header("Location: program.php");
    exit;
}

$allSeats = $showtimeModel->getSeatsByHall($showtime['hall_id']);
$reservedSeatIds = $showtimeModel->getReservedSeats($showtime_id);

// Group seats by row
$rows = [];
foreach ($allSeats as $seat) {
    $rows[$seat['row_num']][] = $seat;
}

include 'src/templates/header.php'; ?>


<main class="container selection-layout pt-32">
    <section class="seats-section">
        <header class="seats-section-header">
            <h1 class="hero-title text-3xl md:text-6xl"><?php echo mb_strtoupper($showtime['title']); ?></h1>
            <div class="flex gap-6 text-secondary text-sm font-bold uppercase flex-wrap">
                <span><?php echo date('d F, Y', strtotime($showtime['start_time'])); ?></span>
                <span><?php echo date('H:i', strtotime($showtime['start_time'])); ?> ч.</span>
                <span class="text-primary-container"><?php echo $showtime['hall_name']; ?></span>
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
                    <?php foreach ($rows as $row_num => $rowSeats): ?>
                        <div class="seat-row">
                            <span class="row-label <?php echo (max(array_column($rowSeats, 'type')) == 'vip') ? 'text-gold' : ''; ?>"><?php echo $row_num; ?></span>
                            <?php 
                            $prevSeatNum = 0;
                            foreach ($rowSeats as $seat): 
                                // Add gap if needed (simple logic: if jump in seat number)
                                if ($prevSeatNum > 0 && $seat['seat_num'] - $prevSeatNum > 1) {
                                    echo '<div class="seat-gap"></div>';
                                }
                                $prevSeatNum = $seat['seat_num'];
                                
                                $classes = ['seat'];
                                if ($seat['type'] == 'vip') $classes[] = 'vip';
                                if (in_array($seat['id'], $reservedSeatIds)) $classes[] = 'occupied';
                            ?>
                                <div class="<?php echo implode(' ', $classes); ?>" 
                                     data-id="<?php echo $seat['id']; ?>"
                                     data-row="<?php echo $seat['row_num']; ?>" 
                                     data-seat="<?php echo $seat['seat_num']; ?>"
                                     data-type="<?php echo $seat['type']; ?>">
                                    <?php echo ($seat['type'] == 'vip') ? 'VIP' : ''; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
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
            <img src="<?php echo $showtime['poster_path']; ?>" alt="Scene" class="w-full h-full object-cover">
            <div class="absolute inset-0 hero-gradient"></div>
            <div class="absolute bottom-4 left-4">
                <p class="text-xs text-primary-container font-black mb-1">ФИЛМ</p>
                <p class="text-lg font-black"><?php echo mb_strtoupper($showtime['title']); ?></p>
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
            <form action="order.php" method="POST" id="booking-form">
                <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
                <input type="hidden" name="selected_seats" id="input-selected-seats">
                <input type="hidden" name="total_price" id="input-total-price">
                <button type="submit" class="btn btn-primary w-full py-4 text-lg justify-center gap-3 opacity-50 pointer-events-none" id="btn-order">
                    ПРОДЪЛЖИ
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>
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
                const id = seat.dataset.id;
                const type = seat.dataset.type;

                if (seat.classList.contains('selected')) {
                    seat.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(s => s.id !== id);
                } else {
                    if (selectedSeats.length < totalTickets) {
                        seat.classList.add('selected');
                        selectedSeats.push({ id, row, seat: num, type });
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

            // Update hidden inputs for form submission
            document.getElementById('input-selected-seats').value = JSON.stringify(selectedSeats);
            document.getElementById('input-total-price').value = totalPrice.toFixed(2);

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