/**
 * Окно «Передать рецепт в архив» — создание и редактирование рецепта.
 * Данные приходят из window.RECIPE_FORM_CONFIG (см. views/site/profile.php).
 */
(function () {
    var cfg = window.RECIPE_FORM_CONFIG || { recipes: {} };
    var modal = document.getElementById('recipe-modal');
    var form = document.getElementById('recipe-form');
    if (!modal || !form) return;

    var ingBox = document.getElementById('rf-ingredients');
    var stepBox = document.getElementById('rf-steps');
    var errBox = document.getElementById('rf-errors');
    var photoInput = document.getElementById('rf-photo');
    var placeholder = document.getElementById('rf-upload-placeholder');
    var preview = document.getElementById('rf-preview');
    var previewImg = document.getElementById('rf-preview-img');
    var removeFlag = document.getElementById('rf-remove-photo-flag');
    var editId = null;
    var counter = 0; // уникальные индексы для имён полей

    function el(html) {
        var t = document.createElement('template');
        t.innerHTML = html.trim();
        return t.content.firstChild;
    }
    function esc(v) {
        return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }

    function renumber() {
        ingBox.querySelectorAll('.ingredient-item').forEach(function (r, i) {
            r.querySelector('.rf-num').textContent = (i < 9 ? '0' : '') + (i + 1) + '.';
        });
        stepBox.querySelectorAll('.step-item').forEach(function (r, i) {
            r.querySelector('.step-number-label').textContent = 'ШАГ ' + (i + 1) + ' •';
        });
    }

    function addIngredient(d) {
        d = d || {};
        var i = counter++;
        var row = el(
            '<div class="flex flex-wrap sm:flex-nowrap items-center gap-2 bg-white border border-[#E5D2BE] p-2 shadow-xs ingredient-item rounded-xl">' +
            '<span class="font-mono text-xs text-warm-muted px-1 font-bold rf-num"></span>' +
            '<input class="flex-1 min-w-[8rem] bg-transparent border-0 p-0 text-sm font-sans text-warm-ink focus:outline-none" list="rf-ingredient-names" name="ingredients[' + i + '][name]" placeholder="Ингредиент" type="text" maxlength="255" value="' + esc(d.name) + '">' +
            '<input class="w-24 bg-[#FFF9F4] border border-[#D4B996] px-2 py-1 text-xs font-mono font-bold text-right focus:outline-none rounded-md" name="ingredients[' + i + '][quantity]" placeholder="Кол-во" type="number" min="0" step="0.01" value="' + esc(d.quantity) + '">' +
            '<input class="w-24 bg-[#FFF9F4] border border-[#D4B996] px-2 py-1 text-xs font-mono font-bold focus:outline-none rounded-md" list="rf-units" name="ingredients[' + i + '][unit]" placeholder="Ед." type="text" maxlength="255" value="' + esc(d.unit) + '">' +
            '<button class="p-1 text-warm-muted hover:text-terracotta rounded-md" type="button" data-remove><span class="material-symbols-outlined text-base">close</span></button>' +
            '</div>');
        ingBox.appendChild(row);
        renumber();
    }

    function addStep(d) {
        d = d || {};
        var i = counter++;
        var row = el(
            '<div class="bg-[#FAF4EB] border border-[#D4B996] p-3.5 flex flex-col gap-2.5 step-item rounded-2xl">' +
            '<div class="flex flex-wrap items-center justify-between gap-2">' +
            '<span class="font-mono text-xs font-bold text-terracotta uppercase step-number-label"></span>' +
            '<input class="flex-1 min-w-[8rem] bg-white border border-[#E5D2BE] px-2.5 py-1 text-xs font-mono font-bold text-warm-ink focus:outline-none rounded-lg" name="steps[' + i + '][title]" placeholder="Наименование шага" type="text" maxlength="255" value="' + esc(d.title) + '">' +
            '<input class="w-24 bg-white border border-[#E5D2BE] px-2 py-1 text-xs font-mono font-bold text-right focus:outline-none rounded-lg" name="steps[' + i + '][duration_min]" placeholder="мин" title="Длительность, мин" type="number" min="0" step="1" value="' + esc(d.duration_min) + '">' +
            '<button class="p-1 text-warm-muted hover:text-terracotta rounded-md" type="button" data-remove><span class="material-symbols-outlined text-base">close</span></button>' +
            '</div>' +
            '<textarea class="w-full bg-white border border-[#E5D2BE] p-2.5 text-sm text-warm-ink focus:outline-none rounded-xl font-sans" name="steps[' + i + '][description]" placeholder="Опишите этап..." rows="2">' + esc(d.description) + '</textarea>' +
            '<div class="flex items-center gap-3 step-photo">' +
            '<label class="relative cursor-pointer bg-white hover:bg-[#F2E5D4] text-warm-ink border border-[#D4B996] px-3 py-1.5 text-xs flex items-center gap-1.5 font-sans font-medium rounded-xl transition-colors">' +
            '<span class="material-symbols-outlined text-sm text-terracotta">add_a_photo</span><span class="sp-label">Фото шага</span>' +
            '<input accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full sp-input" name="step_photo_' + i + '" type="file">' +
            '</label>' +
            '<div class="hidden items-center gap-2 sp-preview">' +
            '<img alt="Фото шага" class="w-14 h-14 object-cover rounded-lg border border-[#D4B996] sp-img" src="">' +
            '<button class="px-2 py-1 bg-white border border-[#D4B996] text-terracotta hover:bg-terracotta hover:text-white transition-colors text-xs font-mono font-bold rounded-lg flex items-center gap-1 sp-remove" type="button"><span class="material-symbols-outlined text-sm">delete</span> Убрать</button>' +
            '</div>' +
            '<input type="hidden" class="sp-keep" name="steps[' + i + '][keep_photo]" value="' + esc(d.photo) + '">' +
            '</div>' +
            '</div>');
        stepBox.appendChild(row);
        bindStepPhoto(row, d.photo_url);
        renumber();
    }

    function bindStepPhoto(row, existingUrl) {
        var input = row.querySelector('.sp-input');
        var prev = row.querySelector('.sp-preview');
        var img = row.querySelector('.sp-img');
        var keep = row.querySelector('.sp-keep');
        var label = row.querySelector('.sp-label');
        function show(src) {
            img.src = src;
            prev.classList.remove('hidden'); prev.classList.add('flex');
            label.textContent = 'Заменить';
        }
        function hide() {
            img.src = '';
            prev.classList.add('hidden'); prev.classList.remove('flex');
            label.textContent = 'Фото шага';
        }
        if (existingUrl) show(existingUrl);
        input.addEventListener('change', function () {
            var f = input.files[0];
            if (!f) return;
            if (f.size > 10 * 1024 * 1024) { showErrors(['Фото шага больше 10 МБ']); input.value = ''; return; }
            var rd = new FileReader();
            rd.onload = function (ev) { show(ev.target.result); };
            rd.readAsDataURL(f);
        });
        row.querySelector('.sp-remove').addEventListener('click', function () {
            input.value = '';
            keep.value = '';
            hide();
        });
    }

    [ingBox, stepBox].forEach(function (box) {
        box.addEventListener('click', function (e) {
            var b = e.target.closest('[data-remove]');
            if (!b) return;
            b.closest('.ingredient-item, .step-item').remove();
            renumber();
        });
    });
    document.getElementById('rf-add-ingredient').addEventListener('click', function () { addIngredient(); });
    document.getElementById('rf-add-step').addEventListener('click', function () { addStep(); });

    // --- фото ---
    function showPreview(src) {
        previewImg.src = src;
        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
        preview.classList.add('flex');
    }
    function hidePreview() {
        previewImg.src = '';
        preview.classList.add('hidden');
        preview.classList.remove('flex');
        placeholder.classList.remove('hidden');
    }
    photoInput.addEventListener('change', function () {
        var f = photoInput.files[0];
        if (!f) return;
        if (f.size > 15 * 1024 * 1024) {
            showErrors(['Фото больше 15 МБ']);
            photoInput.value = '';
            return;
        }
        removeFlag.value = '0';
        var reader = new FileReader();
        reader.onload = function (ev) { showPreview(ev.target.result); };
        reader.readAsDataURL(f);
    });
    document.getElementById('rf-remove-photo').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        photoInput.value = '';
        removeFlag.value = '1';
        hidePreview();
    });

    // --- ошибки ---
    function showErrors(list) {
        errBox.innerHTML = '';
        list.forEach(function (m) {
            var d = document.createElement('div');
            d.textContent = m;
            errBox.appendChild(d);
        });
        errBox.classList.remove('hidden');
        errBox.classList.add('flex');
        errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function clearErrors() {
        errBox.classList.add('hidden');
        errBox.classList.remove('flex');
        errBox.innerHTML = '';
    }

    // --- открытие / закрытие ---
    function setVal(id, v) { document.getElementById(id).value = v == null ? '' : v; }

    window.openRecipeForm = function (id) {
        form.reset();
        clearErrors();
        ingBox.innerHTML = '';
        stepBox.innerHTML = '';
        hidePreview();
        removeFlag.value = '0';
        editId = id || null;

        var r = editId ? cfg.recipes[editId] : null;
        document.getElementById('rf-modal-title').textContent = r ? 'Редактирование рецепта' : 'Передать рецепт в архив';
        document.getElementById('rf-badge').textContent = r ? 'ПРАВКА ЗАПИСИ В КУЛИНАРНОЙ КНИГЕ' : 'ФОРМУЛЯР ПОПОЛНЕНИЯ КУЛИНАРНОЙ КНИГИ';
        document.getElementById('rf-modal-subtitle').textContent = r
            ? 'Измените любые поля и сохраните — рецепт обновится в вашей книге'
            : 'Поделитесь секретом домашней кухни, точными граммовками и историей создания блюда для сохранения';

        if (r) {
            setVal('rf-title', r.title);
            setVal('rf-category', r.category_id);
            setVal('rf-equipment', r.equipment);
            setVal('rf-description', r.description);
            setVal('rf-prep', r.prep_time_min);
            setVal('rf-cook', r.cook_time_min);
            setVal('rf-servings', r.servings || 4);
            setVal('rf-difficulty', r.difficulty);
            setVal('rf-calories', r.calories);
            setVal('rf-protein', r.protein);
            setVal('rf-fat', r.fat);
            setVal('rf-carbs', r.carbs);
            setVal('rf-secret', r.secret);
            (r.ingredients || []).forEach(addIngredient);
            (r.steps || []).forEach(addStep);
            if (r.photo) showPreview(r.photo);
        }
        if (!ingBox.children.length) addIngredient();
        if (!stepBox.children.length) addStep();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        modal.scrollTop = 0;
    };

    window.closeRecipeForm = function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    // --- отправка ---
    var submitter = null;
    form.querySelectorAll('button[type=submit]').forEach(function (b) {
        b.addEventListener('click', function () { submitter = b; });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        var btn = submitter || form.querySelector('button[type=submit][data-status=draft]');
        var data = new FormData(form);
        data.append('status', btn.dataset.status);
        data.append('_csrf', cfg.csrf);

        var url = cfg.saveUrl + (editId ? (cfg.saveUrl.indexOf('?') === -1 ? '?' : '&') + 'id=' + editId : '');
        var buttons = form.querySelectorAll('button[type=submit]');
        buttons.forEach(function (b) { b.disabled = true; b.classList.add('opacity-60'); });

        fetch(url, { method: 'POST', body: data, headers: { 'X-CSRF-Token': cfg.csrf, 'Accept': 'application/json' } })
            .then(function (r) { return r.json().then(function (j) { return { status: r.status, body: j }; }); })
            .then(function (res) {
                if (res.body && res.body.ok) {
                    window.location.reload();
                    return;
                }
                showErrors((res.body && res.body.errors) || [res.body && res.body.message || 'Не удалось сохранить рецепт']);
            })
            .catch(function () { showErrors(['Не удалось связаться с сервером. Попробуйте ещё раз.']); })
            .then(function () {
                buttons.forEach(function (b) { b.disabled = false; b.classList.remove('opacity-60'); });
            });
    });

    // Esc закрывает окно
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeRecipeForm();
    });
})();
