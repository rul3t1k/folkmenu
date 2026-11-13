<?php
use yii\helpers\Url;
use yii\helpers\Html;

/** @var app\models\ShoppingListItem[] $items */

$total = count($items);
$bought = count(array_filter($items, function ($i) { return $i->is_bought; }));
$plural = function ($n) {
    $a = $n % 100; $b = $n % 10;
    if ($a > 10 && $a < 20) return 'позиций';
    if ($b > 1 && $b < 5) return 'позиции';
    if ($b == 1) return 'позиция';
    return 'позиций';
};
$fmtQty = function ($q) { return rtrim(rtrim(number_format((float)$q, 2, '.', ''), '0'), '.'); };
?>
<!-- ================= MAIN CONTENT ================= -->
<main class="w-full pt-28 pb-16 flex-1">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Title Banner -->
        <div class="relative mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#EDF4EF] text-sage font-mono text-xs tracking-wider uppercase shadow-xs -rotate-1 mb-2 border border-sage/20 rounded-md">
                    <span class="material-symbols-outlined text-sm">shopping_basket</span>
                    Кладовая и припасы
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-warm-ink font-serif">Список покупок</h1>
                <p class="font-sans text-xs sm:text-sm text-warm-muted max-w-xl mt-1.5">
                    Сводный перечень продуктов из ваших рецептов. Отмечайте купленное в магазине или добавляйте свои продукты.
                </p>
            </div>

            <!-- Header Counter Ticket -->
            <div class="relative bg-[#FFF9F4] px-4 py-3 rounded-2xl shadow-xs border-2 border-dashed border-[#D4B996] flex items-center gap-3 shrink-0">
                <div class="w-10 h-10 bg-terracotta/10 flex items-center justify-center text-terracotta rounded-xl">
                    <span class="material-symbols-outlined text-2xl">receipt_long</span>
                </div>
                <div>
                    <span class="block font-mono text-[11px] text-warm-muted uppercase tracking-wider">Всего в списке</span>
                    <span class="font-mono text-lg font-bold text-terracotta" id="sl-counter"><?= $total ?> <?= $plural($total) ?></span>
                </div>
            </div>
        </div>

        <!-- Notepad Container -->
        <div class="relative bg-white rounded-3xl shadow-scrapbook p-6 sm:p-8 border border-[#D4B996]">
            
            <!-- Washi Tapes Decor -->
            <div class="washi-tape washi-sage -top-3 left-10 w-32 h-6 -rotate-2"></div>
            <div class="washi-tape washi-honey -top-2 right-12 w-24 h-5 rotate-3"></div>

            <!-- Notepad Binding Simulation -->
            <div class="flex justify-between items-center pb-4 border-b-2 border-dashed border-[#D4B996] mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-3.5 h-3.5 rounded-full bg-[#FAF4EB] border border-[#D4B996] shadow-inner inline-block"></span>
                    <span class="w-3.5 h-3.5 rounded-full bg-[#FAF4EB] border border-[#D4B996] shadow-inner inline-block"></span>
                    <span class="w-3.5 h-3.5 rounded-full bg-[#FAF4EB] border border-[#D4B996] shadow-inner inline-block"></span>
                    <span class="w-3.5 h-3.5 rounded-full bg-[#FAF4EB] border border-[#D4B996] shadow-inner inline-block"></span>
                </div>
                <span class="font-mono text-xs text-warm-muted uppercase font-bold tracking-wider">Ревизия припасов</span>
            </div>

            <!-- Add Custom Item Form -->
            <form class="mb-6 relative flex items-center" id="sl-add-form">
                <div class="w-full flex items-stretch border border-[#D4B996] bg-[#FFF9F4] shadow-xs focus-within:border-terracotta rounded-xl overflow-hidden">
                    <input autocomplete="off" class="w-full bg-transparent font-sans text-xs sm:text-sm text-warm-ink placeholder-[#8a726a] px-4 py-3 border-0 focus:ring-0 outline-none" id="sl-new-item" placeholder="Вписать свой ингредиент (например, Стручковая ваниль)..." type="text">
                    <button aria-label="Добавить ингредиент" class="shrink-0 px-5 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-1.5 transition-colors cursor-pointer" type="submit">
                        <span class="material-symbols-outlined text-base">add</span>
                        <span>Добавить</span>
                    </button>
                </div>
            </form>

            <!-- Unified Ingredients List Container -->
            <div class="space-y-2 mb-6" id="sl-list">
                <?php foreach ($items as $it):
                    $name = $it->ingredient ? $it->ingredient->name : 'Ингредиент';
                    $hasQty = $it->quantity !== null;
                    $unit = (string)$it->unit;
                    $step = in_array($unit, ['г', 'мл'], true) ? 50 : (in_array($unit, ['кг', 'л'], true) ? 0.5 : 1);
                ?>
                <div class="item-row group flex items-center justify-between p-3 <?= $it->is_bought ? 'bg-[#FFF9F4]' : 'bg-white' ?> hover:bg-[#FAF4EB] transition-colors border border-[#D4B996]/50 rounded-xl" data-id="<?= (int)$it->id ?>" data-checked="<?= $it->is_bought ? 'true' : 'false' ?>" data-name="<?= Html::encode($name) ?>">
                    <div class="flex items-center gap-3 min-w-0">
                        <input <?= $it->is_bought ? 'checked' : '' ?> class="custom-checkbox sl-check" type="checkbox">
                        <div class="min-w-0">
                            <p class="item-title font-sans text-sm text-warm-ink font-medium truncate <?= $it->is_bought ? 'line-through opacity-60' : '' ?>"><?= Html::encode($name) ?><?php if (!$hasQty && $unit !== ''): ?> — <?= Html::encode($unit) ?><?php endif; ?></p>
                            <span class="font-mono text-[11px] text-warm-muted block truncate"><?= $it->recipe ? 'для «' . Html::encode($it->recipe->title) . '»' : 'добавлено вручную' ?></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <?php if ($hasQty): ?>
                        <div class="flex items-center gap-1 bg-[#FFF9F4] border border-[#D4B996] rounded-lg px-1 py-0.5 shadow-xs">
                            <button class="sl-minus text-xs text-warm-ink hover:text-terracotta px-1.5 font-bold transition-colors select-none" data-step="<?= $step ?>" type="button">−</button>
                            <input class="sl-qty w-14 text-center font-mono text-xs font-bold text-terracotta bg-transparent border-0 focus:ring-0 p-0" min="0.01" step="any" type="number" value="<?= $fmtQty($it->quantity) ?>">
                            <button class="sl-plus text-xs text-warm-ink hover:text-terracotta px-1.5 font-bold transition-colors select-none" data-step="<?= $step ?>" type="button">+</button>
                            <?php if ($unit !== ''): ?><span class="font-mono text-[11px] text-warm-muted pr-1"><?= Html::encode($unit) ?></span><?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <button class="sl-delete text-warm-muted hover:text-terracotta p-1 transition-colors cursor-pointer" title="Удалить" type="button">
                            <span class="material-symbols-outlined text-lg leading-none">close</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <p class="text-center text-sm text-warm-muted py-6 <?= $items ? 'hidden' : '' ?>" id="sl-empty">Список пуст. Добавьте продукты вручную или из карточки рецепта.</p>
            </div>
            <!-- Summary Footer Bar -->
            <div class="pt-4 border-t-2 border-dashed border-[#D4B996] flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#FFF9F4] p-4 rounded-2xl">
                <div class="flex items-center gap-2 font-mono text-xs text-warm-muted">
                    <span class="material-symbols-outlined text-sage text-base">checklist_rtl</span>
                    <span class="font-bold" id="sl-summary">Всего: <?= $total ?> • Куплено: <?= $bought ?> • Осталось: <?= $total - $bought ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <button class="px-4 py-2 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer" id="sl-clear" type="button">
                        <span class="material-symbols-outlined text-sm">delete</span>
                        <span>Убрать купленное</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Delete Confirmation Modal (Адаптировано под единый крафтовый стиль) -->
