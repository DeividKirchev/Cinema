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
use App\Models\Booking;

$showtimeModel = new Showtime();
$bookingModel = new Booking();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: program.php");
    exit;
}

$showtime_id = (int)$_POST['showtime_id'];
$selected_seats = json_decode($_POST['selected_seats'], true);
$total_price = (float)$_POST['total_price'];

// Handle final submission
if (isset($_POST['final_submit'])) {
    $customer_name = $_POST['customer_name'] ?? 'Guest';
    $customer_email = $_POST['customer_email'];
    $payment_method = $_POST['payment_method'] ?? 'card';

    $reservationData = [
        'showtime_id' => $showtime_id,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'total_price' => $total_price,
        'payment_method' => $payment_method,
        'seats' => array_map(function($s) use ($total_price, $selected_seats) {
            return [
                'id' => $s['id'],
                'type' => $s['type'] ?? 'standard',
                'price' => $total_price / count($selected_seats)
            ];
        }, $selected_seats)
    ];

    try {
        $reservationId = $bookingModel->createReservation($reservationData);
        header("Location: order-finished.php?reservation_id=" . $reservationId);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$showtime = $showtimeModel->getById($showtime_id);

include 'src/templates/header.php'; ?>


<main class="container order-layout">
    <section>
        <header class="mb-12">
            <h1 class="hero-title text-5xl">ЗАВЪРШВАНЕ НА ПОРЪЧКАТА</h1>
            <div class="section-line w-12"></div>
        </header>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl mb-8 text-red-500 font-bold text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="order.php" method="POST" id="final-order-form">
            <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
            <input type="hidden" name="selected_seats" value='<?php echo json_encode($selected_seats); ?>'>
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
            <input type="hidden" name="final_submit" value="1">
            <input type="hidden" name="payment_method" id="input-payment-method" value="card">

            <div class="order-section">
                <h3 class="order-section-title">ДАННИ ЗА КОНТАКТ</h3>
                <div class="space-y-4">
                    <div class="input-group">
                        <span class="material-symbols-outlined input-icon">person</span>
                        <input type="text" name="customer_name" class="order-input" placeholder="Вашето име" required>
                    </div>
                    <div class="input-group">
                        <span class="material-symbols-outlined input-icon">mail</span>
                        <input type="email" name="customer_email" class="order-input" placeholder="Имейл адрес (за получаване на билетите)" required>
                    </div>
                </div>
            </div>

            <div class="order-section">
                <h3 class="order-section-title">МЕТОД НА ПЛАЩАНЕ</h3>
                <div class="payment-grid">
                    <div class="payment-card active" data-method="card">
                        <span class="material-symbols-outlined">credit_card</span>
                        <span>КАРТА</span>
                    </div>
                    <div class="payment-card" data-method="apple_pay">
                        <span class="material-symbols-outlined">payments</span>
                        <span>APPLE PAY</span>
                    </div>
                    <div class="payment-card" data-method="google_pay">
                        <span class="material-symbols-outlined">google</span>
                        <span>GOOGLE PAY</span>
                    </div>
                </div>
            </div>
    </section>

    <aside class="booking-panel">
        <h2 class="panel-title">РЕЗЮМЕ НА РЕЗЕРВАЦИЯТА</h2>
        
        <div class="flex gap-5 mb-8">
            <div class="summary-poster">
                <img src="<?php echo $showtime['poster_path']; ?>" alt="Poster" class="w-full h-full object-cover">
            </div>
            <div class="summary-details">
                <h3 class="text-lg font-black mb-1"><?php echo mb_strtoupper($showtime['title']); ?></h3>
                <p class="imax-badge mb-1"><?php echo mb_strtoupper($showtime['hall_name']); ?></p>
                <p class="text-sm text-muted"><?php echo date('d F, H:i', strtotime($showtime['start_time'])); ?> ч.</p>
            </div>
        </div>

        <div class="pt-6 border-t border-white/5 mb-6">
            <h3 class="order-section-title mb-6">РЕЗЮМЕ НА МЕСТАТА</h3>
            <?php foreach ($selected_seats as $s): ?>
            <div class="summary-item">
                <span>РЕД <?php echo $s['row']; ?>, МЯСТО <?php echo $s['seat']; ?></span>
                <span class="font-bold"><?php echo ($total_price / count($selected_seats)); ?> лв.</span>
            </div>
            <?php endforeach; ?>
        </div>

            <div class="pt-6 border-t border-white/5">
                <div class="flex justify-between items-baseline mb-6">
                    <span class="text-xs text-muted font-black">ОБЩА СУМА:</span>
                    <div class="text-right">
                        <span class="total-price"><?php echo number_format($total_price, 2); ?></span>
                        <span class="text-sm text-muted font-black ml-1">ЛВ.</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-full py-4 text-lg justify-center">
                    ПЛАТИ И РЕЗЕРВИРАЙ
                </button>
                <p class="terms-text">
                    С натискане на бутона се съгласявате с Общите условия на КИНО НОАР.
                </p>
            </div>
        </form>
    </aside>
</main>

<script>
    document.querySelectorAll('.payment-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            document.getElementById('input-payment-method').value = card.dataset.method;
        });
    });
</script>

<?php include 'src/templates/footer.php'; ?>
