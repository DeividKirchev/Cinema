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

session_start();

require_once __DIR__ . '/config/database.php';
use App\Models\User;

$userModel = new User();
$db = Database::getInstance();

$error = '';
if (isset($_POST['login'])) {
    $user = $userModel->login($_POST['username'], $_POST['password']);
    if ($user) {
        $_SESSION['admin_user'] = $user['username'];
    } else {
        $error = 'Невалидно потребителско име или парола.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$isLoggedIn = isset($_SESSION['admin_user']);

// Statistics
$stats = [
    'revenue' => 0,
    'tickets' => 0,
    'movies' => 0,
    'reservations' => 0
];

if ($isLoggedIn) {
    // Revenue
    $stmt = $db->query("SELECT SUM(total_price) FROM reservations WHERE status = 'confirmed'");
    $stats['revenue'] = $stmt->fetchColumn() ?: 0;

    // Tickets
    $stmt = $db->query("SELECT COUNT(*) FROM reserved_seats rs JOIN reservations r ON rs.reservation_id = r.id WHERE r.status = 'confirmed'");
    $stats['tickets'] = $stmt->fetchColumn();

    // Active Movies
    $stmt = $db->query("SELECT COUNT(*) FROM movies WHERE status = 'now playing'");
    $stats['movies'] = $stmt->fetchColumn();

    // Reservations
    $stmt = $db->query("SELECT COUNT(*) FROM reservations");
    $stats['reservations'] = $stmt->fetchColumn();
}

include 'src/templates/header.php'; ?>

<main class="container" style="padding-top: 140px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <?php if (!$isLoggedIn): ?>
    <div style="background: var(--surface); padding: 48px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); width: 100%; max-width: 450px; box-shadow: 0 40px 80px rgba(0,0,0,0.5);">
        <h2 class="hero-title" style="font-size: 32px; margin-bottom: 8px; text-align: center;">АДМИН ПАНЕЛ</h2>
        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 40px;">Влезте в своя акаунт</p>
        
        <?php if ($error): ?>
            <p style="color: #ff4d4d; text-align: center; margin-bottom: 20px; font-size: 14px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
            <input type="hidden" name="login" value="1">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Потребителско име</label>
                <input type="text" name="username" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="admin" required>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Парола</label>
                <input type="password" name="password" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px;">ВХОД</button>
        </form>
    </div>
    <?php else: ?>
    <div style="width: 100%;">
        <header style="margin-bottom: 48px; display: flex; justify-content: space-between; align-items: center;">
            <h1 class="hero-title" style="font-size: 40px;">СТАТИСТИКА</h1>
            <a href="admin.php?logout=1" class="btn btn-outline">ИЗХОД</a>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; margin-bottom: 64px;">
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">ОБЩ ПРИХОД</p>
                <h3 style="font-size: 28px;"><?php echo number_format($stats['revenue'], 2); ?> лв.</h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">ПРОДАДЕНИ БИЛЕТИ</p>
                <h3 style="font-size: 28px;"><?php echo $stats['tickets']; ?></h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">АКТИВНИ ФИЛМИ</p>
                <h3 style="font-size: 28px;"><?php echo $stats['movies']; ?></h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">ОБЩО РЕЗЕРВАЦИИ</p>
                <h3 style="font-size: 28px;"><?php echo $stats['reservations']; ?></h3>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include 'src/templates/footer.php'; ?>