<div aria-labelledby="sl-modal-title" aria-modal="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-warm-ink/60 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none" id="sl-delete-modal" role="dialog">
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-scrapbook border-2 border-dashed border-[#D4B996] p-6 sm:p-7 transform transition-transform duration-200 scale-95" id="sl-delete-panel">
        <div class="washi-tape washi-sage -top-3.5 left-1/2 -translate-x-1/2 w-28 h-6 -rotate-1 pointer-events-none"></div>
        
        <div class="flex items-start justify-between mb-4 pt-1">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-terracotta text-lg">bookmark</span>
                <span class="font-mono text-xs font-bold text-terracotta uppercase tracking-wider">Ревизия припасов</span>
            </div>
            <button aria-label="Закрыть окно" class="text-warm-muted hover:text-warm-ink transition-colors p-1 cursor-pointer" id="sl-cancel-x" type="button">
                <span class="material-symbols-outlined text-lg leading-none">close</span>
            </button>
        </div>

        <h3 class="text-2xl font-serif font-bold text-warm-ink mb-3" id="sl-modal-title">
            Удалить позицию из списка?
        </h3>

        <div class="bg-[#FFF9F4] border-l-4 border-l-terracotta border border-[#D4B996] p-3.5 mb-4 rounded-xl shadow-xs">
            <span class="block font-mono text-[10px] text-warm-muted uppercase tracking-wider mb-1">Выбранная позиция:</span>
            <p class="font-sans text-sm font-bold text-warm-ink" id="sl-modal-name">«Яблоки антоновские»</p>
        </div>

        <p class="font-sans text-xs text-warm-muted leading-relaxed mb-6">
            Вы всегда сможете добавить её снова вручную или из карточки рецепта.
        </p>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-dashed border-[#D4B996]">
            <button class="px-4 py-2 border border-[#D4B996] bg-white hover:bg-paper text-warm-ink font-sans text-xs font-bold transition-colors rounded-xl cursor-pointer" id="sl-cancel-btn" type="button">
                Отмена
            </button>
            <button class="px-5 py-2 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-colors rounded-xl shadow-xs flex items-center gap-1.5 cursor-pointer" id="sl-confirm-btn" type="button">
                <span class="material-symbols-outlined text-sm">delete</span>
                <span>Удалить</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="fixed bottom-6 right-6 z-50 bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm hidden rounded-2xl" id="sl-toast">
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-terracotta text-2xl shrink-0">verified</span>
        <div class="flex flex-col">
            <span class="font-serif font-bold text-base text-warm-ink">Folk Menu</span>
            <span class="font-sans text-xs text-warm-muted mt-0.5" id="sl-toast-msg"></span>
        </div>
    </div>
</div>

<script>
(function () {
    var csrf = <?= json_encode(Yii::$app->request->csrfToken) ?>;
    var U = {
        toggle: <?= json_encode(Url::to(['site/toggle-shopping-item'])) ?>,
        update: <?= json_encode(Url::to(['site/update-shopping-item'])) ?>,
        del: <?= json_encode(Url::to(['site/delete-shopping-item'])) ?>,
        clear: <?= json_encode(Url::to(['site/clear-bought-items'])) ?>,
        add: <?= json_encode(Url::to(['site/add-shopping-item'])) ?>
    };
    var list = document.getElementById('sl-list');

    function toast(msg) {
        var t = document.getElementById('sl-toast');
        document.getElementById('sl-toast-msg').textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { t.classList.add('hidden'); }, 3500);
    }
    function post(url, id, data) {
        var body = new URLSearchParams(data || {});
        body.append('_csrf', csrf);
        return fetch(url + (id ? (url.indexOf('?') === -1 ? '?' : '&') + 'id=' + id : ''), { method: 'POST', body: body, headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' } })
            .then(function (r) { return r.json().then(function (j) { if (!r.ok) throw new Error(j.message || r.status); return j; }); });
    }
    function plural(n) {
        var a = n % 100, b = n % 10;
        if (a > 10 && a < 20) return 'позиций';
        if (b > 1 && b < 5) return 'позиции';
        if (b === 1) return 'позиция';
        return 'позиций';
    }
    function stats() {
        var rows = list.querySelectorAll('.item-row');
        var total = rows.length, bought = 0;
        rows.forEach(function (r) { if (r.dataset.checked === 'true') bought++; });
        document.getElementById('sl-counter').textContent = total + ' ' + plural(total);
        document.getElementById('sl-summary').textContent = 'Всего: ' + total + ' • Куплено: ' + bought + ' • Осталось: ' + (total - bought);
        document.getElementById('sl-empty').classList.toggle('hidden', total > 0);
    }
    function paint(row, checked) {
        row.dataset.checked = checked ? 'true' : 'false';
        var t = row.querySelector('.item-title');
        t.classList.toggle('line-through', checked);
        t.classList.toggle('opacity-60', checked);
        row.classList.toggle('bg-[#FFF9F4]', checked);
        row.classList.toggle('bg-white', !checked);
    }

    // отметка «куплено»
    list.addEventListener('change', function (e) {
        var cb = e.target.closest('.sl-check');
        if (!cb) return;
        var row = cb.closest('.item-row');
        paint(row, cb.checked);
        stats();
        post(U.toggle, row.dataset.id, { bought: cb.checked ? 1 : 0 }).catch(function () {
            cb.checked = !cb.checked; paint(row, cb.checked); stats(); toast('Не удалось сохранить отметку');
        });
    });

    // количество: кнопки ± и ручной ввод
    var timers = {};
    function saveQty(row, input) {
        var v = parseFloat(String(input.value).replace(',', '.'));
        if (!(v > 0)) { input.value = input.dataset.last || input.defaultValue; return; }
        clearTimeout(timers[row.dataset.id]);
        timers[row.dataset.id] = setTimeout(function () {
            post(U.update, row.dataset.id, { quantity: v }).then(function () { input.dataset.last = input.value; })
                .catch(function (err) { input.value = input.dataset.last || input.defaultValue; toast(err.message || 'Не удалось изменить количество'); });
        }, 350);
    }
    list.addEventListener('click', function (e) {
        var btn = e.target.closest('.sl-minus, .sl-plus');
        if (btn) {
            var row = btn.closest('.item-row'), input = row.querySelector('.sl-qty');
            var step = parseFloat(btn.dataset.step) || 1;
            var cur = parseFloat(input.value) || 0;
            var next = Math.round((cur + (btn.classList.contains('sl-plus') ? step : -step)) * 100) / 100;
            if (next <= 0) return;
            if (!input.dataset.last) input.dataset.last = input.value;
            input.value = next;
            saveQty(row, input);
            return;
        }
        var del = e.target.closest('.sl-delete');
        if (del) openDelete(del.closest('.item-row'));
    });
    list.addEventListener('change', function (e) {
        var input = e.target.closest('.sl-qty');
        if (input) saveQty(input.closest('.item-row'), input);
    });

    // удаление с подтверждением
    var modal = document.getElementById('sl-delete-modal');
    var panel = document.getElementById('sl-delete-panel');
    var pending = null;
    function openDelete(row) {
        pending = row;
        document.getElementById('sl-modal-name').textContent = '«' + row.dataset.name + '»';
        modal.classList.remove('opacity-0', 'pointer-events-none');
        panel.classList.remove('scale-95');
    }
    function closeDelete() {
        pending = null;
        modal.classList.add('opacity-0', 'pointer-events-none');
        panel.classList.add('scale-95');
    }
    document.getElementById('sl-cancel-btn').addEventListener('click', closeDelete);
    document.getElementById('sl-cancel-x').addEventListener('click', closeDelete);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeDelete(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDelete(); });
    document.getElementById('sl-confirm-btn').addEventListener('click', function () {
        var row = pending;
        if (!row) return;
        post(U.del, row.dataset.id).then(function () {
            row.remove(); stats(); closeDelete(); toast('Позиция удалена');
        }).catch(function () { closeDelete(); toast('Не удалось удалить позицию'); });
    });

    // убрать купленное
    document.getElementById('sl-clear').addEventListener('click', function () {
        var bought = list.querySelectorAll('.item-row[data-checked="true"]');
        if (!bought.length) { toast('Купленных позиций пока нет'); return; }
        post(U.clear).then(function (r) {
            bought.forEach(function (row) { row.remove(); });
            stats(); toast('Убрано купленного: ' + r.removed);
        }).catch(function () { toast('Не удалось очистить список'); });
    });

    // добавить свой продукт
    document.getElementById('sl-add-form').addEventListener('submit', function (e) {
        e.preventDefault();
        var input = document.getElementById('sl-new-item');
        var name = input.value.trim();
        if (!name) return;
        post(U.add, null, { name: name }).then(function (r) {
            if (r.duplicate) { toast('Этот продукт уже есть в списке'); return; }
            window.location.reload();
        }).catch(function (err) { toast(err.message || 'Не удалось добавить продукт'); });
    });

    stats();
})();
</script>
