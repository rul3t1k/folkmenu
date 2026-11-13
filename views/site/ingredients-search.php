<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\Ingredient[] $pantry первые 10 ингредиентов из БД */
/** @var string[] $allNames все названия ингредиентов (для подсказок) */
/** @var array $recipesData опубликованные рецепты с ингредиентами */
?>
<!-- ================= MAIN CONTENT ================= -->
<main class="w-full pt-28 pb-16 flex-1" id="inventory-section">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8">
        
        <!-- Intro Header Banner -->
        <div class="relative bg-[#FAF4EB] border-2 border-[#D4B996] p-6 sm:p-8 shadow-scrapbook rounded-3xl">
            <div class="washi-tape washi-sage -top-3 left-10 w-32 h-6 -rotate-2"></div>
            <div class="washi-tape washi-honey -top-2 right-12 w-24 h-5 rotate-3"></div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                <div class="flex flex-col gap-2 max-w-2xl">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#EDF4EF] text-sage font-mono text-xs uppercase tracking-wider font-bold self-start border border-sage/20 rounded-md">
                        <span class="material-symbols-outlined text-sm">kitchen</span>
                        Экономная кулинария
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-warm-ink font-serif">Что в холодильнике?</h1>
                    <p class="font-sans text-xs sm:text-sm text-warm-muted leading-relaxed">
                        Отметьте продукты, которые есть у вас дома, и наш архив мгновенно подберет подходящие домашние рецепты без похода в магазин.
                    </p>
                </div>

                <!-- Selected Counter Badge -->
                <div class="bg-white px-5 py-4 border border-[#D4B996] rounded-2xl shadow-xs flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 bg-terracotta/10 text-terracotta flex items-center justify-center rounded-xl">
                        <span class="material-symbols-outlined text-2xl">checklist</span>
                    </div>
                    <div>
                        <span class="block font-mono text-[11px] text-warm-muted uppercase tracking-wider">Выбрано продуктов</span>
                        <span class="font-mono text-lg font-bold text-terracotta" id="ps-counter">0 позиций</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory List Box -->
        <div class="relative bg-white border border-[#D4B996] shadow-scrapbook p-6 sm:p-8 rounded-3xl">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-dashed border-[#D4B996] pb-4 mb-6">
                <div>
                    <span class="font-mono text-[11px] text-warm-muted uppercase tracking-widest">Кладовая</span>
                    <h2 class="text-2xl text-warm-ink font-serif font-bold">Выберите имеющиеся ингредиенты</h2>
                </div>
                
                <button class="px-4 py-2 bg-white hover:bg-paper text-terracotta border border-[#D4B996] font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer self-start sm:self-auto" onclick="psClear()" type="button">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    <span>Очистить выбор</span>
                </button>
            </div>

            <!-- Add Custom Ingredient Form -->
            <form class="mb-6 relative flex items-center" id="customIngredientForm" onsubmit="psAdd(event)">
                <div class="w-full flex items-stretch border border-[#D4B996] bg-[#FFF9F4] shadow-xs focus-within:border-terracotta rounded-xl overflow-hidden">
                    <input autocomplete="off" class="w-full bg-transparent font-sans text-xs sm:text-sm text-warm-ink placeholder-[#8a726a] px-4 py-3 border-0 focus:ring-0 outline-none" id="customIngredientInput" list="ps-names" placeholder="Добавить свой продукт (например, Розмарин)..." type="text">
                    <button aria-label="Добавить" class="shrink-0 px-5 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition-colors cursor-pointer" type="submit">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span>Добавить</span>
                    </button>
                </div>
            </form>

            <!-- Ingredients Grid -->
           <datalist id="ps-names">
                <?php foreach ($allNames as $n): ?><option value="<?= Html::encode($n) ?>"><?php endforeach; ?>
            </datalist>
            <?php if (!$pantry): ?>
                <p class="text-sm text-warm-muted mb-4">В базе пока нет ингредиентов — добавьте свои продукты в поле выше.</p>
            <?php endif; ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8" id="ingredientsGrid">
                <?php foreach ($pantry as $ing): ?>
                <div class="pantry-item flex items-center justify-between p-3 bg-white hover:bg-[#FFF9F4] border border-[#D4B996]/50 transition-colors rounded-xl" data-name="<?= Html::encode($ing->name) ?>">
                    <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                        <input class="custom-checkbox ps-check" type="checkbox">
                        <span class="font-sans text-sm font-medium text-warm-ink truncate"><?= Html::encode($ing->name) ?></span>
                    </label>
                    <button type="button" onclick="psPromptDelete(this)" class="ml-2 text-warm-muted hover:text-terracotta p-1 transition-colors cursor-pointer" title="Убрать продукт">
                        <span class="material-symbols-outlined text-base leading-none">close</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recipe Results Showcase Section -->
        <div class="flex flex-col gap-6" id="resultsSection">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2 border-b border-dashed border-[#D4B996]">
                <div>
                    <span class="font-mono text-[11px] text-warm-muted uppercase tracking-widest">Результаты подбора</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-warm-ink font-serif" id="ps-title">Отметьте продукты, чтобы подобрать рецепты</h2>
                </div>
                
                <!-- Sort / Filter Ribbon Controls -->
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs text-warm-muted hidden sm:inline">Сортировка:</span>
                    <button class="px-3.5 py-2 bg-terracotta text-white font-sans text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer rounded-xl" id="sortByMatch" onclick="psSort('match')" type="button">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span>По совпадению</span>
                    </button>
                    <button class="px-3.5 py-2 bg-white hover:bg-paper text-warm-muted hover:text-warm-ink border border-[#D4B996] font-sans text-xs font-semibold transition-colors flex items-center gap-1.5 cursor-pointer rounded-xl" id="sortByTime" onclick="psSort('time')" type="button">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        <span>По времени</span>
                    </button>
                </div>
            </div>

            <!-- Recipe Cards Container (Dynamic) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="ps-results">
                <!-- Dynamically populated by JS -->
            </div>

            <!-- Bottom Return to Top Button -->
            <div class="mt-8 flex justify-center">
                <button class="px-6 py-3 bg-[#FAF4EB] hover:bg-paper border border-[#D4B996] text-warm-ink font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-2 cursor-pointer" onclick="psTop()" type="button">
                    <span class="material-symbols-outlined text-base">arrow_upward</span>
                    <span>Вверх к списку продуктов</span>
                </button>
            </div>
        </div>

    </div>
</main>

<!-- Delete Confirmation Modal -->
<div aria-labelledby="modal-title" aria-modal="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-warm-ink/60 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none" id="delete-modal" role="dialog">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-scrapbook border-2 border-dashed border-[#D4B996] p-6 sm:p-7 transform transition-transform duration-200 scale-95" id="delete-modal-panel">
        <div class="washi-tape washi-sage -top-3.5 left-1/2 -translate-x-1/2 w-28 h-6 -rotate-1 pointer-events-none"></div>
        
        <div class="flex items-start justify-between mb-4 pt-1">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-terracotta text-lg">bookmark</span>
                <span class="font-mono text-xs font-bold text-terracotta uppercase tracking-wider">Удаление продукта</span>
            </div>
            <button aria-label="Закрыть" class="text-warm-muted hover:text-warm-ink transition-colors p-1 cursor-pointer" onclick="psCloseDelete()" type="button">
                <span class="material-symbols-outlined text-lg leading-none">close</span>
            </button>
        </div>

        <h3 class="text-2xl font-serif font-bold text-warm-ink mb-3" id="modal-title">
            Удалить продукт из кладовой?
        </h3>

        <div class="bg-[#FFF9F4] border-l-4 border-l-terracotta border border-[#D4B996] p-3.5 mb-4 rounded-xl shadow-xs">
            <span class="block font-mono text-[10px] text-warm-muted uppercase tracking-wider mb-1">Продукт:</span>
            <p class="font-sans text-sm font-bold text-warm-ink" id="modal-item-name">«Томаты спелые»</p>
        </div>

        <p class="font-sans text-xs text-warm-muted leading-relaxed mb-6">
            Вы сможете добавить его снова в любой момент с помощью формы выше.
        </p>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-dashed border-[#D4B996]">
            <button class="px-4 py-2 border border-[#D4B996] bg-white hover:bg-paper text-warm-ink font-sans text-xs font-bold transition-colors rounded-xl cursor-pointer" onclick="psCloseDelete()" type="button">
                Отмена
            </button>
            <button class="px-5 py-2 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer" onclick="psConfirmDelete()" type="button">
                <span class="material-symbols-outlined text-sm">delete</span>
                <span>Удалить</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification Box -->
<div class="fixed bottom-6 right-6 z-50 bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm hidden rounded-2xl" id="auth-toast">
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-terracotta text-2xl shrink-0">verified</span>
        <div class="flex flex-col">
            <span class="font-serif font-bold text-base text-warm-ink" id="toast-title">Folk Menu</span>
            <span class="font-sans text-xs text-warm-muted mt-0.5" id="toast-message">Уведомление</span>
        </div>
    </div>
</div>

<script>
(function () {
    var RECIPES = <?= json_encode($recipesData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
    var ALL_NAMES = <?= json_encode($allNames, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
    var CFG = {
        guest: <?= Yii::$app->user->isGuest ? 'true' : 'false' ?>,
        loginUrl: <?= json_encode(Url::to(['site/login'])) ?>,
        cartUrl: <?= json_encode(Url::to(['site/add-to-shopping-list'])) ?>,
        csrf: <?= json_encode(Yii::$app->request->csrfToken) ?>
    };
    var sortMode = 'match';
    var pendingRow = null;

    var grid = document.getElementById('ingredientsGrid');
    var results = document.getElementById('ps-results');

    function norm(s) { return String(s || '').toLowerCase().replace(/ё/g, 'е').replace(/\s+/g, ' ').trim(); }
    function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function plural(n, a, b, c) {
        var m100 = n % 100, m10 = n % 10;
        if (m100 > 10 && m100 < 20) return c;
        if (m10 > 1 && m10 < 5) return b;
        if (m10 === 1) return a;
        return c;
    }
    function toast(msg) {
        var t = document.getElementById('auth-toast'), m = document.getElementById('toast-message');
        if (!t || !m) return;
        m.textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { t.classList.add('hidden'); }, 3500);
    }
    function fmtTime(min) {
        if (!min) return '';
        if (min < 60) return min + ' мин';
        var h = Math.floor(min / 60), m = min % 60;
        return h + ' ч' + (m ? ' ' + m + ' мин' : '');
    }

    // продукт из кладовой подходит к ингредиенту рецепта
    function matches(pantryName, ingName) {
        var a = norm(pantryName), b = norm(ingName);
        if (!a || !b) return false;
        if (a === b) return true;
        return a.length >= 3 && b.length >= 3 && (a.indexOf(b) !== -1 || b.indexOf(a) !== -1);
    }

    function selected() {
        var list = [];
        document.querySelectorAll('.pantry-item').forEach(function (row) {
            var cb = row.querySelector('.ps-check');
            var on = cb && cb.checked;
            row.classList.toggle('bg-[#FFF9F4]', !!on);
            row.classList.toggle('bg-white', !on);
            if (on) list.push(row.dataset.name);
        });
        return list;
    }

    function update() {
        var have = selected();
        document.getElementById('ps-counter').textContent = have.length + ' ' + plural(have.length, 'позиция', 'позиции', 'позиций');
        var title = document.getElementById('ps-title');

        if (!have.length) {
            title.textContent = 'Отметьте продукты, чтобы подобрать рецепты';
            results.innerHTML = '<div class="md:col-span-2 text-center bg-white border-2 border-dashed border-[#D4B996] rounded-3xl p-8 text-sm text-warm-muted">Выберите продукты, которые есть у вас дома, — архив покажет подходящие рецепты.</div>';
            return;
        }

        var evaluated = [];
        RECIPES.forEach(function (r) {
            var required = r.ingredients.filter(function (i) { return !i.optional; });
            if (!required.length) return;
            var matched = 0, missing = [];
            required.forEach(function (ing) {
                if (have.some(function (h) { return matches(h, ing.name); })) matched++;
                else missing.push(ing);
            });
            if (!matched) return;
            evaluated.push({ r: r, matched: matched, total: required.length, missing: missing, percent: Math.round(matched / required.length * 100) });
        });

        evaluated.sort(function (x, y) {
            if (sortMode === 'time') return (x.r.time || 1e9) - (y.r.time || 1e9);
            return (y.percent - x.percent) || (x.missing.length - y.missing.length) || ((x.r.time || 1e9) - (y.r.time || 1e9));
        });

        var n = evaluated.length;
        title.textContent = n
            ? plural(n, 'Найден ', 'Найдено ', 'Найдено ') + n + ' ' + plural(n, 'рецепт', 'рецепта', 'рецептов') + ' под ваши продукты'
            : 'Пока ничего не нашлось';
        render(evaluated);
    }

    function render(list) {
        if (!list.length) {
            results.innerHTML = '<div class="md:col-span-2 text-center bg-white border-2 border-dashed border-[#D4B996] rounded-3xl p-8"><span class="material-symbols-outlined text-4xl text-terracotta">search_off</span><h3 class="font-serif text-2xl font-bold text-warm-ink mt-2">Подходящих рецептов нет</h3><p class="font-sans text-sm text-warm-muted mt-1">Отметьте больше продуктов или добавьте свои.</p></div>';
            return;
        }
        results.innerHTML = '';
        list.forEach(function (e) {
            var r = e.r, full = e.missing.length === 0;
            var missingNames = e.missing.map(function (m) { return m.name; });
            var badgeBg = full ? 'bg-[#EDF4EF] text-sage border-sage/40' : 'bg-[#FDF0EB] text-terracotta border-terracotta/40';
            var badge = full
                ? '✓ Все ' + e.total + ' ' + plural(e.total, 'ингредиент', 'ингредиента', 'ингредиентов') + ' в наличии (100%)'
                : e.matched + ' из ' + e.total + ' (Не хватает: ' + missingNames.join(', ') + ')';
            var img = r.photo
                ? '<img alt="' + esc(r.title) + '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="' + esc(r.photo) + '">'
                : '<div class="w-full h-full flex items-center justify-center"><span class="material-symbols-outlined text-6xl text-[#dec0b7]">restaurant_menu</span></div>';
            var meta = '';
            if (r.time) meta += '<span class="flex items-center gap-1"><span class="material-symbols-outlined text-terracotta text-sm">schedule</span>' + fmtTime(r.time) + '</span>';
            meta += '<span class="flex items-center gap-1"><span class="material-symbols-outlined text-sage text-sm">room_service</span>' + r.servings + ' ' + plural(r.servings, 'порция', 'порции', 'порций') + '</span>';
            if (r.calories) meta += '<span class="flex items-center gap-1"><span class="material-symbols-outlined text-honey text-sm">local_fire_department</span>' + r.calories + ' ккал</span>';

            var card = document.createElement('div');
            card.className = 'relative bg-white border border-[#D4B996] shadow-xs flex flex-col justify-between rounded-3xl overflow-visible group';
            card.innerHTML =
                '<div class="washi-tape ' + (full ? 'washi-sage' : 'washi-terracotta') + ' -top-3 left-8 w-32 h-6 flex items-center justify-center"><span class="font-mono text-[10px] text-white font-bold tracking-widest uppercase">' + (full ? 'Идеально' : 'Докупить') + '</span></div>' +
                '<div class="relative h-56 w-full overflow-hidden bg-[#FFF9F4] rounded-t-3xl">' + img + '</div>' +
                '<div class="p-6 flex-1 flex flex-col justify-between gap-4"><div>' +
                '<span class="inline-block px-2.5 py-0.5 ' + badgeBg + ' font-mono text-xs font-bold rounded-md mb-2 border">' + esc(badge) + '</span>' +
                '<h3 class="text-xl font-serif font-bold text-warm-ink">' + esc(r.title) + '</h3>' +
                (r.description ? '<p class="font-sans text-xs text-warm-muted mt-1 leading-relaxed">' + esc(r.description) + '</p>' : '') +
                '<div class="flex flex-wrap items-center gap-4 text-warm-muted font-mono text-xs mt-3">' + meta + '</div></div>' +
                '<div class="pt-4 border-t border-dashed border-[#D4B996] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">' +
                (full ? '<div></div>' : '<button class="ps-cart px-3 py-2 bg-white hover:bg-paper text-warm-ink border border-[#D4B996] font-sans text-xs font-medium transition-colors flex items-center justify-center gap-1.5 rounded-xl cursor-pointer" type="button"><span class="material-symbols-outlined text-sm text-terracotta">shopping_cart</span><span>Добавить недостающие</span></button>') +
                '<a href="' + esc(r.url) + '" class="px-5 py-2.5 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-all rounded-xl shadow-xs text-center flex items-center justify-center gap-1.5"><span>Смотреть рецепт</span><span class="material-symbols-outlined text-sm">arrow_forward</span></a>' +
                '</div></div>';
            var cart = card.querySelector('.ps-cart');
            if (cart) cart.addEventListener('click', function () { addMissing(r, e.missing); });
            results.appendChild(card);
        });
    }

    function addMissing(r, missing) {
        if (CFG.guest) { window.location.href = CFG.loginUrl; return; }
        var body = new URLSearchParams();
        missing.forEach(function (m) { body.append('ingredient_ids[]', m.id); });
        body.append('servings', r.servings);
        body.append('_csrf', CFG.csrf);
        fetch(CFG.cartUrl + '?id=' + r.id, { method: 'POST', body: body, headers: { 'X-CSRF-Token': CFG.csrf, 'Accept': 'application/json' } })
            .then(function (res) { if (!res.ok) throw new Error(); return res.json(); })
            .then(function (res) { toast('В список покупок добавлено позиций: ' + res.added); })
            .catch(function () { toast('Не удалось добавить в список покупок'); });
    }

    // ---- обработчики страницы ----
    grid.addEventListener('change', function (e) { if (e.target.classList.contains('ps-check')) update(); });

    window.psSort = function (mode) {
        sortMode = mode;
        var on = 'px-3.5 py-2 bg-terracotta text-white font-sans text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer rounded-xl';
        var off = 'px-3.5 py-2 bg-white hover:bg-paper text-warm-muted hover:text-warm-ink border border-[#D4B996] font-sans text-xs font-semibold transition-colors flex items-center gap-1.5 cursor-pointer rounded-xl';
        document.getElementById('sortByMatch').className = mode === 'match' ? on : off;
        document.getElementById('sortByTime').className = mode === 'time' ? on : off;
        update();
    };
    window.psClear = function () {
        document.querySelectorAll('.ps-check').forEach(function (cb) { cb.checked = false; });
        update();
        toast('Выбор ингредиентов очищен');
    };
    window.psTop = function () {
        var t = document.getElementById('inventory-section');
        if (t) t.scrollIntoView({ behavior: 'smooth' });
    };
    window.psAdd = function (e) {
        e.preventDefault();
        var input = document.getElementById('customIngredientInput');
        var val = input.value.replace(/\s+/g, ' ').trim();
        if (!val) return;
        // если такой продукт уже в списке — просто отмечаем его
        var existing = Array.prototype.find.call(document.querySelectorAll('.pantry-item'), function (r) { return norm(r.dataset.name) === norm(val); });
        if (existing) {
            existing.querySelector('.ps-check').checked = true;
            input.value = '';
            update();
            toast('«' + existing.dataset.name + '» отмечен');
            return;
        }
        // каноническое написание из базы, если оно есть
        var canon = ALL_NAMES.filter(function (n) { return norm(n) === norm(val); })[0] || val;
        var row = document.createElement('div');
        row.className = 'pantry-item flex items-center justify-between p-3 bg-[#FFF9F4] hover:bg-[#FAF4EB] border border-[#D4B996]/50 transition-colors rounded-xl';
        row.dataset.name = canon;
        row.innerHTML =
            '<label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0"><input checked class="custom-checkbox ps-check" type="checkbox"><span class="font-sans text-sm font-medium text-warm-ink truncate">' + esc(canon) + '</span></label>' +
            '<button type="button" onclick="psPromptDelete(this)" class="ml-2 text-warm-muted hover:text-terracotta p-1 transition-colors cursor-pointer" title="Убрать продукт"><span class="material-symbols-outlined text-base leading-none">close</span></button>';
        grid.prepend(row);
        input.value = '';
        update();
        toast('Продукт «' + canon + '» добавлен в кладовую');
    };

    // удаление продукта из кладовой (с подтверждением)
    window.psPromptDelete = function (btn) {
        var row = btn.closest('.pantry-item');
        if (!row) return;
        pendingRow = row;
        document.getElementById('modal-item-name').textContent = '«' + row.dataset.name + '»';
        var modal = document.getElementById('delete-modal'), panel = document.getElementById('delete-modal-panel');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        panel.classList.remove('scale-95');
    };
    window.psCloseDelete = function () {
        pendingRow = null;
        document.getElementById('delete-modal').classList.add('opacity-0', 'pointer-events-none');
        document.getElementById('delete-modal-panel').classList.add('scale-95');
    };
    window.psConfirmDelete = function () {
        if (pendingRow) {
            pendingRow.remove();
            update();
            toast('Продукт убран из кладовой');
        }
        window.psCloseDelete();
    };

    update();
})();
</script>
