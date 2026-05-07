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
use App\Models\Movie;
use App\Models\Hall;
use App\Models\Showtime;
use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\Actor;

$userModel = new User();
$db = Database::getInstance();

$error = '';
$success = '';

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

if ($isLoggedIn) {
    $movieModel = new Movie();
    $hallModel = new Hall();
    $showtimeModel = new Showtime();
    $bookingModel = new Booking();
    $promoModel = new PromoCode();
    $actorModel = new Actor();

    // Tab and Action routing
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

    // Handle delete actions safely
    if ($action === 'delete' && $id) {
        if ($tab === 'movies') {
            $movieModel->delete($id);
            header("Location: admin.php?tab=movies&success=Филмът+е+изтрит+успешно");
            exit;
        } elseif ($tab === 'halls') {
            $hallModel->delete($id);
            header("Location: admin.php?tab=halls&success=Залата+е+изтрита+успешно");
            exit;
        } elseif ($tab === 'showtimes') {
            $showtimeModel->delete($id);
            header("Location: admin.php?tab=showtimes&success=Прожекцията+е+изтрита+успешно");
            exit;
        } elseif ($tab === 'reservations') {
            $bookingModel->delete($id);
            header("Location: admin.php?tab=reservations&success=Резервацията+е+изтрита+успешно");
            exit;
        } elseif ($tab === 'promo_codes') {
            $promoModel->delete($id);
            header("Location: admin.php?tab=promo_codes&success=Промо+кодът+е+изтрит+успешно");
            exit;
        } elseif ($tab === 'actors') {
            $actorModel->delete($id);
            header("Location: admin.php?tab=actors&success=Актьорът+е+изтрит+успешно");
            exit;
        }
    }

    // Handle POST actions (Save/Update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
        if ($tab === 'movies') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'duration' => (int)($_POST['duration'] ?? 0),
                'genre' => trim($_POST['genre'] ?? ''),
                'rating' => trim($_POST['rating'] ?? ''),
                'release_date' => trim($_POST['release_date'] ?? ''),
                'director' => trim($_POST['director'] ?? ''),
                'trailer_url' => trim($_POST['trailer_url'] ?? ''),
                'poster_path' => trim($_POST['poster_path'] ?? ''),
                'status' => trim($_POST['status'] ?? 'now playing'),
                'user_rating' => (float)($_POST['user_rating'] ?? 8.5)
            ];

            // Keep an empty cast array in movies table for DB schema requirements
            $data['cast'] = '[]';

            if (empty($data['title'])) {
                $error = 'Заглавието е задължително поле.';
            } elseif ($data['duration'] <= 0) {
                $error = 'Времетраенето трябва да бъде положително число в минути.';
            } elseif (empty($data['release_date'])) {
                $error = 'Моля, изберете дата на премиера.';
            } else {
                if ($action === 'add') {
                    $movieId = $movieModel->create($data);
                    // Sync actors in the many-to-many table
                    if (isset($_POST['actors']) && is_array($_POST['actors'])) {
                        $movieModel->syncActors($movieId, $_POST['actors']);
                    }
                    header("Location: admin.php?tab=movies&success=Филмът+е+добавен+успешно");
                    exit;
                } elseif ($action === 'edit' && $id) {
                    $movieModel->update($id, $data);
                    // Sync actors in the many-to-many table
                    if (isset($_POST['actors']) && is_array($_POST['actors'])) {
                        $movieModel->syncActors($id, $_POST['actors']);
                    } else {
                        $movieModel->syncActors($id, []); // clear all links if nothing selected
                    }
                    header("Location: admin.php?tab=movies&success=Филмът+е+редактиран+успешно");
                    exit;
                }
            }
        }

        if ($tab === 'actors') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'birth_date' => trim($_POST['birth_date'] ?? ''),
                'image_url' => trim($_POST['image_url'] ?? '')
            ];

            if (empty($data['name'])) {
                $error = 'Името на актьора е задължително поле.';
            } else {
                if ($action === 'add') {
                    $actorModel->create($data);
                    header("Location: admin.php?tab=actors&success=Актьорът+е+добавен+успешно");
                    exit;
                } elseif ($action === 'edit' && $id) {
                    $actorModel->update($id, $data);
                    header("Location: admin.php?tab=actors&success=Актьорът+е+редактиран+успешно");
                    exit;
                }
            }
        }

        if ($tab === 'halls') {
            $name = trim($_POST['name'] ?? '');
            $rows = (int)($_POST['rows'] ?? 8);
            $cols = (int)($_POST['cols'] ?? 10);

            if (empty($name)) {
                $error = 'Името на залата е задължително поле.';
            } else {
                if ($action === 'add') {
                    if ($rows <= 0 || $cols <= 0) {
                        $error = 'Редовете и колоните трябва да са по-големи от 0 за генериране на места.';
                    } elseif ($rows > 25 || $cols > 30) {
                        $error = 'Залите могат да имат максимум 25 реда и 30 колони.';
                    } else {
                        $hallModel->create($name, $rows, $cols);
                        header("Location: admin.php?tab=halls&success=Залата+е+добавена+успешно+с+генерирани+места");
                        exit;
                    }
                } elseif ($action === 'edit' && $id) {
                    $hallModel->update($id, $name);
                    header("Location: admin.php?tab=halls&success=Името+на+залата+е+променено+успешно");
                    exit;
                }
            }
        }

        if ($tab === 'showtimes') {
            $data = [
                'movie_id' => (int)($_POST['movie_id'] ?? 0),
                'hall_id' => (int)($_POST['hall_id'] ?? 0),
                'start_time' => trim($_POST['start_time'] ?? ''),
                'base_price' => (float)($_POST['base_price'] ?? 0)
            ];

            $movie = $movieModel->getById($data['movie_id']);
            $hall = $hallModel->getById($data['hall_id']);

            if (!$movie) {
                $error = 'Моля, изберете съществуващ филм.';
            } elseif (!$hall) {
                $error = 'Моля, изберете съществуваща зала.';
            } elseif (empty($data['start_time'])) {
                $error = 'Началният час е задължителен.';
            } elseif ($data['base_price'] <= 0) {
                $error = 'Базовата цена трябва да бъде по-голяма от 0 лв.';
            } else {
                // Check for overlapping showtimes
                $excludeId = ($action === 'edit') ? $id : null;
                $hasOverlap = $showtimeModel->checkOverlap($data['hall_id'], $data['start_time'], $movie['duration'], $excludeId);

                if ($hasOverlap) {
                    $error = 'Времеви конфликт! Залата е заета по това време от друга прожекция (с включени 20 мин. за почистване).';
                } else {
                    if ($action === 'add') {
                        $showtimeModel->create($data);
                        header("Location: admin.php?tab=showtimes&success=Прожекцията+е+добавена+успешно");
                        exit;
                    } elseif ($action === 'edit' && $id) {
                        $showtimeModel->update($id, $data);
                        header("Location: admin.php?tab=showtimes&success=Прожекцията+е+редактирана+успешно");
                        exit;
                    }
                }
            }
        }

        if ($tab === 'promo_codes') {
            $data = [
                'code' => strtoupper(trim($_POST['code'] ?? '')),
                'discount_percent' => (int)($_POST['discount_percent'] ?? 0),
                'valid_until' => trim($_POST['valid_until'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['code'])) {
                $error = 'Кодът на купона е задължителен.';
            } elseif ($data['discount_percent'] <= 0 || $data['discount_percent'] > 100) {
                $error = 'Процентът на отстъпка трябва да бъде между 1 и 100%.';
            } else {
                if ($action === 'add') {
                    $promoModel->create($data);
                    header("Location: admin.php?tab=promo_codes&success=Промо+кодът+е+създаден+успешно");
                    exit;
                } elseif ($action === 'edit' && $id) {
                    $promoModel->update($id, $data);
                    header("Location: admin.php?tab=promo_codes&success=Промо+кодът+е+редактиран+успешно");
                    exit;
                }
            }
        }

        if ($tab === 'reservations' && $action === 'edit' && $id) {
            $data = [
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'customer_email' => trim($_POST['customer_email'] ?? ''),
                'payment_method' => trim($_POST['payment_method'] ?? 'cash'),
                'status' => trim($_POST['status'] ?? 'confirmed'),
                'total_price' => (float)($_POST['total_price'] ?? 0)
            ];

            if (empty($data['customer_name'])) {
                $error = 'Името на клиента е задължително.';
            } elseif (empty($data['customer_email']) || !filter_var($data['customer_email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Моля, въведете валиден имейл адрес.';
            } elseif ($data['total_price'] < 0) {
                $error = 'Крайната цена не може да бъде отрицателно число.';
            } else {
                $bookingModel->updateReservation($id, $data);
                header("Location: admin.php?tab=reservations&success=Резервацията+е+редактирана+успешно");
                exit;
            }
        }
    }

    // Capture success message from redirect URL
    if (isset($_GET['success'])) {
        $success = htmlspecialchars($_GET['success']);
    }

    // Statistics Calculation
    $stats = [
        'revenue' => 0,
        'tickets' => 0,
        'movies' => 0,
        'reservations' => 0
    ];

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

<main class="container" style="padding-top: 140px; min-height: 85vh; display: flex; flex-direction: column;">
    <?php if (!$isLoggedIn): ?>
    <!-- Login Screen -->
    <div style="margin: auto; background: var(--surface); padding: 48px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); width: 100%; max-width: 450px; box-shadow: 0 40px 80px rgba(0,0,0,0.5);">
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
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px; justify-content: center;">ВХОД</button>
        </form>
    </div>
    
    <?php else: ?>
    <!-- Admin Workspace Grid -->
    <div style="width: 100%; display: flex; flex-direction: column; gap: 32px; margin-bottom: 64px;">
        <header style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 20px;">
            <div>
                <h1 class="hero-title" style="font-size: 36px; margin: 0;">АДМИНИСТРАТИВЕН ПАНЕЛ</h1>
                <p style="color: var(--text-secondary); margin: 4px 0 0 0;">Управление на резервации, филми, зали и прожекции</p>
            </div>
            <div style="display: flex; align-items: center; gap: 16px;">
                <span class="text-xs text-muted font-bold">Здравейте, <?php echo htmlspecialchars($_SESSION['admin_user']); ?>!</span>
                <a href="admin.php?logout=1" class="btn btn-outline" style="padding: 10px 20px; font-size: 12px; border-radius: 10px;">ИЗХОД</a>
            </div>
        </header>

        <!-- Feedback Messages -->
        <?php if ($success): ?>
            <div style="background: rgba(74, 222, 128, 0.1); border-left: 4px solid #4ade80; color: #4ade80; padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: bold; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span><?php echo $success; ?></span>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; color: #ef4444; padding: 16px 24px; border-radius: 12px; font-size: 14px; font-weight: bold; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <span><?php echo $error; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 32px; align-items: flex-start; flex-wrap: wrap; width: 100%;">
            <!-- Sidebar Navigation (Desktop) -->
            <aside style="width: 260px; flex-shrink: 0; background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <a href="admin.php?tab=dashboard" class="btn <?php echo $tab === 'dashboard' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">dashboard</span>ТАБЛО
                </a>
                <a href="admin.php?tab=movies" class="btn <?php echo $tab === 'movies' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">movie</span>ФИЛМИ
                </a>
                <a href="admin.php?tab=actors" class="btn <?php echo $tab === 'actors' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">person</span>АКТЬОРИ
                </a>
                <a href="admin.php?tab=halls" class="btn <?php echo $tab === 'halls' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">theater_comedy</span>ЗАЛИ
                </a>
                <a href="admin.php?tab=showtimes" class="btn <?php echo $tab === 'showtimes' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">schedule</span>ПРОЖЕКЦИИ
                </a>
                <a href="admin.php?tab=reservations" class="btn <?php echo $tab === 'reservations' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">confirmation_number</span>РЕЗЕРВАЦИИ
                </a>
                <a href="admin.php?tab=promo_codes" class="btn <?php echo $tab === 'promo_codes' ? 'btn-primary' : 'btn-outline border-none opacity-70'; ?>" style="justify-content: flex-start; gap: 12px; width: 100%; padding: 14px 20px; border-radius: 14px;">
                    <span class="material-symbols-outlined">sell</span>ПРОМО КОДОВЕ
                </a>
            </aside>

            <!-- Workspace Body -->
            <div style="flex: 1; min-width: 320px;">
                <?php if ($tab === 'dashboard'): ?>
                    <!-- Statistics Dashboard View -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 24px; margin-bottom: 48px;">
                        <div style="background: var(--surface); padding: 28px; border-radius: 20px; border-left: 4px solid var(--primary); box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                            <p style="color: var(--text-muted); font-size: 11px; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.1em; text-transform: uppercase;">Общ приход</p>
                            <h3 style="font-size: 26px; margin: 0; color: #4ade80;"><?php echo number_format($stats['revenue'], 2); ?> лв.</h3>
                        </div>
                        <div style="background: var(--surface); padding: 28px; border-radius: 20px; border-left: 4px solid var(--primary); box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                            <p style="color: var(--text-muted); font-size: 11px; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.1em; text-transform: uppercase;">Продадени билети</p>
                            <h3 style="font-size: 26px; margin: 0;"><?php echo $stats['tickets']; ?></h3>
                        </div>
                        <div style="background: var(--surface); padding: 28px; border-radius: 20px; border-left: 4px solid var(--primary); box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                            <p style="color: var(--text-muted); font-size: 11px; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.1em; text-transform: uppercase;">Активни филми</p>
                            <h3 style="font-size: 26px; margin: 0;"><?php echo $stats['movies']; ?></h3>
                        </div>
                        <div style="background: var(--surface); padding: 28px; border-radius: 20px; border-left: 4px solid var(--primary); box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                            <p style="color: var(--text-muted); font-size: 11px; font-weight: 800; margin-bottom: 6px; letter-spacing: 0.1em; text-transform: uppercase;">Общо резервации</p>
                            <h3 style="font-size: 26px; margin: 0;"><?php echo $stats['reservations']; ?></h3>
                        </div>
                    </div>

                    <!-- Quick recent reservations overview -->
                    <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
                        <h2 style="font-size: 20px; margin-bottom: 24px; font-weight: 800;">ПОСЛЕДНИ РЕЗЕРВАЦИИ</h2>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                        <th style="padding: 12px 16px;">UID</th>
                                        <th style="padding: 12px 16px;">КЛИЕНТ</th>
                                        <th style="padding: 12px 16px;">ФИЛМ</th>
                                        <th style="padding: 12px 16px;">ЗАЛА</th>
                                        <th style="padding: 12px 16px;">СУМА</th>
                                        <th style="padding: 12px 16px;">СТАТУС</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                    <?php 
                                    $recentRes = $bookingModel->getAll();
                                    $recentRes = array_slice($recentRes, 0, 6);
                                    foreach ($recentRes as $res): 
                                    ?>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 16px; font-weight: bold; color: var(--primary);"><?php echo $res['uid']; ?></td>
                                            <td style="padding: 16px;">
                                                <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($res['customer_name']); ?></p>
                                                <p style="margin: 0; font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($res['customer_email']); ?></p>
                                            </td>
                                            <td style="padding: 16px;"><?php echo htmlspecialchars($res['movie_title']); ?></td>
                                            <td style="padding: 16px; color: var(--text-muted);"><?php echo htmlspecialchars($res['hall_name']); ?></td>
                                            <td style="padding: 16px; font-weight: bold;"><?php echo number_format($res['total_price'], 2); ?> лв.</td>
                                            <td style="padding: 16px;">
                                                <?php if ($res['status'] === 'confirmed'): ?>
                                                    <span style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ПОТВЪРДЕНА</span>
                                                <?php elseif ($res['status'] === 'cancelled'): ?>
                                                    <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ОТКАЗАНА</span>
                                                <?php else: ?>
                                                    <span style="background: rgba(251, 191, 36, 0.1); color: #fbbf24; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ЧАКАЩА</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($recentRes)): ?>
                                        <tr><td colspan="6" style="padding: 32px; text-align: center; color: var(--text-muted);">Няма намерени резервации.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($tab === 'movies'): ?>
                    <!-- MOVIES TAB -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА ФИЛМИ</h2>
                            <a href="admin.php?tab=movies&action=add" class="btn btn-primary" style="padding: 10px 20px; font-size: 13px; border-radius: 10px;">ДОБАВИ ФИЛМ</a>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">ПОСТЕР</th>
                                            <th style="padding: 12px;">ЗАГЛАВИЕ</th>
                                            <th style="padding: 12px;">ЖАНР</th>
                                            <th style="padding: 12px;">ВРЕМЕТРАЕНЕ</th>
                                            <th style="padding: 12px;">СТАТУС</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allMovs = $movieModel->getAll(['status' => 'all']);
                                        foreach ($allMovs as $mov): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 12px;">
                                                    <img src="<?php echo htmlspecialchars($mov['poster_path']); ?>" alt="Poster" style="width: 45px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                                                </td>
                                                <td style="padding: 12px; font-weight: bold;">
                                                    <div><?php echo htmlspecialchars($mov['title']); ?></div>
                                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">Режисьор: <?php echo htmlspecialchars($mov['director']); ?></div>
                                                </td>
                                                <td style="padding: 12px;"><?php echo htmlspecialchars($mov['genre']); ?></td>
                                                <td style="padding: 12px;"><?php echo $mov['duration']; ?> мин.</td>
                                                <td style="padding: 12px;">
                                                    <?php if ($mov['status'] === 'now playing'): ?>
                                                        <span style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase;">СЕГА ПРОЖЕКТИРАН</span>
                                                    <?php elseif ($mov['status'] === 'coming soon'): ?>
                                                        <span style="background: rgba(251, 191, 36, 0.1); color: #fbbf24; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase;">ОЧАКВАЙТЕ</span>
                                                    <?php else: ?>
                                                        <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase;">АРХИВИРАН</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 12px; text-align: right; white-space: nowrap;">
                                                    <a href="admin.php?tab=movies&action=edit&id=<?php echo $mov['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Редакция
                                                    </a>
                                                    <a href="admin.php?tab=movies&action=delete&id=<?php echo $mov['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('Наистина ли искате да изтриете филма и всички негови прожекции?');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <?php 
                        $mov = [];
                        if ($action === 'edit' && $id) {
                            $mov = $movieModel->getById($id);
                        }
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;"><?php echo ($action === 'add') ? 'ДОБАВЯНЕ НА НОВ ФИЛМ' : 'РЕДАКЦИЯ НА ФИЛМ'; ?></h2>
                        
                        <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                <input type="hidden" name="save" value="1">
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Заглавие *</label>
                                        <input type="text" name="title" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($mov['title'] ?? ''); ?>" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Режисьор</label>
                                        <input type="text" name="director" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($mov['director'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Описание</label>
                                    <textarea name="description" class="search-input" style="width: 100%; min-height: 100px; background: var(--surface-light); resize: vertical; padding: 12px 16px;"><?php echo htmlspecialchars($mov['description'] ?? ''); ?></textarea>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Времетраене (минути) *</label>
                                        <input type="number" name="duration" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($mov['duration'] ?? ''); ?>" min="1" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Жанр</label>
                                        <input type="text" name="genre" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="напр. Екшън, Фантастика" value="<?php echo htmlspecialchars($mov['genre'] ?? ''); ?>">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Възрастова граница</label>
                                        <input type="text" name="rating" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="напр. 12+, 16+, B" value="<?php echo htmlspecialchars($mov['rating'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Премиерна дата *</label>
                                        <input type="date" name="release_date" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($mov['release_date'] ?? ''); ?>" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Рейтинг (Потребителски)</label>
                                        <input type="number" name="user_rating" class="search-input" style="width: 100%; background: var(--surface-light);" min="1" max="10" step="0.1" value="<?php echo htmlspecialchars($mov['user_rating'] ?? '8.5'); ?>">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Статус</label>
                                        <select name="status" class="search-input" style="width: 100%; background: var(--surface-light); height: 46px; padding: 0 16px;">
                                            <option value="now playing" <?php echo (isset($mov['status']) && $mov['status'] === 'now playing') ? 'selected' : ''; ?>>Now Playing (Прожектира се)</option>
                                            <option value="coming soon" <?php echo (isset($mov['status']) && $mov['status'] === 'coming soon') ? 'selected' : ''; ?>>Coming Soon (Очаквайте скоро)</option>
                                            <option value="archived" <?php echo (isset($mov['status']) && $mov['status'] === 'archived') ? 'selected' : ''; ?>>Archived (Архивиран)</option>
                                        </select>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Път към постер (Изображение)</label>
                                        <input type="text" name="poster_path" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="напр. public/assets/images/img_15.jpg" value="<?php echo htmlspecialchars($mov['poster_path'] ?? ''); ?>">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Трейлър URL (YouTube Embed)</label>
                                        <input type="text" name="trailer_url" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="напр. https://www.youtube.com/embed/..." value="<?php echo htmlspecialchars($mov['trailer_url'] ?? ''); ?>">
                                    </div>
                                </div>

                                <!-- Relational Actors Selector -->
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Изберете актьори и роля за филма</label>
                                    <div style="max-height: 250px; overflow-y: auto; background: var(--surface-light); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; gap: 12px;">
                                        <?php 
                                        $allActors = $actorModel->getAll();
                                        $movieActorLinks = [];
                                        if ($action === 'edit' && $id) {
                                            $movieActorLinks = $movieModel->getActorLinks($id);
                                        }
                                        foreach ($allActors as $act):
                                            $assigned = isset($movieActorLinks[$act['id']]);
                                            $charName = $assigned ? $movieActorLinks[$act['id']] : '';
                                        ?>
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid rgba(255,255,255,0.02); padding-bottom: 8px;">
                                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: bold; flex: 1;">
                                                    <input type="checkbox" name="actors[<?php echo $act['id']; ?>][id]" value="<?php echo $act['id']; ?>" <?php echo $assigned ? 'checked' : ''; ?> style="width: 16px; height: 16px; accent-color: var(--primary);">
                                                    <span><?php echo htmlspecialchars($act['name']); ?></span>
                                                </label>
                                                <input type="text" name="actors[<?php echo $act['id']; ?>][character]" value="<?php echo htmlspecialchars($charName); ?>" class="search-input" style="width: 220px; height: 32px; font-size: 12px; padding: 0 10px; background: var(--background);" placeholder="Роля / Герой">
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (empty($allActors)): ?>
                                            <p style="color: var(--text-muted); font-size: 12px; font-style: italic; margin: 0;">Няма налични актьори. Добавете актьори от таб „АКТЬОРИ“.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 16px; margin-top: 12px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                    <a href="admin.php?tab=movies" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'actors'): ?>
                    <!-- ACTORS TAB (NEW) -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА АКТЬОРИ</h2>
                            <a href="admin.php?tab=actors&action=add" class="btn btn-primary" style="padding: 10px 20px; font-size: 13px; border-radius: 10px;">ДОБАВИ АКТЬОР</a>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">СНИМКА</th>
                                            <th style="padding: 12px;">ИМЕ</th>
                                            <th style="padding: 12px;">ДАТА НА РАЖДАНЕ</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allActorsList = $actorModel->getAll();
                                        foreach ($allActorsList as $act): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 12px;">
                                                    <?php if (!empty($act['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($act['image_url']); ?>" alt="Actor" style="width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1);">
                                                    <?php else: ?>
                                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--surface-light); display: flex; align-items: center; justify-content: center;">
                                                            <span class="material-symbols-outlined" style="font-size: 20px; color: var(--text-muted);">person</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($act['name']); ?></td>
                                                <td style="padding: 12px; color: var(--text-secondary);">
                                                    <?php echo !empty($act['birth_date']) ? date('d.m.Y', strtotime($act['birth_date'])) : 'Няма данни'; ?>
                                                </td>
                                                <td style="padding: 12px; text-align: right; white-space: nowrap;">
                                                    <a href="admin.php?tab=actors&action=edit&id=<?php echo $act['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Редакция
                                                    </a>
                                                    <a href="admin.php?tab=actors&action=delete&id=<?php echo $act['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('Наистина ли искате да изтриете този актьор? Връзките с филми ще бъдат премахнати автоматично.');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($allActorsList)): ?>
                                            <tr><td colspan="4" style="padding: 32px; text-align: center; color: var(--text-muted);">Няма въведени актьори в системата.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <?php 
                        $act = [];
                        if ($action === 'edit' && $id) {
                            $act = $actorModel->getById($id);
                        }
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;"><?php echo ($action === 'add') ? 'ДОБАВЯНЕ НА НОВ АКТЬОР' : 'РЕДАКЦИЯ НА АКТЬОР'; ?></h2>
                        
                        <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                <input type="hidden" name="save" value="1">
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Име на актьор *</label>
                                        <input type="text" name="name" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($act['name'] ?? ''); ?>" placeholder="напр. Педро Паскал" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Дата на раждане</label>
                                        <input type="date" name="birth_date" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($act['birth_date'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">URL адрес на снимка (Изображение)</label>
                                    <input type="text" name="image_url" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="напр. https://m.media-amazon.com/images/M/...jpg" value="<?php echo htmlspecialchars($act['image_url'] ?? ''); ?>">
                                </div>

                                <div style="display: flex; gap: 16px; margin-top: 12px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                    <a href="admin.php?tab=actors" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'halls'): ?>
                    <!-- HALLS TAB -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА КИНО ЗАЛИ</h2>
                            <a href="admin.php?tab=halls&action=add" class="btn btn-primary" style="padding: 10px 20px; font-size: 13px; border-radius: 10px;">ДОБАВИ ЗАЛА</a>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">ID</th>
                                            <th style="padding: 12px;">ИМЕ НА ЗАЛА</th>
                                            <th style="padding: 12px;">КАПАЦИТЕТ</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allHalls = $hallModel->getAll();
                                        foreach ($allHalls as $hall): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 16px; font-weight: bold; color: var(--primary);"><?php echo $hall['id']; ?></td>
                                                <td style="padding: 16px; font-weight: bold;"><?php echo htmlspecialchars($hall['name']); ?></td>
                                                <td style="padding: 16px;"><?php echo $hall['capacity']; ?> места</td>
                                                <td style="padding: 16px; text-align: right;">
                                                    <a href="admin.php?tab=halls&action=edit&id=<?php echo $hall['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Редакция
                                                    </a>
                                                    <a href="admin.php?tab=halls&action=delete&id=<?php echo $hall['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('ВНИМАНИЕ: Изтриването на залата ще премахне автоматично всички места в нея и свързаните прожекции! Искате ли да продължите?');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <?php 
                        $hall = [];
                        if ($action === 'edit' && $id) {
                            $hall = $hallModel->getById($id);
                        }
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;"><?php echo ($action === 'add') ? 'ДОБАВЯНЕ НА НОВА КИНО ЗАЛА' : 'РЕДАКЦИЯ НА КИНО ЗАЛА'; ?></h2>
                        
                        <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                <input type="hidden" name="save" value="1">
                                
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Име на зала * (напр. Зала 4 • LUXE)</label>
                                    <input type="text" name="name" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($hall['name'] ?? ''); ?>" placeholder="Зала 4 • LUXE" required>
                                </div>

                                <?php if ($action === 'add'): ?>
                                    <div style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 16px; border: 1px dashed rgba(255,255,255,0.05);">
                                        <p style="color: var(--primary); font-size: 12px; font-weight: bold; margin: 0 0 16px 0; text-transform: uppercase; letter-spacing: 0.05em;">АВТОМАТИЧНО ГЕНЕРИРАНЕ НА МЕСТА (СХЕМА)</p>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Брой редове *</label>
                                                <input type="number" name="rows" class="search-input" style="width: 100%; background: var(--surface-light);" min="1" max="25" value="8" required>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Места на ред *</label>
                                                <input type="number" name="cols" class="search-input" style="width: 100%; background: var(--surface-light);" min="1" max="30" value="10" required>
                                            </div>
                                        </div>
                                        <p style="color: var(--text-muted); font-size: 11px; margin: 12px 0 0 0;">* Забележка: Местата на последния ред автоматично ще се маркират като VIP ложа с повишена цена, а останалите ще са стандартни.</p>
                                    </div>
                                <?php endif; ?>

                                <div style="display: flex; gap: 16px; margin-top: 12px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                    <a href="admin.php?tab=halls" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'showtimes'): ?>
                    <!-- SHOWTIMES TAB -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА ПРОЖЕКЦИИ</h2>
                            <a href="admin.php?tab=showtimes&action=add" class="btn btn-primary" style="padding: 10px 20px; font-size: 13px; border-radius: 10px;">ДОБАВИ ПРОЖЕКЦИЯ</a>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">ФИЛМ</th>
                                            <th style="padding: 12px;">ЗАЛА</th>
                                            <th style="padding: 12px;">ДАТА & ЧАС</th>
                                            <th style="padding: 12px;">БАЗОВА ЦЕНА</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allST = $showtimeModel->getAll();
                                        foreach ($allST as $st): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 16px; font-weight: bold;"><?php echo htmlspecialchars($st['movie_title']); ?></td>
                                                <td style="padding: 16px; color: var(--primary); font-weight: bold;"><?php echo htmlspecialchars($st['hall_name']); ?></td>
                                                <td style="padding: 16px;">
                                                    <span style="font-weight: bold;"><?php echo date('d.m.Y', strtotime($st['start_time'])); ?></span>
                                                    <span style="color: var(--text-muted); margin-left: 8px;"><?php echo date('H:i', strtotime($st['start_time'])); ?> ч.</span>
                                                </td>
                                                <td style="padding: 16px; font-weight: bold;"><?php echo number_format($st['base_price'], 2); ?> лв.</td>
                                                <td style="padding: 16px; text-align: right;">
                                                    <a href="admin.php?tab=showtimes&action=edit&id=<?php echo $st['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Редакция
                                                    </a>
                                                    <a href="admin.php?tab=showtimes&action=delete&id=<?php echo $st['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('ВНИМАНИЕ: Изтриването на прожекцията ще премахне автоматично и всички резервации за нея! Желаете ли да изтриете?');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <?php 
                        $st = [];
                        if ($action === 'edit' && $id) {
                            $st = $showtimeModel->getById($id);
                        }
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;"><?php echo ($action === 'add') ? 'ДОБАВЯНЕ НА НОВА ПРОЖЕКЦИЯ' : 'РЕДАКЦИЯ НА ПРОЖЕКЦИЯ'; ?></h2>
                        
                        <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                <input type="hidden" name="save" value="1">
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Изберете Филм *</label>
                                        <select name="movie_id" class="search-input" style="width: 100%; background: var(--surface-light); height: 46px; padding: 0 16px;" required>
                                            <option value="">-- Изберете филм --</option>
                                            <?php 
                                            $allMovsList = $movieModel->getAll(['status' => 'all']);
                                            foreach ($allMovsList as $mov): 
                                            ?>
                                                <option value="<?php echo $mov['id']; ?>" <?php echo (isset($st['movie_id']) && $st['movie_id'] == $mov['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($mov['title']); ?> (<?php echo $mov['duration']; ?> мин.)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Изберете Зала *</label>
                                        <select name="hall_id" class="search-input" style="width: 100%; background: var(--surface-light); height: 46px; padding: 0 16px;" required>
                                            <option value="">-- Изберете кино зала --</option>
                                            <?php 
                                            $allHallsList = $hallModel->getAll();
                                            foreach ($allHallsList as $hall): 
                                            ?>
                                                <option value="<?php echo $hall['id']; ?>" <?php echo (isset($st['hall_id']) && $st['hall_id'] == $hall['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($hall['name']); ?> (<?php echo $hall['capacity']; ?> места)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Начален час & Дата *</label>
                                        <?php 
                                        $formattedDateTime = '';
                                        if (!empty($st['start_time'])) {
                                            $formattedDateTime = date('Y-m-d\TH:i', strtotime($st['start_time']));
                                        }
                                        ?>
                                        <input type="datetime-local" name="start_time" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo $formattedDateTime; ?>" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Базова цена на билет (лв.) *</label>
                                        <input type="number" name="base_price" class="search-input" style="width: 100%; background: var(--surface-light);" min="0.01" step="0.01" value="<?php echo htmlspecialchars($st['base_price'] ?? '12.00'); ?>" required>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 16px; margin-top: 12px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                    <a href="admin.php?tab=showtimes" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'promo_codes'): ?>
                    <!-- PROMO CODES TAB -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА ПРОМО КОДОВЕ</h2>
                            <a href="admin.php?tab=promo_codes&action=add" class="btn btn-primary" style="padding: 10px 20px; font-size: 13px; border-radius: 10px;">ДОБАВИ КОД</a>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">КОД</th>
                                            <th style="padding: 12px;">ОТСТЪПКА (%)</th>
                                            <th style="padding: 12px;">ВАЛИДНОСТ ДО</th>
                                            <th style="padding: 12px;">АКТИВЕН</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allPromos = $promoModel->getAll();
                                        foreach ($allPromos as $promo): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 16px; font-weight: bold; color: var(--primary); font-size: 16px;"><?php echo htmlspecialchars($promo['code']); ?></td>
                                                <td style="padding: 16px; font-weight: bold;"><?php echo $promo['discount_percent']; ?>%</td>
                                                <td style="padding: 16px; color: var(--text-muted);">
                                                    <?php echo !empty($promo['valid_until']) ? date('d.m.Y H:i', strtotime($promo['valid_until'])) : 'Безсрочен'; ?>
                                                </td>
                                                <td style="padding: 16px;">
                                                    <?php if ($promo['is_active']): ?>
                                                        <span style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ДА</span>
                                                    <?php else: ?>
                                                        <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">НЕ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 16px; text-align: right;">
                                                    <a href="admin.php?tab=promo_codes&action=edit&id=<?php echo $promo['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">edit</span> Редакция
                                                    </a>
                                                    <a href="admin.php?tab=promo_codes&action=delete&id=<?php echo $promo['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('Наистина ли искате да изтриете този промо код?');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'add' || $action === 'edit'): ?>
                        <?php 
                        $promo = [];
                        if ($action === 'edit' && $id) {
                            $promo = $promoModel->getById($id);
                        }
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;"><?php echo ($action === 'add') ? 'ДОБАВЯНЕ НА ПРОМО КОД' : 'РЕДАКЦИЯ НА ПРОМО КОД'; ?></h2>
                        
                        <div style="background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                <input type="hidden" name="save" value="1">
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Промо код * (напр. SUPERDEAL)</label>
                                        <input type="text" name="code" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($promo['code'] ?? ''); ?>" placeholder="SUPERDEAL" required>
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Процент отстъпка (1-100) *</label>
                                        <input type="number" name="discount_percent" class="search-input" style="width: 100%; background: var(--surface-light);" min="1" max="100" value="<?php echo htmlspecialchars($promo['discount_percent'] ?? '15'); ?>" required>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Валиден до (DateTime - Незадължително)</label>
                                        <?php 
                                        $formattedValidUntil = '';
                                        if (!empty($promo['valid_until'])) {
                                            $formattedValidUntil = date('Y-m-d\TH:i', strtotime($promo['valid_until']));
                                        }
                                        ?>
                                        <input type="datetime-local" name="valid_until" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo $formattedValidUntil; ?>">
                                    </div>
                                    <div style="display: flex; align-items: center; padding-top: 32px;">
                                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 14px; font-weight: bold;">
                                            <input type="checkbox" name="is_active" value="1" <?php echo (!isset($promo['is_active']) || $promo['is_active']) ? 'checked' : ''; ?> style="width: 20px; height: 20px; accent-color: var(--primary);">
                                            <span>Промо кодът е активен веднага</span>
                                        </label>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 16px; margin-top: 12px;">
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                    <a href="admin.php?tab=promo_codes" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                <?php elseif ($tab === 'reservations'): ?>
                    <!-- RESERVATIONS TAB -->
                    <?php if ($action === 'list'): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 24px; font-weight: 800; margin: 0;">УПРАВЛЕНИЕ НА РЕЗЕРВАЦИИ</h2>
                        </div>

                        <div style="background: var(--surface); padding: 24px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: 12px; font-weight: 800;">
                                            <th style="padding: 12px;">UID</th>
                                            <th style="padding: 12px;">КЛИЕНТ</th>
                                            <th style="padding: 12px;">ПРОЖЕКЦИЯ</th>
                                            <th style="padding: 12px;">СУМА</th>
                                            <th style="padding: 12px;">СТАТУС</th>
                                            <th style="padding: 12px; text-align: right;">ДЕЙСТВИЯ</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?php 
                                        $allRes = $bookingModel->getAll();
                                        foreach ($allRes as $res): 
                                        ?>
                                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                                <td style="padding: 16px; font-weight: bold; color: var(--primary);"><?php echo $res['uid']; ?></td>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($res['customer_name']); ?></p>
                                                    <p style="margin: 0; font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($res['customer_email']); ?></p>
                                                </td>
                                                <td style="padding: 16px;">
                                                    <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($res['movie_title']); ?></p>
                                                    <p style="margin: 0; font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($res['hall_name']); ?> • <?php echo date('d.m.Y H:i', strtotime($res['start_time'])); ?> ч.</p>
                                                </td>
                                                <td style="padding: 16px; font-weight: bold;"><?php echo number_format($res['total_price'], 2); ?> лв.</td>
                                                <td style="padding: 16px;">
                                                    <?php if ($res['status'] === 'confirmed'): ?>
                                                        <span style="background: rgba(74, 222, 128, 0.1); color: #4ade80; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ПОТВЪРДЕНА</span>
                                                    <?php elseif ($res['status'] === 'cancelled'): ?>
                                                        <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ОТКАЗАНА</span>
                                                    <?php else: ?>
                                                        <span style="background: rgba(251, 191, 36, 0.1); color: #fbbf24; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: bold;">ЧАКАЩА</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="padding: 16px; text-align: right; white-space: nowrap;">
                                                    <a href="admin.php?tab=reservations&action=edit&id=<?php echo $res['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; margin-right: 8px;">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">visibility</span> Преглед & Редакция
                                                    </a>
                                                    <a href="admin.php?tab=reservations&action=delete&id=<?php echo $res['id']; ?>" class="btn btn-outline" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; border-color: rgba(239, 68, 68, 0.3); color: #ef4444; display: inline-flex; align-items: center; gap: 4px;" onclick="return confirm('ВНИМАНИЕ: Изтриването на резервацията ще освободи заетите места! Желаете ли да изтриете?');">
                                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span> Изтрий
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php elseif ($action === 'edit' && $id): ?>
                        <?php 
                        $res = $bookingModel->getById($id);
                        $resSeats = $bookingModel->getSeatsForReservation($id);
                        ?>
                        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">ДЕТАЙЛИ НА РЕЗЕРВАЦИЯ: <?php echo htmlspecialchars($res['uid']); ?></h2>
                        
                        <div style="display: flex; gap: 32px; flex-wrap: wrap; align-items: flex-start;">
                            <!-- Form Details Card -->
                            <div style="flex: 2; min-width: 320px; background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                                <form method="POST" style="display: flex; flex-direction: column; gap: 24px;">
                                    <input type="hidden" name="save" value="1">
                                    
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                                        <div>
                                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Име на клиент *</label>
                                            <input type="text" name="customer_name" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($res['customer_name']); ?>" required>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Имейл адрес *</label>
                                            <input type="email" name="customer_email" class="search-input" style="width: 100%; background: var(--surface-light);" value="<?php echo htmlspecialchars($res['customer_email']); ?>" required>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px;">
                                        <div>
                                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Обща сума (лв.) *</label>
                                            <input type="number" name="total_price" class="search-input" style="width: 100%; background: var(--surface-light);" min="0" step="0.01" value="<?php echo htmlspecialchars($res['total_price']); ?>" required>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Метод на плащане</label>
                                            <select name="payment_method" class="search-input" style="width: 100%; background: var(--surface-light); height: 46px; padding: 0 16px;">
                                                <option value="cash" <?php echo ($res['payment_method'] === 'cash') ? 'selected' : ''; ?>>В брой / На каса</option>
                                                <option value="card" <?php echo ($res['payment_method'] === 'card') ? 'selected' : ''; ?>>С дебитна/кредитна карта</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em;">Статус на плащане</label>
                                            <select name="status" class="search-input" style="width: 100%; background: var(--surface-light); height: 46px; padding: 0 16px;">
                                                <option value="confirmed" <?php echo ($res['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed (Потвърдена)</option>
                                                <option value="cancelled" <?php echo ($res['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled (Отказвана/Свободна)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
                                        <p style="color: var(--text-muted); font-size: 11px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.05em;">ИНФОРМАЦИЯ ЗА ПРОЖЕКЦИЯТА</p>
                                        <p style="margin: 0; font-size: 16px; font-weight: bold;"><?php echo htmlspecialchars($res['movie_title']); ?></p>
                                        <p style="margin: 6px 0 0 0; font-size: 13px; color: var(--primary); font-weight: bold;"><?php echo htmlspecialchars($res['hall_name']); ?> • <?php echo date('d.m.Y H:i', strtotime($res['start_time'])); ?> ч.</p>
                                    </div>

                                    <div style="display: flex; gap: 16px; margin-top: 12px;">
                                        <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">ЗАПАЗИ</button>
                                        <a href="admin.php?tab=reservations" class="btn btn-outline" style="padding: 12px 32px;">ОТКАЗ</a>
                                    </div>
                                </form>
                            </div>

                            <!-- Reserved Seats Card -->
                            <div style="flex: 1; min-width: 250px; background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 20px; color: var(--primary);">РЕЗЕРВИРАНИ МЕСТА (<?php echo count($resSeats); ?>)</h3>
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    <?php foreach ($resSeats as $seat): ?>
                                        <div style="background: var(--surface-light); padding: 16px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid rgba(255,255,255,0.03);">
                                            <div>
                                                <p style="margin: 0; font-size: 13px; font-weight: 800;">Ред <?php echo $seat['row_num']; ?>, Място <?php echo $seat['seat_num']; ?></p>
                                                <p style="margin: 2px 0 0 0; font-size: 11px; color: var(--text-muted); text-transform: uppercase;"><?php echo ($seat['seat_type'] === 'vip') ? 'VIP Ложа' : 'Стандартно'; ?> • <?php echo htmlspecialchars($seat['ticket_type']); ?></p>
                                            </div>
                                            <span style="font-weight: bold; font-size: 13px; color: #4ade80;"><?php echo number_format($seat['price'], 2); ?> лв.</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include 'src/templates/footer.php'; ?>
