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

$reservation_id = isset($_GET['reservation_id']) ? (int)$_GET['reservation_id'] : 0;
$tickets = $showtimeModel->getTicketsByReservation($reservation_id);

if (empty($tickets)) {
    header("Location: index.php");
    exit;
}

$firstTicket = $tickets[0];

include 'src/templates/header.php'; ?>

<main class="container order-finished-main pb-20 pt-32">
    <div class="text-center mb-6 no-print">
        <div class="success-icon-wrapper">
            <span class="material-symbols-outlined">check</span>
        </div>
        <h1 class="text-4xl font-black text-on-surface uppercase tracking-widest mb-2">УСПЕШНА РЕЗЕРВАЦИЯ!</h1>
        <p class="text-muted text-sm">Вашите билети са изпратени на email адрес: <span class="text-white font-bold"><?php echo $firstTicket['customer_email']; ?></span></p>
    </div>

    <div class="selection-layout grid lg:grid-cols-12 gap-10 items-start pt-5 no-print">
        <div class="lg:col-span-7 ticket-finished-card">
            <div class="ticket-image-header relative h-[360px] overflow-hidden">
                <img src="<?php echo $firstTicket['poster_path']; ?>" alt="Movie" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-[#1B1B25] to-transparent"></div>
                <div class="ticket-header-info">
                    <span class="imax-badge mb-2">IMAX EXPERIENCE</span>
                    <h2 class="text-3xl font-black text-white"><?php echo mb_strtoupper($firstTicket['movie_title']); ?></h2>
                </div>
            </div>
            <div class="ticket-meta-grid">
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">КОД ЗА РЕЗЕРВАЦИЯ</p>
                    <p class="text-lg font-extrabold text-on-surface"><?php echo strtoupper($firstTicket['reservation_uid']); ?></p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">ЗАЛА</p>
                    <p class="text-lg font-extrabold text-on-surface"><?php echo mb_strtoupper($firstTicket['hall_name']); ?></p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">ДАТА И ЧАС</p>
                    <p class="text-base font-extrabold text-on-surface"><?php echo date('d F, Y | H:i', strtotime($firstTicket['start_time'])); ?> ч.</p>
                </div>
                <div>
                    <p class="text-[9px] text-muted font-extrabold uppercase mb-1">МЕСТА</p>
                    <p class="text-sm font-extrabold text-on-surface">
                        <?php foreach ($tickets as $t): ?>
                            Ред <?php echo $t['row_num']; ?>, Място <?php echo $t['seat_num']; ?><br>
                        <?php endforeach; ?>
                    </p>
                </div>
            </div>
            <div class="px-8 pb-8 flex gap-3 no-print">
                <button class="btn btn-primary flex-1 h-[52px] text-xs justify-center" onclick="window.print()">
                    <span class="material-symbols-outlined text-lg">download</span>
                    ИЗТЕГЛИ ВСИЧКИ БИЛЕТИ
                </button>
            </div>
        </div>

        <div class="lg:col-span-5 flex flex-col gap-6 no-print">
            <div class="qr-panel">
                <p id="ticket-count-label" class="text-[9px] text-muted font-extrabold uppercase mb-6">СКАНИРАЙТЕ НА ВХОДА (БИЛЕТ 1 ОТ <?php echo count($tickets); ?>)</p>

                <div class="relative mb-8 flex items-center justify-center gap-3">
                    <button id="qr-prev" class="bg-white/5 border-none w-9 h-9 rounded-xl text-white cursor-pointer flex items-center justify-center hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </button>
                    <div class="qr-image-wrapper">
                        <img id="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?php echo $firstTicket['uid']; ?>" alt="QR" class="w-full h-full">
                    </div>
                    <button id="qr-next" class="bg-white/5 border-none w-9 h-9 rounded-xl text-white cursor-pointer flex items-center justify-center hover:bg-white/10 transition-colors">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </button>
                </div>

                <div id="qr-dots" class="qr-dots-group">
                    <?php foreach ($tickets as $i => $t): ?>
                        <div class="qr-dot <?php echo $i === 0 ? 'active' : ''; ?>"></div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white/[0.02] rounded-xl p-4 flex justify-between items-center">
                    <div class="text-left">
                        <p class="text-[8px] text-muted font-extrabold uppercase mb-0.5">БИЛЕТ</p>
                        <p id="seat-label" class="text-sm font-extrabold text-white">Ред <?php echo $firstTicket['row_num']; ?>, Място <?php echo $firstTicket['seat_num']; ?></p>
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

    <!-- Printable area for all tickets -->
    <div class="print-only">
        <?php foreach ($tickets as $t): ?>
            <div class="print-ticket">
                <h1 style="margin: 0 0 20px 0;">CINEMA NOIR - БИЛЕТ</h1>
                <div style="display: flex; gap: 40px;">
                    <div style="flex: 1;">
                        <h2 style="margin: 0 0 10px 0;"><?php echo mb_strtoupper($t['movie_title']); ?></h2>
                        <p><strong>ЗАЛА:</strong> <?php echo mb_strtoupper($t['hall_name']); ?></p>
                        <p><strong>ДАТА:</strong> <?php echo date('d.m.Y H:i', strtotime($t['start_time'])); ?></p>
                        <p><strong>МЯСТО:</strong> РЕД <?php echo $t['row_num']; ?>, МЯСТО <?php echo $t['seat_num']; ?></p>
                        <p><strong>КОД:</strong> <?php echo strtoupper($t['reservation_uid']); ?></p>
                    </div>
                    <div style="width: 150px; height: 150px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $t['uid']; ?>" alt="QR" style="width: 100%;">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<style>
    @media print {
        /* Hide everything by default */
        body > * { display: none !important; }
        
        /* Show only the main container and the print-only section */
        main { display: block !important; padding: 0 !important; margin: 0 !important; }
        main > * { display: none !important; }
        .print-only, .print-only * { display: block !important; }
        
        body, main { 
            background: white !important; 
            color: black !important;
        }

        .print-ticket {
            break-before: page !important;
            padding: 40px !important;
            border-bottom: 1px dashed #ccc !important;
            color: #000 !important;
            background: #fff !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
        }

        .print-ticket:first-child {
            break-before: avoid !important;
        }
    }
    .print-only { display: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const qrData = <?php echo json_encode(array_map(function($t) {
            return [
                'seat' => "Ред {$t['row_num']}, Място {$t['seat_num']}",
                'code' => $t['uid']
            ];
        }, $tickets)); ?>;
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