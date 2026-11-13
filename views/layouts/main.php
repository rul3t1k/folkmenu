<?php use yii\helpers\Url; 

$isGuest = Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->role == 1;
$route = Yii::$app->controller->route;
$adminActive = $isAdmin && (strpos($route, 'admin') === 0 || strpos($route, 'user/') === 0 || strpos($route, 'recipe/') === 0 || strpos($route, 'favorite/') === 0);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Кулинарная книга — Семейные рецепты и архив</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600;1,700&amp;family=Inter:wght@300;400;500;600;700&amp;family=Caveat:wght@600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= Url::to(['css/style.css']) ?>">
    <link rel="stylesheet" href="<?= Url::to(['css/extra.css']) ?>">
</head>
<body class="bg-paper-grain font-sans text-warm-ink antialiased min-h-screen flex flex-col selection:bg-terracotta/20 selection:text-terracotta">

<!-- TOP NAVIGATION HEADER (Главная страница) -->
<header class="fixed top-0 left-0 right-0 z-50 bg-[#FAF4EB] border-b border-dashed border-[#dec0b7] shadow-[0_2px_15px_rgba(47,35,26,0.05)]">
    <div class="max-w-7xl mx-auto h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
        
        <!-- Логотип -->
        <a class="flex items-center gap-3.5 group shrink-0" href="<?= Url::to(['index']) ?>">
            <div class="relative w-11 h-11 bg-white border border-[#dec0b7] flex items-center justify-center text-terracotta shadow-xs transition-transform group-hover:scale-105 duration-200 rounded-xl">
                <span class="material-symbols-outlined text-2xl">menu_book</span>
                <div class="washi-tape washi-terracotta -top-1 -right-1 w-4 h-2"></div>
            </div>
            <span class="font-serif text-2xl sm:text-3xl font-bold tracking-tight text-warm-ink leading-none group-hover:text-terracotta transition-colors">
                Folk Menu
            </span>
        </a>

        <!-- Навигация: Главная активна -->
        <nav class="hidden md:flex items-center gap-1.5 lg:gap-2 text-sm font-sans">
            <a class="<?= Yii::$app->controller->route == 'site/index' ? 'px-3 py-1.5 text-sm font-sans font-bold text-terracotta transition-colors rounded-lg' : 'px-3.5 py-1.5 text-sm font-sans font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-lg' ?>" href="<?= Url::to(['index']) ?>">Главная</a>
            <a class="<?= Yii::$app->controller->route == 'site/recipes' ? 'px-3 py-1.5 text-sm font-sans font-bold text-terracotta transition-colors rounded-lg' : 'px-3.5 py-1.5 text-sm font-sans font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-lg' ?>" href="<?= Url::to(['recipes']) ?>">Рецепты</a>
            <a class="<?= Yii::$app->controller->route == 'site/ingredients-search' ? 'px-3 py-1.5 text-sm font-sans font-bold text-terracotta transition-colors rounded-lg' : 'px-3.5 py-1.5 text-sm font-sans font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-lg' ?>" href="<?= Url::to(['ingredients-search']) ?>">Поиск по ингредиентам</a>
            <?php if ($isAdmin): ?>
            <a href="<?= Url::to(['/admin']) ?>" class="<?= $adminActive ? 'px-3 py-1.5 text-sm font-sans font-bold text-terracotta transition-colors rounded-lg' : 'px-3.5 py-1.5 text-sm font-sans font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-lg' ?>">Админ панель</a>
            <?php endif; ?>
        </nav>

        <!-- Правый блок -->
        <div class="flex items-center gap-3">
           <div class="flex items-center gap-2">
    <!-- Список покупок -->
    <a aria-label="Список покупок" class="relative p-2 bg-white hover:bg-[#FFF8F0] border border-[#dec0b7] text-warm-ink hover:text-terracotta shadow-xs transition-colors rounded-xl" href="<?= Url::to(['shopping-list']) ?>">
        <span class="material-symbols-outlined text-lg leading-none">shopping_bag</span>
    </a>
    
    <!-- Профиль: гость -> вход, авторизованный -> профиль -->
    <a aria-label="<?= Yii::$app->user->isGuest ? 'Войти' : 'Профиль' ?>" class="p-2 bg-white hover:bg-[#FFF8F0] border border-[#dec0b7] text-warm-ink hover:text-terracotta shadow-xs transition-colors rounded-xl" href="<?= Url::to([Yii::$app->user->isGuest ? 'site/login' : 'site/profile']) ?>">
        <span class="material-symbols-outlined text-lg leading-none">person</span>
    </a>
