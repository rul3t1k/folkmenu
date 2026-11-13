<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\Recipe|null $hero рецепт дня */
/** @var app\models\Recipe[] $popular */
/** @var app\models\Category[] $categories */
/** @var int[] $catCounts category_id => число опубликованных рецептов */
/** @var int[] $favCounts */
/** @var int[] $myFavorites */

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
$catStyles = [
    ['icon' => 'soup_kitchen',   'box' => 'bg-sage-light text-sage',       'hover' => 'hover:border-sage',       'text' => 'text-sage',       'group' => 'group-hover:text-sage',       'tape' => 'washi-sage'],
    ['icon' => 'bakery_dining',  'box' => 'bg-terracotta-light text-terracotta', 'hover' => 'hover:border-terracotta', 'text' => 'text-terracotta', 'group' => 'group-hover:text-terracotta', 'tape' => 'washi-terracotta'],
    ['icon' => 'cake',           'box' => 'bg-honey-light text-honey',     'hover' => 'hover:border-honey',      'text' => 'text-honey',      'group' => 'group-hover:text-honey',      'tape' => 'washi-honey'],
    ['icon' => 'skillet',        'box' => 'bg-rose-light text-rose',       'hover' => 'hover:border-rose',       'text' => 'text-rose',       'group' => 'group-hover:text-rose',       'tape' => 'washi-sage'],
    ['icon' => 'nutrition',      'box' => 'bg-sage-light text-sage',       'hover' => 'hover:border-sage',       'text' => 'text-sage',       'group' => 'group-hover:text-sage',       'tape' => 'washi-honey'],
    ['icon' => 'egg_alt',        'box' => 'bg-[#FFF8EE] text-honey',       'hover' => 'hover:border-honey',      'text' => 'text-honey',      'group' => 'group-hover:text-honey',      'tape' => 'washi-terracotta'],
];
$diffColor = ['Простая' => 'text-sage', 'Средняя' => 'text-honey', 'Для терпеливых' => 'text-terracotta'];
?><!-- ================= MAIN CONTENT ================= -->
<main class="w-full pt-20 flex-1">
    
    <!-- Top Archival Welcome Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-2">
        <div class="relative bg-paper border border-craft-border rounded-2xl p-5 md:p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-5 overflow-hidden">
            <div class="flex items-center gap-4 pt-1 md:pt-0">
                <div class="w-13 h-13 rounded-2xl bg-white border border-craft-border flex items-center justify-center text-terracotta shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-2xl">auto_stories</span>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-serif text-2xl sm:text-3xl font-bold text-warm-ink tracking-tight">Кулинарный архив поколений</h2>
                        <span class="text-terracotta text-sm">✦</span>
                        <span class="font-hand text-lg text-terracotta font-bold hidden sm:inline-block">100 лет семейных традиций</span>
                    </div>
                    <p class="text-sm text-warm-muted mt-1 max-w-2xl">
                        Бережно собранные, береженные и адаптированные рецепты домашней кухни: живые рукописные заметки, точные граммовки и тепло родного очага.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3.5 bg-white/90 border border-craft-border px-4 py-2.5 rounded-xl shadow-xs shrink-0 self-stretch md:self-auto justify-between md:justify-start">
                <div class="w-10 h-10 rounded-lg bg-sage-light text-sage flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl">inventory_2</span>
                </div>
                <div class="text-left">
                    <div class="text-xs font-bold text-warm-ink uppercase tracking-wider">Том VII • Осенняя коллекция</div>
                    <div class="text-xs text-warm-muted mt-0.5"><?php $totalRecipes = array_sum($catCounts); echo $totalRecipes . ' ' . $plural($totalRecipes, 'проверенное блюдо', 'проверенных блюда', 'проверенных блюд'); ?></div>
                </div>
            </div>
        </div>
    </section>


    <!-- ================= HERO SECTION: РЕЦЕПТ ДНЯ ================= -->
    <?php if ($hero): ?>
    <?php
        $heroPhoto = $photoUrl($hero->photo);
        $heroIngs = [];
        foreach ($hero->recipeIngredients as $ri) { if ($ri->ingredient) $heroIngs[] = $ri->ingredient->name; }
        $heroIngs = array_slice($heroIngs, 0, 6);
        $heroFav = in_array((int)$hero->id, $myFavorites, true);
        $heroTime = $hero->cook_time_min ?: ((int)$hero->prep_time_min + (int)$hero->cook_time_min);
        $heroAuthor = $hero->user ? $hero->user->fullname : '';
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
        <div class="relative bg-surface border-2 border-craft-border rounded-3xl shadow-scrapbook overflow-hidden p-6 sm:p-8 lg:p-10">
            <div class="washi-tape washi-terracotta top-7 sm:top-8 left-8 sm:left-12 px-8 py-3 flex items-center justify-center text-base sm:text-lg text-white font-bold tracking-widest uppercase shadow-md z-20 rotate-0">★ РЕЦЕПТ ДНЯ ★</div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center pt-4">
                <div class="lg:col-span-7">
                    <div class="relative bg-white p-4 sm:p-5 rounded-2xl border border-craft-border shadow-polaroid -rotate-1 hover:rotate-0 transition-transform duration-500">
                        <div class="washi-tape -top-3 left-6 w-24 h-6 rounded-xs -rotate-4 washi-sage"></div>
                        <div class="washi-tape washi-honey -bottom-3 right-8 w-28 h-6 rounded-xs rotate-2"></div>

                        <div class="relative w-full h-80 sm:h-96 md:h-[420px] rounded-xl overflow-hidden bg-craft-subtle border border-craft-border">
                            <?php if ($heroPhoto): ?>
                                <img alt="<?= Html::encode($hero->title) ?>" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700" src="<?= Html::encode($heroPhoto) ?>">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-7xl text-[#dec0b7]">restaurant_menu</span></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-warm-ink/85 via-warm-ink/25 to-transparent"></div>
                            <div class="absolute bottom-5 left-5 right-5 text-white">
                                <?php if ($hero->category): ?>
                                <div class="text-xs uppercase tracking-widest text-honey-warm font-semibold mb-1.5 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm">auto_stories</span>
                                    <span><?= Html::encode($hero->category->name) ?></span>
                                </div>
                                <?php endif; ?>
                                <h1 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold leading-snug drop-shadow-md"><?= Html::encode($hero->title) ?></h1>
                            </div>
                        </div>

                        <?php if ($heroAuthor): ?>
                        <div class="pt-3.5 px-1 text-xs text-warm-muted">
                            <span class="font-serif italic text-sm sm:text-base font-semibold text-vintage-brown">Автор рецепта: <?= Html::encode($heroAuthor) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-warm-muted font-medium">Карточка рецепта № <?= (int)$hero->id ?></span>
                    </div>

                    <?php if (trim((string)$hero->description) !== ''): ?>
                    <blockquote class="font-serif text-2xl md:text-3xl text-warm-ink leading-snug italic border-l-4 border-terracotta pl-4 my-1 font-bold">
                        «<?= Html::encode($hero->description) ?>»
                    </blockquote>
                    <?php endif; ?>

                    <div class="grid grid-cols-3 gap-2 py-3 px-3.5 bg-paper border border-craft-border rounded-xl shadow-xs">
                        <div class="flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-terracotta text-2xl mb-1">timer</span>
                            <span class="font-bold text-base text-warm-ink"><?= $fmtTime($heroTime) ?: '—' ?></span>
                            <span class="text-[11px] text-warm-muted">Время приготовления</span>
                        </div>
                        <div class="flex flex-col items-center text-center border-x border-craft-border">
                            <span class="material-symbols-outlined text-sage text-2xl mb-1">tune</span>
                            <span class="font-bold text-base text-warm-ink"><?= Html::encode($hero->difficulty) ?></span>
                            <span class="text-[11px] text-warm-muted">Сложность</span>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <span class="material-symbols-outlined text-honey text-2xl mb-1">local_fire_department</span>
                            <span class="font-bold text-base text-warm-ink"><?= $hero->calories !== null ? (int)$hero->calories . ' ккал' : '—' ?></span>
                            <span class="text-[11px] text-warm-muted">На порцию</span>
                        </div>
                    </div>

                    <?php if (trim((string)$hero->secret) !== ''): ?>
                    <div class="p-4 bg-honey-light border-2 border-dashed border-honey-warm/60 rounded-xl relative shadow-xs">
                        <div class="washi-tape washi-honey -top-2.5 right-8 w-18 h-4 rounded-xs rotate-1"></div>
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-terracotta text-xl shrink-0 mt-0.5">lightbulb</span>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-terracotta flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">draw</span> Секрет хозяйки:
                                </h4>
                                <p class="text-sm text-warm-ink italic mt-1 leading-relaxed">«<?= Html::encode($hero->secret) ?>»</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($heroIngs): ?>
                    <div class="text-sm bg-white p-3.5 rounded-xl border border-craft-border shadow-xs">
                        <span class="text-xs uppercase font-bold text-warm-muted tracking-wider block mb-1">Главные ингредиенты:</span>
                        <p class="text-warm-ink leading-relaxed font-medium"><?= Html::encode(implode(', ', $heroIngs)) ?>.</p>
                    </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-3 pt-1">
                        <a class="flex-1 py-3 px-6 bg-terracotta hover:bg-terracotta-hover text-white rounded-xl font-bold text-sm text-center shadow-md border border-terracotta/40 transition-all active:scale-[0.98] flex items-center justify-center gap-2 group" href="<?= Url::to(['site/recipe', 'id' => $hero->id]) ?>">
                            <span class="material-symbols-outlined text-lg leading-none group-hover:rotate-12 transition-transform">menu_book</span>
                            <span>Смотреть рецепт</span>
                        </a>
                        <button class="rc-fav p-3 bg-white hover:bg-paper <?= $heroFav ? 'text-terracotta' : 'text-warm-muted' ?> border border-craft-border rounded-xl transition-colors shadow-xs active:scale-95" data-id="<?= (int)$hero->id ?>" data-fav="<?= $heroFav ? 1 : 0 ?>" title="Сохранить в избранное" type="button">
                            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' <?= $heroFav ? 1 : 0 ?>;">favorite</span>
                        </button>
                        <button class="hero-cart p-3 bg-white hover:bg-paper text-sage border border-craft-border rounded-xl transition-colors shadow-xs active:scale-95" data-id="<?= (int)$hero->id ?>" data-servings="<?= (int)($hero->servings ?: 4) ?>" data-ings="<?= Html::encode(json_encode(array_map(function ($ri) { return (int)$ri->ingredient_id; }, $hero->recipeIngredients))) ?>" title="Добавить ингредиенты в список покупок" type="button">
                            <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= CATEGORY GRID ================= -->
    <?php if ($categories): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-terracotta uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-terracotta"></span> Оглавление кулинарной книги
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-warm-ink mt-1">Категории блюд</h2>
            </div>
            <p class="text-sm text-warm-muted max-w-md">
                Удобный рубрикатор: выберите категорию и откройте все рецепты из неё.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
            <?php foreach ($categories as $i => $cat): $st = $catStyles[$i % count($catStyles)]; $cnt = $catCounts[$cat->id] ?? 0; ?>
            <a class="group relative bg-white border border-craft-border rounded-2xl p-6 shadow-xs hover:shadow-card-lift <?= $st['hover'] ?> transition-all duration-300 flex flex-col justify-between" href="<?= Url::to(['site/recipes', 'category' => $cat->slug]) ?>">
                <div class="washi-tape <?= $st['tape'] ?> -top-3 left-8 w-24 h-6 rounded-xs -rotate-2"></div>
                <div>
                    <div class="flex items-center justify-between mb-4 pt-1">
                        <div class="w-12 h-12 rounded-xl <?= $st['box'] ?> flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-2xl"><?= $st['icon'] ?></span>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-paper text-warm-muted border border-craft-border"><?= $cnt . ' ' . $plural($cnt, 'рецепт', 'рецепта', 'рецептов') ?></span>
                    </div>
                    <h3 class="font-serif text-2xl font-bold text-warm-ink <?= $st['group'] ?> transition-colors"><?= Html::encode($cat->name) ?></h3>
                    <?php if (trim((string)$cat->description) !== ''): ?>
                    <p class="text-sm text-warm-muted mt-2 leading-relaxed"><?= Html::encode($cat->description) ?></p>
                    <?php endif; ?>
                </div>
                <div class="pt-5 mt-3 border-t border-craft-subtle flex items-center text-xs font-bold <?= $st['text'] ?> group-hover:translate-x-1 transition-transform">
                    <span>Смотреть категорию</span>
                    <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= POPULAR RECIPES SECTION ================= -->
    <?php if ($popular): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 gap-4">
            <div>
                <div class="flex items-center gap-1.5 text-xs font-bold text-terracotta uppercase tracking-wider">
                    <span class="material-symbols-outlined text-base">workspace_premium</span> Проверено годами
                </div>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-warm-ink mt-1">Популярные рецепты</h2>
            </div>
            <a class="px-3.5 py-1.5 rounded-lg bg-terracotta text-white text-xs font-bold shadow-xs self-start md:self-auto" href="<?= Url::to(['site/recipes']) ?>">Все рецепты</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($popular as $r): $ph = $photoUrl($r->photo); $isFav = in_array((int)$r->id, $myFavorites, true); $t = $fmtTime((int)$r->prep_time_min + (int)$r->cook_time_min); $sv = (int)($r->servings ?: 4); ?>
            <article class="bg-white border border-craft-border rounded-2xl p-4 shadow-sm hover:shadow-card-lift transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative w-full h-56 rounded-xl overflow-hidden bg-paper border border-craft-subtle">
                        <?php if ($ph): ?>
                            <img alt="<?= Html::encode($r->title) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="<?= Html::encode($ph) ?>">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-6xl text-[#dec0b7]">restaurant_menu</span></div>
                        <?php endif; ?>
                        <button aria-label="В избранное" class="rc-fav absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 hover:bg-white <?= $isFav ? 'text-terracotta' : 'text-warm-muted' ?> flex items-center justify-center shadow-xs transition-transform active:scale-90" data-id="<?= (int)$r->id ?>" data-fav="<?= $isFav ? 1 : 0 ?>" type="button">
                            <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' <?= $isFav ? 1 : 0 ?>;">favorite</span>
                        </button>
                    </div>
                    <div class="pt-4">
                        <div class="flex items-center gap-2 text-xs text-warm-muted mb-2">
                            <?php if ($t): ?><span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> <?= $t ?></span><span>•</span><?php endif; ?>
                            <span><?= $sv . ' ' . $plural($sv, 'порция', 'порции', 'порций') ?></span>
                        </div>
                        <h3 class="font-serif text-2xl font-bold text-warm-ink leading-snug group-hover:text-terracotta transition-colors line-clamp-2"><?= Html::encode($r->title) ?></h3>
                        <?php if (trim((string)$r->description) !== ''): ?>
                        <p class="text-sm text-warm-muted mt-2 line-clamp-2 leading-relaxed"><?= Html::encode($r->description) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-craft-subtle flex items-center justify-between">
                    <div class="text-xs">
                        <span class="text-warm-muted block">Сложность:</span>
                        <span class="font-semibold <?= $diffColor[$r->difficulty] ?? 'text-sage' ?>"><?= Html::encode($r->difficulty) ?></span>
                    </div>
                    <a class="px-4 py-2 bg-terracotta hover:bg-terracotta-hover text-white text-xs font-bold rounded-lg shadow-xs transition-colors flex items-center gap-1.5" href="<?= Url::to(['site/recipe', 'id' => $r->id]) ?>">
                        <span>Открыть рецепт</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= CULINARY NOTES & TIPS ================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-paper border-2 border-craft-border rounded-3xl p-6 sm:p-8 lg:p-10 relative shadow-sm">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-3">
                <div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-terracotta uppercase tracking-wider">
                        <span class="material-symbols-outlined text-base">edit_note</span> Полезные заметки
                    </div>
                    <h2 class="font-serif text-3xl md:text-4xl font-bold text-warm-ink mt-1">Секреты кулинаров</h2>
                </div>
                <p class="text-sm text-warm-muted max-w-md">Маленькие тонкости домашней кухни, которые экономят время и гарантируют идеальный вкус.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-1">
                <!-- Note 1 -->
                <div class="bg-white border border-craft-border rounded-2xl p-5 shadow-xs relative flex flex-col justify-between">
                    <div class="washi-tape -top-3 left-6 w-20 h-5 rounded-xs -rotate-2 washi-terracotta"></div>
                    <div>
                        <div class="flex items-center gap-2 text-sage text-xs font-bold uppercase tracking-wider mb-2 pt-1">
                            <span class="material-symbols-outlined text-base">yard</span> Совет № 1
                        </div>
                        <h4 class="font-serif text-xl font-bold text-warm-ink">Сохранение пряных трав</h4>
                        <p class="text-sm text-warm-muted mt-2 italic leading-relaxed">
                            «Мелко нарежьте тимьян и розмарин, разложите по формочкам для льда и залейте качественным оливковым маслом. Один такой кубик зимой мгновенно наполнит суп летним ароматом».
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-craft-subtle flex items-center justify-between text-xs text-warm-muted">
                        <span>Ароматные заготовки</span>
                        <span class="material-symbols-outlined text-base text-sage">check_circle</span>
                    </div>
                </div>

                <!-- Note 2 -->
                <div class="bg-white border border-craft-border rounded-2xl p-5 shadow-xs relative flex flex-col justify-between">
                    <div class="washi-tape washi-terracotta -top-3 left-6 w-20 h-5 rounded-xs rotate-1 washi-sage"></div>
                    <div>
                        <div class="flex items-center gap-2 text-terracotta text-xs font-bold uppercase tracking-wider mb-2 pt-1">
                            <span class="material-symbols-outlined text-base">straighten</span> Совет № 2
                        </div>
                        <h4 class="font-serif text-xl font-bold text-warm-ink">Пропорции без весов</h4>
                        <p class="text-sm text-warm-muted mt-2 italic leading-relaxed">
                            «Стандартную пачку сливочного масла (180 г) удобно делить ножом: пополам — 90 г, четверть — 45 г, а столовая ложка мягкого масла с горкой — ровно 20 граммов».
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-craft-subtle flex items-center justify-between text-xs text-warm-muted">
                        <span>Быстрая мерка</span>
                        <span class="material-symbols-outlined text-base text-terracotta">check_circle</span>
                    </div>
                </div>

                <!-- Note 3 -->
                <div class="bg-white border border-craft-border rounded-2xl p-5 shadow-xs relative flex flex-col justify-between">
                    <div class="washi-tape washi-honey -top-3 left-6 w-20 h-5 rounded-xs -rotate-1 washi-sage"></div>
                    <div>
                        <div class="flex items-center gap-2 text-honey text-xs font-bold uppercase tracking-wider mb-2 pt-1">
                            <span class="material-symbols-outlined text-base">cookie</span> Совет № 3
                        </div>
                        <h4 class="font-serif text-xl font-bold text-warm-ink">Секрет идеального теста</h4>
                        <p class="text-sm text-warm-muted mt-2 italic leading-relaxed">
                            «Вода для песочного теста обязательно должна быть ледяной, а масло — холодным и порубленным. Не давайте маслу нагреваться руками до того, как пирог окажется в духовке».
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-craft-subtle flex items-center justify-between text-xs text-warm-muted">
                        <span>Совет пекаря</span>
                        <span class="material-symbols-outlined text-base text-honey">check_circle</span>
                    </div>
                </div>
            </div>

            <!-- Banner for user contributions -->
            <div class="mt-8 bg-white border border-craft-border rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-xs relative overflow-hidden">
                <div class="flex items-center gap-4">
                    <div class="w-13 h-13 rounded-2xl bg-terracotta-light text-terracotta flex items-center justify-center shrink-0 border border-terracotta/20">
                        <span class="material-symbols-outlined text-2xl">history_edu</span>
                    </div>
                    <div>
                        <h3 class="font-serif text-2xl font-bold text-warm-ink">Хотите поделиться рецептом?</h3>
                        <p class="text-sm text-warm-muted mt-0.5">Пополните архив фамильным блюдом: сохраните историю, точные граммовки и авторские заметки для поколений.</p>
                    </div>
                </div>
                <a class="shrink-0 px-6 py-3.5 bg-terracotta hover:bg-terracotta-hover text-white rounded-xl text-sm font-bold shadow-sm transition-all active:scale-95 flex items-center gap-2" href="<?= Url::to(['site/profile']) ?>">
                    <span class="material-symbols-outlined text-lg leading-none">post_add</span>
                    <span>Добавить рецепт</span>
                </a>
            </div>
        </div>
    </section>
