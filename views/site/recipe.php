<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\Recipe $recipe */
/** @var bool $isOwner */
/** @var bool $isFavorite */

$assetUrl = function ($path) {
    if (!$path) {
        return null;
    }
    if (preg_match('~^(https?:)?//|^/~', $path)) {
        return $path;
    }
    $path = (strpos($path, '/') === false ? 'images/' : '') . $path;
    return Yii::getAlias('@web') . '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
};
$num = function ($v) {
    return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
};
$mins = function ($v) {
    return $v === null || $v === '' ? '—' : (int)$v . ' мин';
};
$plural = function ($n, $one, $few, $many) {
    $n = abs($n) % 100; $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $many;
    if ($n1 > 1 && $n1 < 5) return $few;
    if ($n1 == 1) return $one;
    return $many;
};

$photo = $assetUrl($recipe->photo);
$ingredients = $recipe->recipeIngredients;
$steps = $recipe->recipeSteps;
$hasNutrition = $recipe->protein !== null || $recipe->fat !== null || $recipe->carbs !== null;
$p = (float)$recipe->protein; $f = (float)$recipe->fat; $c = (float)$recipe->carbs;
$sum = $p + $f + $c;
$pp = $sum > 0 ? round($p / $sum * 100, 1) : 0;
$fp = $sum > 0 ? round($f / $sum * 100, 1) : 0;
$cp = $sum > 0 ? round($c / $sum * 100, 1) : 0;
$tapes = ['washi-sage', 'washi-honey', 'washi-terracotta', 'washi-sage'];
$tones = ['text-terracotta', 'text-sage', 'text-terracotta', 'text-sage'];
$rots = ['-rotate-[0.35deg]', 'rotate-[0.4deg]', '-rotate-[0.5deg]', 'rotate-[0.35deg]'];
?>
<!-- ================= MAIN RECIPE SPREAD ================= -->
<main class="w-full pt-24 pb-12 flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8">

        <!-- MAIN TWO-PAGE SPREAD CONTAINER -->
        <div class="relative w-full bg-[#FAF4EB] border-2 border-[#D4B996] shadow-scrapbook p-6 md:p-8 lg:p-10 rounded-3xl overflow-hidden">

            <!-- Spine Binding Shadow -->
            <div class="hidden lg:block absolute top-0 bottom-0 left-1/2 w-8 -translate-x-1/2 pointer-events-none z-10">
                <div class="w-full h-full bg-gradient-to-r from-warm-ink/10 via-warm-ink/5 to-transparent"></div>
                <div class="absolute top-0 bottom-0 left-1/2 w-0.5 -translate-x-1/2 bg-[#D4B996]/40"></div>
            </div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- LEFT PAGE: OVERVIEW & MEDIA -->
                <div class="lg:col-span-6 flex flex-col gap-6 lg:pr-4">

                    <!-- Header & Category -->
                    <div class="relative flex flex-col gap-2 border-b border-dashed border-[#D4B996] pb-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 bg-[#E8F0E9] text-sage font-mono text-xs uppercase tracking-wider font-bold -rotate-1 shadow-xs border border-sage/20 rounded-md">
                                <?= Html::encode($recipe->category ? $recipe->category->name : 'Рецепт') ?>
                            </span>
                            <?php if ($recipe->status !== 'Опубликовано'): ?>
                                <span class="px-3 py-1 bg-[#FFF3DC] text-honey font-mono text-xs uppercase tracking-wider font-bold border border-honey/40 rounded-md">Черновик</span>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-3xl lg:text-4xl text-warm-ink font-serif font-bold tracking-tight leading-snug mt-1">
                            <?= Html::encode($recipe->title) ?>
                        </h1>
                        <?php if (trim((string)$recipe->description) !== ''): ?>
                            <p class="font-sans text-sm md:text-base text-warm-muted leading-relaxed">
                                «<?= Html::encode($recipe->description) ?>»
                            </p>
                        <?php endif; ?>
                        <?php if ($recipe->user): ?>
                            <p class="font-mono text-xs text-warm-muted">Записал(а): <?= Html::encode($recipe->user->fullname) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Main Dish Image -->
                    <div class="relative p-3 bg-white border border-[#D4B996] shadow-polaroid -rotate-[0.75deg] rounded-2xl">
                        <div class="washi-tape washi-sage -top-3 -left-3 w-14 h-5 -rotate-45 pointer-events-none"></div>
                        <div class="washi-tape washi-sage -top-3 -right-3 w-14 h-5 rotate-45 pointer-events-none"></div>

                        <div class="relative w-full aspect-[4/3] overflow-hidden rounded-xl bg-[#FFF9F4] flex items-center justify-center">
                            <?php if ($photo): ?>
                                <img alt="<?= Html::encode($recipe->title) ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" src="<?= Html::encode($photo) ?>">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-7xl text-[#dec0b7]">restaurant_menu</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Metrics Badges -->
                    <div class="grid grid-cols-3 gap-3 mt-4">
                        <div class="p-3 bg-white border border-[#D4B996] shadow-xs flex flex-col items-center text-center rounded-xl">
                            <span class="material-symbols-outlined text-terracotta text-xl">schedule</span>
                            <span class="font-mono text-[11px] text-warm-muted uppercase tracking-wider mt-1">Подготовка</span>
                            <span class="font-mono text-sm text-warm-ink font-bold"><?= $mins($recipe->prep_time_min) ?></span>
                        </div>
                        <div class="p-3 bg-white border border-[#D4B996] shadow-xs flex flex-col items-center text-center rounded-xl">
                            <span class="material-symbols-outlined text-honey text-xl">oven_gen</span>
                            <span class="font-mono text-[11px] text-warm-muted uppercase tracking-wider mt-1">Готовка</span>
                            <span class="font-mono text-sm text-warm-ink font-bold"><?= $mins($recipe->cook_time_min) ?></span>
                        </div>
                        <div class="p-3 bg-white border border-[#D4B996] shadow-xs flex flex-col items-center text-center rounded-xl">
                            <span class="material-symbols-outlined text-sage text-xl">equalizer</span>
                            <span class="font-mono text-[11px] text-warm-muted uppercase tracking-wider mt-1">Сложность</span>
                            <span class="font-mono text-sm text-warm-ink font-bold"><?= Html::encode($recipe->difficulty) ?></span>
                        </div>
                    </div>

                    <?php if ($recipe->calories !== null): ?>
                    <!-- Calorie Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 p-3.5 bg-[#FFF9F4] border border-[#D4B996] rounded-2xl shadow-xs">
                        <div class="flex items-center gap-1 font-mono text-xs text-warm-muted font-bold">
                            <span class="material-symbols-outlined text-base text-terracotta">local_fire_department</span>
                            <span><?= (int)$recipe->calories ?> ккал / порция</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (trim((string)$recipe->secret) !== ''): ?>
                    <!-- Secret Card -->
                    <div class="relative p-4 bg-white border-l-4 border-l-terracotta border border-[#D4B996] shadow-xs rounded-2xl rotate-[0.5deg]">
                        <div class="washi-tape washi-terracotta -top-2 left-6 w-12 h-3.5 opacity-60"></div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-terracotta text-xl shrink-0 mt-0.5">stylus_note</span>
                            <div class="flex flex-col">
                                <span class="font-mono text-[13px] text-terracotta uppercase font-bold tracking-wider">Секрет:</span>
                                <p class="font-serif italic text-base text-warm-ink mt-1.5 leading-relaxed">
                                    «<?= Html::encode($recipe->secret) ?>»
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button class="flex items-center gap-2 px-5 py-2.5 font-sans text-xs font-bold shadow-xs transition-all rounded-xl cursor-pointer <?= $isFavorite ? 'bg-terracotta hover:bg-terracotta-hover text-white' : 'bg-white hover:bg-paper border border-[#D4B996] text-warm-ink' ?>" id="rs-fav-btn" type="button">
                            <span class="material-symbols-outlined text-base" id="rs-fav-icon" style="font-variation-settings: 'FILL' <?= $isFavorite ? 1 : 0 ?>;">favorite</span>
                            <span id="rs-fav-text"><?= $isFavorite ? 'В избранном' : 'В избранное' ?></span>
                        </button>
                        <?php if ($isOwner): ?>
                            <a class="flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-paper border border-[#D4B996] text-warm-ink font-sans text-xs font-bold transition-all rounded-xl" href="<?= Url::to(['site/profile']) ?>">
                                <span class="material-symbols-outlined text-base">edit</span>
                                <span>Править в профиле</span>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- RIGHT PAGE: INGREDIENTS & NUTRITION -->
                <div class="lg:col-span-6 flex flex-col gap-6 lg:pl-4">

                    <div class="p-6 bg-white border border-[#D4B996] shadow-xs flex flex-col gap-5 relative rounded-3xl">

                        <div class="absolute -top-3 right-8 flex flex-col items-center">
                            <span class="material-symbols-outlined text-warm-muted text-3xl">attach_file</span>
                        </div>

                        <!-- Ingredients Header & Servings Counter -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-dashed border-[#D4B996] pb-3">
                            <div>
                                <span class="font-mono text-[11px] text-warm-muted uppercase tracking-widest">Перечень припасов</span>
                                <h2 class="text-2xl text-warm-ink font-serif font-bold tracking-tight">Ингредиенты</h2>
                            </div>

                            <div class="flex items-center gap-2 bg-[#FFF9F4] border border-[#D4B996] px-3 py-1 shadow-xs rounded-xl self-start sm:self-auto">
                                <span class="font-mono text-xs text-warm-muted font-bold uppercase">Порций:</span>
                                <div class="flex items-center gap-1">
                                    <button aria-label="Уменьшить" class="w-6 h-6 flex items-center justify-center text-warm-ink font-mono text-base hover:text-terracotta cursor-pointer select-none" id="rs-dec" type="button">−</button>
                                    <input aria-label="Количество порций" class="w-10 text-center font-mono text-sm text-terracotta font-bold bg-white border border-[#D4B996] rounded-md focus:outline-none focus:border-terracotta p-0.5" id="rs-servings" max="100" min="1" type="number" value="<?= (int)($recipe->servings ?: 4) ?>">
                                    <button aria-label="Увеличить" class="w-6 h-6 flex items-center justify-center text-warm-ink font-mono text-base hover:text-terracotta cursor-pointer select-none" id="rs-inc" type="button">+</button>
                                </div>
                            </div>
                        </div>

                        <?php if (!$ingredients): ?>
                            <p class="text-sm text-warm-muted">Ингредиенты пока не указаны.</p>
                        <?php endif; ?>

                        <!-- Ruled Checklist (отмеченное = уже есть дома) -->
                        <div class="flex flex-col gap-2 text-warm-ink" id="rs-ingredients">
                            <?php foreach ($ingredients as $ri): $name = $ri->ingredient ? $ri->ingredient->name : 'Ингредиент'; ?>
                            <label class="group flex items-center justify-between py-1.5 px-2 cursor-pointer transition-colors hover:bg-[#FFF9F4] rounded-lg">
                                <div class="flex items-center gap-3 min-w-0">
                                    <input class="w-4 h-4 text-[#C85A32] border-[#dec0b7] rounded-md cursor-pointer focus:ring-[#C85A32] accent-[#C85A32]" data-ingredient-id="<?= (int)$ri->ingredient_id ?>" type="checkbox">
                                    <span class="font-sans text-sm text-warm-ink truncate group-has-[:checked]:text-warm-muted group-has-[:checked]:line-through"><?= Html::encode($name) ?><?php if (trim((string)$ri->note) !== ''): ?> <span class="text-warm-muted">(<?= Html::encode($ri->note) ?>)</span><?php endif; ?></span>
                                </div>
                                <span class="font-mono text-sm text-terracotta font-bold whitespace-nowrap ml-2 rs-qty" data-qty="<?= $ri->quantity === null ? '' : $num($ri->quantity) ?>" data-unit="<?= Html::encode((string)$ri->unit) ?>"><?= Html::encode(trim(($ri->quantity === null ? '' : str_replace('.', ',', $num($ri->quantity))) . ' ' . $ri->unit) ?: '—') ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($ingredients): ?>
                        <!-- Add Missing Ingredients Button -->
                        <button class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold shadow-sm transition-all rounded-xl cursor-pointer mt-2" id="rs-cart-btn" type="button">
                            <span class="material-symbols-outlined text-lg">shopping_basket</span>
                            <span>Добавить недостающие ингредиенты в корзину</span>
                        </button>
                        <p class="font-sans text-[11px] text-warm-muted -mt-2">Отметьте то, что уже есть дома — остальное попадёт в список покупок.</p>
                        <?php endif; ?>

                        <?php if (trim((string)$recipe->equipment) !== ''): ?>
                        <!-- Utensils Note -->
                        <div class="p-3 bg-[#FFF9F4] border border-[#D4B996] flex items-center justify-between gap-3 rounded-xl">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-sage text-lg">skillet</span>
                                <span class="font-mono text-xs text-warm-ink font-bold">Потребуется:</span>
                                <span class="font-sans text-xs text-warm-muted"><?= Html::encode($recipe->equipment) ?></span>
                            </div>
                            <span class="material-symbols-outlined text-warm-muted text-base">check</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($hasNutrition || $recipe->calories !== null): ?>
                    <!-- Nutrition Chart -->
                    <div class="p-4 bg-white border border-[#D4B996] shadow-xs flex items-center justify-between gap-4 rounded-2xl">
                        <div class="flex flex-col">
                            <span class="font-mono text-[11px] text-warm-muted uppercase tracking-wider">Пищевая ценность (на порцию)</span>
                            <div class="flex items-center gap-4 mt-2">
                                <div>
                                    <span class="font-mono text-sm text-warm-ink font-bold"><?= $recipe->protein === null ? '—' : $num($recipe->protein) . ' г' ?></span>
                                    <span class="block font-sans text-xs text-warm-muted">Белки</span>
                                </div>
                                <div>
                                    <span class="font-mono text-sm text-warm-ink font-bold"><?= $recipe->fat === null ? '—' : $num($recipe->fat) . ' г' ?></span>
                                    <span class="block font-sans text-xs text-warm-muted">Жиры</span>
                                </div>
                                <div>
                                    <span class="font-mono text-sm text-warm-ink font-bold"><?= $recipe->carbs === null ? '—' : $num($recipe->carbs) . ' г' ?></span>
                                    <span class="block font-sans text-xs text-warm-muted">Углеводы</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                                <circle class="stroke-[#FAF4EB]" cx="18" cy="18" fill="none" r="14" stroke="#FAF4EB" stroke-width="3"></circle>
                                <?php if ($sum > 0): ?>
                                <circle class="stroke-terracotta" cx="18" cy="18" fill="none" pathLength="100" r="14" stroke-dasharray="<?= $pp ?> 100" stroke-width="3.5"></circle>
                                <circle class="stroke-sage" cx="18" cy="18" fill="none" pathLength="100" r="14" stroke-dasharray="<?= $fp ?> 100" stroke-dashoffset="-<?= $pp ?>" stroke-width="3.5"></circle>
                                <circle class="stroke-honey" cx="18" cy="18" fill="none" pathLength="100" r="14" stroke-dasharray="<?= $cp ?> 100" stroke-dashoffset="-<?= round($pp + $fp, 1) ?>" stroke-width="3.5"></circle>
                                <?php endif; ?>
                            </svg>
                            <?php if ($recipe->calories !== null): ?>
                                <span class="absolute font-mono text-[10px] text-warm-ink font-bold"><?= (int)$recipe->calories ?> к</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>

        <!-- ================= STEP-BY-STEP GUIDE ================= -->
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between gap-4 pb-2 border-b border-dashed border-[#D4B996]">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-terracotta text-white flex items-center justify-center font-mono text-sm font-bold shadow-xs rounded-lg">§</div>
                    <div>
                        <h2 class="text-2xl text-warm-ink font-serif font-bold">Пошаговое руководство</h2>
                        <p class="font-sans text-xs text-warm-muted">Следуйте технологии шаг за шагом</p>
                    </div>
                </div>
                <?php if ($steps): $cnt = count($steps); ?>
                <div class="hidden sm:flex items-center gap-2 font-mono text-xs text-warm-muted uppercase tracking-wider">
                    <span>Всего <?= $cnt ?> <?= $plural($cnt, 'этап', 'этапа', 'этапов') ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!$steps): ?>
                <p class="text-sm text-warm-muted">Шаги приготовления пока не добавлены.</p>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($steps as $i => $st): $simg = $assetUrl($st->photo); ?>
                <div class="relative p-6 bg-white border border-[#D4B996] shadow-xs flex flex-col justify-between gap-4 rounded-2xl <?= $rots[$i % 4] ?> transition-all hover:rotate-0 step-card">
                    <div class="washi-tape <?= $tapes[$i % 4] ?> -top-2.5 left-1/2 -translate-x-1/2 w-20 h-4"></div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-[#FFF9F4] <?= $tones[$i % 4] ?> font-mono text-xs uppercase font-bold border border-[#D4B996] rounded-md">
                                Шаг <?= (int)$st->step_number ?><?php if ($st->duration_min): ?> • <?= (int)$st->duration_min ?> мин<?php endif; ?>
                            </span>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input class="w-4 h-4 text-[#C85A32] border-[#dec0b7] rounded-md cursor-pointer focus:ring-[#C85A32] accent-[#C85A32] step-check" type="checkbox">
                                <span class="font-mono text-xs text-warm-muted">Сделано</span>
                            </label>
                        </div>
                        <?php if (trim((string)$st->title) !== ''): ?>
                            <h3 class="text-xl text-warm-ink font-serif font-bold mt-1"><?= Html::encode($st->title) ?></h3>
                        <?php endif; ?>
                        <?php if ($simg): ?>
                        <div class="w-full h-36 overflow-hidden bg-[#FFF9F4] my-2 relative border border-[#D4B996] rounded-xl">
                            <img alt="<?= Html::encode($st->title) ?>" class="w-full h-full object-cover" src="<?= Html::encode($simg) ?>">
                        </div>
                        <?php endif; ?>
                        <p class="font-sans text-xs text-warm-ink leading-relaxed"><?= nl2br(Html::encode($st->description)) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<!-- Toast -->
<div class="fixed bottom-6 right-6 z-50 bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm hidden rounded-2xl" id="rs-toast">
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-terracotta text-2xl shrink-0">verified</span>
        <div class="flex flex-col">
            <span class="font-serif font-bold text-base text-warm-ink">Folk Menu</span>
            <span class="font-sans text-xs text-warm-muted mt-0.5" id="rs-toast-msg"></span>
        </div>
    </div>
</div>

<script>
(function () {
    var cfg = {
        recipeId: <?= (int)$recipe->id ?>,
        guest: <?= Yii::$app->user->isGuest ? 'true' : 'false' ?>,
        loginUrl: <?= json_encode(Url::to(['site/login'])) ?>,
        favUrl: <?= json_encode(Url::to(['site/toggle-favorite', 'id' => $recipe->id])) ?>,
        cartUrl: <?= json_encode(Url::to(['site/add-to-shopping-list', 'id' => $recipe->id])) ?>,
        csrf: <?= json_encode(Yii::$app->request->csrfToken) ?>,
        shoppingUrl: <?= json_encode(Url::to(['site/profile'])) ?>,
        baseServings: <?= (int)($recipe->servings ?: 4) ?>
    };
    var isFav = <?= $isFavorite ? 'true' : 'false' ?>;

    function toast(msg) {
        var t = document.getElementById('rs-toast');
        document.getElementById('rs-toast-msg').textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { t.classList.add('hidden'); }, 4000);
    }
    function post(url, data) {
        var body = new URLSearchParams(data || {});
        body.append('_csrf', cfg.csrf);
        return fetch(url, { method: 'POST', body: body, headers: { 'X-CSRF-Token': cfg.csrf, 'Accept': 'application/json' } })
            .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); });
    }

    // --- порции: пересчёт количеств (в рецепте количества записаны на указанное число порций) ---
    var input = document.getElementById('rs-servings');
    function fmt(x, unit) {
        if ((unit === 'г' || unit === 'мл') && x >= 10) return String(Math.round(x));
        return String(Math.round(x * 100) / 100).replace('.', ',');
    }
    function recalc() {
        var n = parseInt(input.value, 10);
        if (isNaN(n) || n < 1) n = 1;
        if (n > 100) n = 100;
        var ratio = n / cfg.baseServings;
        document.querySelectorAll('.rs-qty').forEach(function (el) {
            var q = el.dataset.qty, unit = el.dataset.unit;
            if (q === '') { el.textContent = unit || '—'; return; }
            el.textContent = (fmt(parseFloat(q) * ratio, unit) + ' ' + unit).trim();
        });
    }
    function setServings(n) {
        n = Math.max(1, Math.min(100, n));
        input.value = n;
        recalc();
    }
    document.getElementById('rs-dec').addEventListener('click', function () { setServings((parseInt(input.value, 10) || cfg.baseServings) - 1); });
    document.getElementById('rs-inc').addEventListener('click', function () { setServings((parseInt(input.value, 10) || cfg.baseServings) + 1); });
    input.addEventListener('input', function () { if (parseInt(input.value, 10) > 0) recalc(); });
    input.addEventListener('change', function () { setServings(parseInt(input.value, 10) || cfg.baseServings); });

    // --- избранное ---
    var favBtn = document.getElementById('rs-fav-btn');
    function renderFav() {
        var icon = document.getElementById('rs-fav-icon');
        document.getElementById('rs-fav-text').textContent = isFav ? 'В избранном' : 'В избранное';
        icon.style.fontVariationSettings = "'FILL' " + (isFav ? 1 : 0);
        favBtn.classList.toggle('bg-terracotta', isFav);
        favBtn.classList.toggle('hover:bg-terracotta-hover', isFav);
        favBtn.classList.toggle('text-white', isFav);
        favBtn.classList.toggle('bg-white', !isFav);
        favBtn.classList.toggle('border', !isFav);
        favBtn.classList.toggle('border-[#D4B996]', !isFav);
        favBtn.classList.toggle('text-warm-ink', !isFav);
    }
    favBtn.addEventListener('click', function () {
        if (cfg.guest) { window.location.href = cfg.loginUrl; return; }
        post(cfg.favUrl).then(function (r) {
            isFav = !!r.favorite;
            renderFav();
            toast(isFav ? 'Рецепт добавлен в избранное' : 'Рецепт удалён из избранного');
        }).catch(function () { toast('Не удалось изменить избранное'); });
    });

    // --- список покупок ---
    var cartBtn = document.getElementById('rs-cart-btn');
    if (cartBtn) {
        cartBtn.addEventListener('click', function () {
            if (cfg.guest) { window.location.href = cfg.loginUrl; return; }
            var ids = [];
            document.querySelectorAll('#rs-ingredients input[type=checkbox]:not(:checked)').forEach(function (cb) { ids.push(cb.dataset.ingredientId); });
            if (!ids.length) { toast('Все ингредиенты уже отмечены как имеющиеся!'); return; }
            var body = new URLSearchParams();
            ids.forEach(function (id) { body.append('ingredient_ids[]', id); });
            body.append('servings', parseInt(input.value, 10) || cfg.baseServings);
            body.append('_csrf', cfg.csrf);
            fetch(cfg.cartUrl, { method: 'POST', body: body, headers: { 'X-CSRF-Token': cfg.csrf, 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(function (r) { toast('В список покупок добавлено позиций: ' + r.added); })
                .catch(function () { toast('Не удалось добавить в список покупок'); });
        });
    }
})();
</script>
