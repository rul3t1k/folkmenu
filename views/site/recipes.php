<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\Recipe[] $recipes */
/** @var app\models\Category[] $categories */
/** @var int[] $favCounts */
/** @var int[] $myFavorites */

$total = count($recipes);
$plural = function ($n, $one, $few, $many) {
    $a = $n % 100; $b = $n % 10;
    if ($a > 10 && $a < 20) return $many;
    if ($b > 1 && $b < 5) return $few;
    if ($b == 1) return $one;
    return $many;
};
$fmtTime = function ($min) {
    if ($min <= 0) return null;
    if ($min < 60) return $min . ' мин';
    $h = intdiv($min, 60); $m = $min % 60;
    return $h . ' ч' . ($m ? ' ' . $m . ' мин' : '');
};
$photoUrl = function ($photo) {
    if (!$photo) return null;
    if (preg_match('~^(https?:)?//|^/~', $photo)) return $photo;
    $path = (strpos($photo, '/') === false ? 'images/' : '') . $photo;
    return Yii::getAlias('@web') . '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
};
$diffKey = ['Простая' => 'simple', 'Средняя' => 'medium', 'Для терпеливых' => 'patient'];
?>
<!-- ================= MAIN CONTENT ================= -->
<main class="w-full pt-28 pb-16 flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8">

        <!-- Баннер страницы -->
        <div class="relative bg-[#FAF4EB] border-2 border-[#D4B996] p-6 sm:p-8 shadow-scrapbook rounded-3xl">
            <div class="washi-tape washi-terracotta -top-3 left-10 w-32 h-6 -rotate-2"></div>
            <div class="washi-tape washi-honey -top-2 right-12 w-24 h-5 rotate-3"></div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                <div class="flex flex-col gap-2 max-w-2xl">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-terracotta-light text-terracotta font-mono text-xs uppercase tracking-wider font-bold self-start border border-terracotta/20 rounded-md">
                        <span class="material-symbols-outlined text-sm">menu_book</span>
                        Кулинарный архив
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-warm-ink font-serif">Все рецепты</h1>
                    <p class="font-sans text-xs sm:text-sm text-warm-muted leading-relaxed">
                        Выбирайте блюдо по категории, сложности и времени приготовления или найдите рецепт по названию.
                    </p>
                </div>

                <div class="bg-white px-5 py-4 border border-[#D4B996] rounded-2xl shadow-xs flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 bg-terracotta/10 text-terracotta flex items-center justify-center rounded-xl">
                        <span class="material-symbols-outlined text-2xl">restaurant_menu</span>
                    </div>
                    <div>
                        <span class="block font-mono text-[11px] text-warm-muted uppercase tracking-wider">В каталоге</span>
                        <span class="font-mono text-lg font-bold text-terracotta" id="recipes-total"><?= $total ?> <?= $plural($total, 'рецепт', 'рецепта', 'рецептов') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="relative bg-white border border-[#D4B996] shadow-scrapbook p-6 sm:p-8 rounded-3xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-dashed border-[#D4B996] pb-4 mb-6">
                <div>
                    <span class="font-mono text-[11px] text-warm-muted uppercase tracking-widest">Подбор блюда</span>
                    <h2 class="text-2xl text-warm-ink font-serif font-bold">Фильтры</h2>
                </div>
                <button class="px-4 py-2 bg-white hover:bg-paper text-terracotta border border-[#D4B996] font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer self-start sm:self-auto" id="filters-reset" type="button">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    <span>Сбросить фильтры</span>
                </button>
            </div>

            <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end" id="recipes-filters" onsubmit="return false">
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="filter-search">Название</label>
                    <input autocomplete="off" class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors" id="filter-search" placeholder="Например: пирог" type="text">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="filter-category">Категория</label>
                    <select class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors" id="filter-category">
                        <option value="all">Все категории</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= Html::encode($c->slug) ?>"><?= Html::encode($c->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="filter-difficulty">Сложность</label>
                    <select class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors" id="filter-difficulty"><option value="all">Любая</option><option value="simple">Простая</option><option value="medium">Средняя</option><option value="patient">Для терпеливых</option></select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="filter-time">Время</label>
                    <select class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors" id="filter-time"><option value="all">Любое</option><option value="30">До 30 минут</option><option value="45">До 45 минут</option><option value="60">До 60 минут</option><option value="120">До 2 часов</option></select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="filter-sort">Сортировка</label>
                    <select class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors" id="filter-sort"><option value="new">Сначала новые</option><option value="popular">По популярности</option><option value="time">По времени</option><option value="title">По названию</option></select>
                </div>
            </form>
        </div>

        <!-- Результаты -->
        <div class="flex flex-col gap-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2 border-b border-dashed border-[#D4B996]">
                <div>
                    <span class="font-mono text-[11px] text-warm-muted uppercase tracking-widest">Каталог</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-warm-ink font-serif" id="recipes-count">Найдено рецептов: <?= $total ?></h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="recipes-grid">
            <?php foreach ($recipes as $r):
                $img = $photoUrl($r->photo);
                $minutes = (int)$r->prep_time_min + (int)$r->cook_time_min;
                $timeText = $fmtTime($minutes);
                $servings = (int)($r->servings ?: 4);
                $isFav = in_array((int)$r->id, $myFavorites, true);
            ?>
            <article class="recipe-card bg-white border border-craft-border rounded-2xl p-4 shadow-sm hover:shadow-card-lift transition-all duration-300 flex flex-col justify-between group"
                     data-title="<?= Html::encode($r->title) ?>"
                     data-category="<?= Html::encode($r->category ? $r->category->slug : '') ?>"
                     data-difficulty="<?= Html::encode($diffKey[$r->difficulty] ?? '') ?>"
                     data-time="<?= $minutes > 0 ? $minutes : '' ?>"
                     data-created="<?= $r->created_at ? (int)strtotime($r->created_at) : 0 ?>"
                     data-favs="<?= (int)($favCounts[(int)$r->id] ?? 0) ?>">
                <div>
                    <div class="relative w-full h-56 rounded-xl overflow-hidden bg-paper border border-craft-subtle flex items-center justify-center">
                        <?php if ($img): ?>
                            <img alt="<?= Html::encode($r->title) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= Html::encode($img) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-[#dec0b7]">restaurant_menu</span>
                        <?php endif; ?>
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 text-warm-ink text-[11px] font-bold shadow-xs"><?= Html::encode($r->category ? $r->category->name : 'Рецепт') ?></span>
                        <button aria-label="В избранное" class="rc-fav absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 hover:bg-white <?= $isFav ? 'text-terracotta' : 'text-warm-muted' ?> flex items-center justify-center shadow-xs transition-transform active:scale-90" data-id="<?= (int)$r->id ?>" data-fav="<?= $isFav ? 1 : 0 ?>" type="button">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' <?= $isFav ? 1 : 0 ?>;">favorite</span>
                        </button>
                    </div>
                    <div class="pt-4">
                        <div class="flex items-center justify-between text-xs text-warm-muted mb-2">
                            <div class="flex items-center gap-2">
                                <?php if ($timeText): ?>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> <?= $timeText ?></span>
                                    <span>•</span>
                                <?php endif; ?>
                                <span><?= $servings ?> <?= $plural($servings, 'порция', 'порции', 'порций') ?></span>
                            </div>
                        </div>
                        <h3 class="font-serif text-2xl font-bold text-warm-ink leading-snug group-hover:text-terracotta transition-colors line-clamp-2">
                            <?= Html::encode($r->title) ?>
                        </h3>
                        <?php if (trim((string)$r->description) !== ''): ?>
                        <p class="text-sm text-warm-muted mt-2 line-clamp-2 leading-relaxed">
                            <?= Html::encode($r->description) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-craft-subtle flex items-center justify-between">
                    <div class="text-xs">
                        <span class="text-warm-muted block">Сложность:</span>
                        <span class="font-semibold text-terracotta"><?= Html::encode($r->difficulty) ?></span>
                    </div>
                    <a class="px-4 py-2 bg-terracotta hover:bg-terracotta-hover text-white text-xs font-bold rounded-lg shadow-xs transition-colors flex items-center gap-1.5" href="<?= Url::to(['site/recipe', 'id' => $r->id]) ?>">
                        <span>Открыть рецепт</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
            </div>

            <div class="hidden relative text-center bg-white border-2 border-dashed border-[#D4B996] rounded-3xl p-8 shadow-scrapbook" id="recipes-empty">
                <span class="material-symbols-outlined text-4xl text-terracotta">search_off</span>
                <h3 class="font-serif text-2xl font-bold text-warm-ink mt-2">Ничего не найдено</h3>
                <p class="font-sans text-sm text-warm-muted mt-1">Попробуйте изменить фильтры или сбросить их.</p>
                <button class="mt-6 px-4 py-2 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-colors rounded-xl shadow-xs inline-flex items-center gap-1.5 cursor-pointer" id="empty-reset" type="button">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    <span>Сбросить фильтры</span>
                </button>
            </div>
        </div>

    </div>
</main>
<script>
(function () {
    var guest = <?= Yii::$app->user->isGuest ? 'true' : 'false' ?>;
    var loginUrl = <?= json_encode(Url::to(['site/login'])) ?>;
    var favUrl = <?= json_encode(Url::to(['site/toggle-favorite'])) ?>;
    var csrf = <?= json_encode(Yii::$app->request->csrfToken) ?>;
    function paint(btn, on) {
        btn.dataset.fav = on ? 1 : 0;
        btn.classList.toggle('text-terracotta', on);
        btn.classList.toggle('text-warm-muted', !on);
        btn.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' " + (on ? 1 : 0);
    }
    document.querySelectorAll('.rc-fav').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (guest) { window.location.href = loginUrl; return; }
            var body = new URLSearchParams({ _csrf: csrf });
            fetch(favUrl + '?id=' + btn.dataset.id, { method: 'POST', body: body, headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                .then(function (r) { paint(btn, !!r.favorite); })
                .catch(function () { alert('Не удалось изменить избранное'); });
        });
    });
})();
</script>