</main>

<script>
(function () {
    var CFG = {
        guest: <?= Yii::$app->user->isGuest ? 'true' : 'false' ?>,
        loginUrl: <?= json_encode(Url::to(['site/login'])) ?>,
        favUrl: <?= json_encode(Url::to(['site/toggle-favorite'])) ?>,
        cartUrl: <?= json_encode(Url::to(['site/add-to-shopping-list'])) ?>,
        csrf: <?= json_encode(Yii::$app->request->csrfToken) ?>
    };
    function post(url, params) {
        params.append('_csrf', CFG.csrf);
        return fetch(url, { method: 'POST', body: params, headers: { 'X-CSRF-Token': CFG.csrf, 'Accept': 'application/json' } })
            .then(function (r) { if (!r.ok) throw new Error(); return r.json(); });
    }
    document.querySelectorAll('.rc-fav').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (CFG.guest) { window.location.href = CFG.loginUrl; return; }
            post(CFG.favUrl + '?id=' + btn.dataset.id, new URLSearchParams())
                .then(function (r) {
                    var on = !!r.favorite;
                    btn.dataset.fav = on ? 1 : 0;
                    btn.classList.toggle('text-terracotta', on);
                    btn.classList.toggle('text-warm-muted', !on);
                    btn.querySelector('.material-symbols-outlined').style.fontVariationSettings = "'FILL' " + (on ? 1 : 0);
                })
                .catch(function () { alert('Не удалось изменить избранное'); });
        });
    });
    document.querySelectorAll('.hero-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (CFG.guest) { window.location.href = CFG.loginUrl; return; }
            var p = new URLSearchParams();
            JSON.parse(btn.dataset.ings || '[]').forEach(function (id) { p.append('ingredient_ids[]', id); });
            p.append('servings', btn.dataset.servings);
            post(CFG.cartUrl + '?id=' + btn.dataset.id, p)
                .then(function (r) { alert('В список покупок добавлено позиций: ' + r.added); })
                .catch(function () { alert('Не удалось добавить в список покупок'); });
        });
    });
})();
</script>