</div>

            <button id="mobile-menu-btn" aria-label="Открыть меню" class="md:hidden p-2 bg-white hover:bg-[#FFF8F0] border border-[#dec0b7] text-warm-ink rounded-xl shadow-xs transition-colors">
                <span class="material-symbols-outlined text-xl leading-none" id="menu-icon">menu</span>
            </button>
        </div>
    </div>
</header>

<!-- МОБИЛЬНАЯ ВЫЕЗЖАЮЩАЯ ПАНЕЛЬ (БУРГЕР-МЕНЮ) -->
<div id="mobile-menu" class="fixed inset-x-0 top-20 bg-[#FAF4EB] border-b border-dashed border-[#dec0b7] shadow-xl p-6 transform -translate-y-full opacity-0 pointer-events-none transition-all duration-300 z-40 md:hidden flex flex-col gap-4">
    <div class="relative w-full flex items-center bg-[#FFF8F0] border border-[#dec0b7] shadow-xs px-3.5 py-2 gap-2 rounded-xl">
        <input class="w-full bg-transparent border-0 p-0 text-sm text-warm-ink placeholder-[#8a726a] focus:ring-0 font-sans" placeholder="Поиск по рецептам..." type="text">
        <span class="material-symbols-outlined text-base text-terracotta">search</span>
    </div>
    <nav class="flex flex-col gap-2 font-sans pt-2 border-t border-[#dec0b7]/50">
        <a class="px-4 py-2.5 text-base font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-xl" href="<?= Url::to(['index']) ?>">Главная</a>
        <a class="px-4 py-2.5 text-base font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-xl" href="<?= Url::to(['recipes']) ?>">Рецепты</a>
        <a class="px-4 py-2.5 text-base font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-xl" href="<?= Url::to(['ingredients-search']) ?>">Поиск по ингредиентам</a>
        <?php if ($isAdmin): ?>
        <a class="px-4 py-2.5 text-base font-semibold text-warm-ink hover:text-terracotta hover:bg-canvas-alt transition-colors rounded-xl" href="<?= Url::to(['/admin']) ?>">Админ панель</a>
        <?php endif; ?>
    </nav>
</div>
        <?= $content ?>

<?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
    <div class="flash-toast fixed bottom-6 right-6 z-50 bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm rounded-2xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-terracotta text-2xl shrink-0"><?= $type === 'error' ? 'error' : 'verified' ?></span>
            <div class="flex flex-col">
                <span class="font-serif font-bold text-base text-warm-ink">Folk Menu</span>
                <span class="font-sans text-xs text-warm-muted mt-0.5"><?= \yii\helpers\Html::encode(is_array($message) ? implode(' ', $message) : $message) ?></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script>setTimeout(function(){document.querySelectorAll('.flash-toast').forEach(function(e){e.remove();});}, 4000);</script>
<!-- ================= FOOTER ================= -->
<footer class="w-full bg-[#FAF4EB] border-t border-craft-border mt-auto py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex flex-col items-center md:items-start text-center md:text-left">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-terracotta text-2xl">menu_book</span>
                <span class="font-serif text-2xl font-bold text-warm-ink">Folk Menu</span>
            </div>
            <p class="text-xs text-warm-muted mt-1 max-w-md">Домашний архив рецептов, гастрономические традиции и кулинарное вдохновение на каждый день.</p>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-warm-muted font-medium">
            <a class="hover:text-terracotta transition-colors" href="<?= Url::to(['index']) ?>">Главная</a>
            <a class="hover:text-terracotta transition-colors" href="<?= Url::to(['recipes']) ?>">Рецепты</a>
            <a class="hover:text-terracotta transition-colors" href="<?= Url::to(['ingredients-search']) ?>">Поиск по ингредиентам</a>
        </div>
        <div class="text-xs text-warm-muted/80 text-center md:text-right">Folk Menu © 2026. Все права защищены.</div>
    </div>
</footer>

<script src="<?= Url::to(['js/script.js']) ?>"></script>
</body>
</html>
