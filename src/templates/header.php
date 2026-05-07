<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>CINEMA NOIR - Премиум Кино Изживяване</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=DM+Serif+Display&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container flex items-center justify-between w-full">
            <a href="index.php" class="logo-link">
                <img src="public/assets/images/logo.svg" alt="CINEMA NOIR" class="logo-img">
            </a>
            
            <div class="nav-links" id="nav-links">
                <a class="nav-link" href="index.php">Начало</a>
                <a class="nav-link" href="archive.php">Филми</a>
                <a class="nav-link" href="program.php">Програма</a>
            </div>
            
            <div class="nav-actions">
                <form action="archive.php" method="GET" class="search-box">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input name="search" class="search-input" placeholder="Търсене на филм..." type="text" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"/>
                </form>
                <button class="burger-menu" id="burger-menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
    </nav>

    <div class="mobile-menu" id="mobile-menu">
        <a class="nav-link text-xl font-black text-white" href="index.php">Начало</a>
        <a class="nav-link text-xl font-black text-white" href="archive.php">Филми</a>
        <a class="nav-link text-xl font-black text-white" href="program.php">Програма</a>
    </div>

    <script>
        document.getElementById('burger-menu').addEventListener('click', () => {
            document.getElementById('mobile-menu').classList.toggle('active');
        });
    </script>
