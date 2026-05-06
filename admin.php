<?php include 'src/templates/header.php'; ?>

<main class="container" style="padding-top: 140px; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <?php
    $isLoggedIn = false;
    if (!$isLoggedIn):
    ?>
    <div style="background: var(--surface); padding: 48px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.05); width: 100%; max-width: 450px; box-shadow: 0 40px 80px rgba(0,0,0,0.5);">
        <h2 class="hero-title" style="font-size: 32px; margin-bottom: 8px; text-align: center;">АДМИН ПАНЕЛ</h2>
        <p style="color: var(--text-secondary); text-align: center; margin-bottom: 40px;">Влезте в своя акаунт</p>
        
        <form style="display: flex; flex-direction: column; gap: 24px;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Потребителско име</label>
                <input type="text" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="admin">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Парола</label>
                <input type="password" class="search-input" style="width: 100%; background: var(--surface-light);" placeholder="••••••••">
            </div>
            <button class="btn btn-primary" style="width: 100%; margin-top: 12px;">ВХОД</button>
        </form>
    </div>
    <?php else: ?>
    <div style="width: 100%;">
        <header style="margin-bottom: 48px; display: flex; justify-content: space-between; align-items: center;">
            <h1 class="hero-title" style="font-size: 40px;">СТАТИСТИКА</h1>
            <button class="btn btn-outline">ИЗХОД</button>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; margin-bottom: 64px;">
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">ДНЕВЕН ПРИХОД</p>
                <h3 style="font-size: 28px;">2,450.00 лв.</h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">ПРОДАДЕНИ БИЛЕТИ</p>
                <h3 style="font-size: 28px;">154</h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">АКТИВНИ ФИЛМИ</p>
                <h3 style="font-size: 28px;">12</h3>
            </div>
            <div style="background: var(--surface); padding: 32px; border-radius: 20px; border-left: 4px solid var(--primary);">
                <p style="color: var(--text-muted); font-size: 12px; font-weight: 800; margin-bottom: 8px;">НОВИ ПОТРЕБИТЕЛИ</p>
                <h3 style="font-size: 28px;">42</h3>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php include 'src/templates/footer.php'; ?>
