<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var app\models\User $user */
/** @var app\models\ProfileForm $profileForm */
/** @var app\models\Recipe[] $recipes */
/** @var app\models\Recipe[] $favorites */
/** @var app\models\ShoppingListItem[] $shoppingItems */
/** @var app\models\Category[] $categories */

$recipesCount = count($recipes);
$favoritesCount = count($favorites);
$shoppingCount = count(array_filter($shoppingItems, function ($i) { return !$i->is_bought; }));

$tapes = ['washi-honey', 'washi-sage', 'washi-terracotta'];
$photoUrl = function ($photo) {
    if (!$photo) {
        return null;
    }
    if (preg_match('~^(https?:)?//|^/~', $photo)) {
        return $photo;
    }
    $path = (strpos($photo, '/') === false ? 'images/' : '') . $photo;
    return Yii::getAlias('@web') . '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
};
$fmtQty = function ($q, $unit) {
    $q = rtrim(rtrim(number_format((float)$q, 2, '.', ''), '0'), '.');
    return trim($q . ' ' . $unit);
};
$hasErrors = $profileForm->hasErrors();
?>
<!-- MAIN CONTENT -->
<!-- MAIN CONTENT -->
<main class="w-full pt-28 pb-16 flex-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-8">

        <!-- Верхний блок профиля -->
        <div class="relative bg-[#FAF4EB] border border-[#dec0b7] shadow-polaroid p-6 sm:p-8 rounded-3xl">
            <div class="washi-tape washi-terracotta -top-3 left-10 w-28 h-5 -rotate-1 pointer-events-none"></div>
            <div class="washi-tape washi-sage -bottom-2 right-12 w-24 h-5 rotate-1 pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <div class="lg:col-span-3 flex flex-col items-center sm:items-start">
                    <div class="relative bg-white p-3 border border-[#dec0b7] shadow-scrapbook rounded-2xl">
                        <div class="washi-tape washi-honey -top-2 left-6 w-16 h-4 rotate-2 pointer-events-none"></div>
                        <?php $avatarUrl = $photoUrl($user->avatar); ?>
                        <?php if ($avatarUrl): ?>
                            <img id="profile-avatar-img" class="w-44 h-52 sm:w-48 sm:h-56 object-cover rounded-xl contrast-105" src="<?= Html::encode($avatarUrl) ?>" alt="Аватар">
                        <?php else: ?>
                            <div id="profile-avatar-img" class="w-44 h-52 sm:w-48 sm:h-56 rounded-xl bg-[#FFF9F4] flex items-center justify-center"><span class="material-symbols-outlined text-7xl text-[#dec0b7]">person</span></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="lg:col-span-6 flex flex-col gap-4">
                    <div>
                        <div class="font-mono text-xs text-warm-muted mb-1">@<?= Html::encode($user->username) ?></div>
                        <h1 id="profile-name-heading" class="font-serif text-3xl sm:text-4xl text-warm-ink font-bold leading-tight">
                            <?= Html::encode($user->fullname) ?>
                        </h1>
                        <div class="flex flex-col gap-1 mt-2 text-xs sm:text-sm font-sans text-warm-muted">
                            <span id="profile-email-text" class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-terracotta">mail</span> <?= Html::encode($user->mail) ?></span>
                            <span id="profile-phone-text" class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-sage">call</span> <?= Html::encode($user->phone) ?></span>
                        </div>
                    </div>
                    <p id="profile-bio-text" class="text-xs sm:text-sm font-sans text-warm-ink leading-relaxed max-w-xl">
                        <?= $user->bio ? Html::encode($user->bio) : 'Здесь пока пусто — расскажите немного о себе.' ?>
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button onclick="openEditProfileModal()" class="px-4 py-2.5 bg-white hover:bg-[#FFF8F0] text-warm-ink border border-[#dec0b7] text-xs font-sans font-bold transition-all rounded-xl shadow-xs flex items-center gap-2 cursor-pointer" type="button">
                            <span class="material-symbols-outlined text-base text-terracotta">edit_note</span>
                            <span>Редактировать профиль</span>
                        </button>
                        <?= Html::beginForm(['site/logout'], 'post', ['class' => 'inline']) ?>
                            <button class="px-4 py-2.5 bg-white hover:bg-[#FFF8F0] text-warm-ink border border-[#dec0b7] text-xs font-sans font-bold transition-all rounded-xl shadow-xs flex items-center gap-2 cursor-pointer" type="submit">
                                <span class="material-symbols-outlined text-base text-terracotta">logout</span>
                                <span>Выйти</span>
                            </button>
                        <?= Html::endForm() ?>
                    </div>
                </div>

                <div class="lg:col-span-3 flex flex-col gap-3">
                    <div class="text-xs font-mono font-bold uppercase tracking-widest text-warm-muted pb-1 border-b border-dashed border-[#dec0b7]">
                        Картотека записей
                    </div>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-white p-3.5 border border-[#dec0b7] shadow-xs flex items-center justify-between rounded-2xl">
                            <div>
                                <div class="text-2xl font-serif font-bold text-terracotta leading-none"><?= $recipesCount ?></div>
                                <div class="text-xs font-sans text-warm-muted font-medium mt-1">Записанных рецептов</div>
                            </div>
                            <span class="material-symbols-outlined text-2xl text-[#dec0b7]">menu_book</span>
                        </div>
                        <div class="bg-white p-3.5 border border-[#dec0b7] shadow-xs flex items-center justify-between rounded-2xl">
                            <div>
                                <div class="text-2xl font-serif font-bold text-sage leading-none" id="stat-favorites"><?= $favoritesCount ?></div>
                                <div class="text-xs font-sans text-warm-muted font-medium mt-1">Закладок в архиве</div>
                            </div>
                            <span class="material-symbols-outlined text-2xl text-[#dec0b7]">bookmark_added</span>
                        </div>
                        <div class="bg-white p-3.5 border border-[#dec0b7] shadow-xs flex items-center justify-between rounded-2xl">
                            <div>
                                <div class="text-2xl font-serif font-bold text-honey leading-none"><?= $shoppingCount ?></div>
                                <div class="text-xs font-sans text-warm-muted font-medium mt-1">В корзине покупок</div>
                            </div>
                            <span class="material-symbols-outlined text-2xl text-[#dec0b7]">shopping_basket</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Навигационные вкладки -->
        <div class="flex flex-wrap items-end gap-2 border-b border-[#dec0b7] pt-2">
            <button onclick="switchProfileTab('recipes')" id="tab-recipes-btn" class="px-5 py-3 bg-terracotta text-white font-sans text-xs sm:text-sm font-bold tracking-wide rounded-t-2xl shadow-xs flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-base">auto_stories</span>
                <span>Мои рецепты</span>
            </button>
            <button onclick="switchProfileTab('favorites')" id="tab-favorites-btn" class="px-5 py-3 bg-white/80 hover:bg-white text-warm-muted hover:text-warm-ink border border-b-0 border-[#dec0b7] font-sans text-xs sm:text-sm font-semibold transition-colors rounded-t-2xl flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-base">favorite</span>
                <span>Избранное (<span id="tab-fav-count"><?= $favoritesCount ?></span>)</span>
            </button>
            <button onclick="switchProfileTab('shopping')" id="tab-shopping-btn" class="px-5 py-3 bg-white/80 hover:bg-white text-warm-muted hover:text-warm-ink border border-b-0 border-[#dec0b7] font-sans text-xs sm:text-sm font-semibold transition-colors rounded-t-2xl flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-base">shopping_bag</span>
                <span>Список покупок (<?= $shoppingCount ?>)</span>
            </button>
        </div>

        <!-- СЕКЦИЯ 1: МОИ РЕЦЕПТЫ -->
        <div id="section-recipes" class="flex flex-col gap-6">
            <div class="bg-[#FAF4EB] p-4 border border-[#dec0b7] rounded-2xl flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative w-full sm:w-56 shrink-0">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-warm-muted">
                            <span class="material-symbols-outlined text-base">search</span>
                        </span>
                        <input id="recipe-search-input" class="w-full pl-9 pr-3 py-2 bg-white border border-[#dec0b7] text-xs font-sans placeholder-[#8a726a] text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta" placeholder="Поиск..." type="text">
                    </div>
                    <div class="flex items-center gap-2 flex-1">
                        <select id="recipe-category-filter" class="w-full sm:w-72 bg-white border border-[#dec0b7] text-xs font-sans text-warm-ink px-3 py-2 rounded-xl focus:ring-0 focus:border-terracotta truncate">
                            <option value="">Все категории тетради</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c->id ?>"><?= Html::encode($c->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button onclick="openRecipeForm()" class="px-5 py-2.5 bg-terracotta hover:bg-terracotta-hover text-white text-xs font-bold rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer shrink-0" type="button">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Записать рецепт</span>
                </button>
            </div>

            <?php if (!$recipes): ?>
                <div class="bg-white border border-dashed border-[#D4B996] rounded-3xl p-10 text-center text-sm text-warm-muted">
                    У вас пока нет записанных рецептов. Нажмите «Записать рецепт», чтобы добавить первый.
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="my-recipes-grid">
                <?php foreach ($recipes as $i => $r): $img = $photoUrl($r->photo); ?>
                <div class="my-recipe-card relative bg-white border border-[#D4B996] shadow-xs flex flex-col justify-between rounded-3xl overflow-visible group"
                     data-category="<?= (int)$r->category_id ?>" data-title="<?= Html::encode(mb_strtolower($r->title)) ?>">
                    <div class="washi-tape <?= $tapes[$i % 3] ?> -top-3 left-8 w-32 h-6 flex items-center justify-center">
                        <span class="font-mono text-[10px] text-white font-bold tracking-widest uppercase"><?= Html::encode($r->category ? StringHelper::truncate($r->category->name, 18) : 'Рецепт') ?></span>
                    </div>
                    <div class="relative h-56 w-full overflow-hidden bg-[#FFF9F4] rounded-t-3xl flex items-center justify-center">
                        <?php if ($img): ?>
                            <img alt="<?= Html::encode($r->title) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?= Html::encode($img) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-[#dec0b7]">restaurant_menu</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <?php if ($r->status === 'Опубликовано'): ?>
                                    <span class="inline-block px-2.5 py-0.5 bg-[#EDF4EF] text-sage border border-sage/40 font-mono text-xs font-bold rounded-md">✓ Опубликован</span>
                                <?php else: ?>
                                    <span class="inline-block px-2.5 py-0.5 bg-[#FFF3DC] text-honey border border-honey/40 font-mono text-xs font-bold rounded-md">✎ Черновик</span>
                                <?php endif; ?>
                                <span class="font-mono text-xs text-warm-muted"><?= $r->created_at ? Yii::$app->formatter->asDate($r->created_at, 'php:d.m.Y') : '' ?></span>
                            </div>
                            <h3 class="text-xl font-serif font-bold text-warm-ink group-hover:text-terracotta transition-colors"><?= Html::encode($r->title) ?></h3>
                            <p class="font-sans text-xs text-warm-muted mt-1 leading-relaxed"><?= Html::encode(StringHelper::truncate((string)$r->description, 130)) ?></p>
                        </div>
                        <div class="pt-4 border-t border-dashed border-[#D4B996] flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <button type="button" class="p-2 text-warm-muted hover:text-terracotta transition-colors" title="Редактировать" onclick="openRecipeForm(<?= (int)$r->id ?>)"><span class="material-symbols-outlined text-lg">edit</span></button>
                                <?= Html::beginForm(['site/delete-recipe', 'id' => $r->id], 'post', ['class' => 'inline', 'onsubmit' => 'return confirm(' . Html::encode(json_encode('Удалить рецепт «' . $r->title . '»? Это действие нельзя отменить.', JSON_UNESCAPED_UNICODE)) . ');']) ?>
                                    <button type="submit" class="p-2 text-warm-muted hover:text-terracotta transition-colors" title="Удалить"><span class="material-symbols-outlined text-lg">delete</span></button>
                                <?= Html::endForm() ?>
                            </div>
                            <a href="<?= Url::to(['site/recipe', 'id' => $r->id]) ?>" class="px-4 py-2 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold transition-all rounded-xl shadow-xs flex items-center gap-1"><span>Открыть</span><span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- СЕКЦИЯ 2: ИЗБРАННОЕ -->
        <div id="section-favorites" class="hidden flex-col gap-6">
            <div class="bg-[#FAF4EB] p-6 border border-[#dec0b7] rounded-3xl flex flex-col gap-4">
                <h3 class="font-serif text-2xl font-bold text-warm-ink">Сохраненные рецепты (Избранное)</h3>
                <p class="text-xs text-warm-muted">Рецепты из общей кулинарной книги мастеров, которые вы добавили в закладки.</p>
                                    <div class="text-center text-sm text-warm-muted py-6 <?= $favorites ? 'hidden' : '' ?>" id="fav-empty">В избранном пока ничего нет.</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($favorites as $f): $img = $photoUrl($f->photo); ?>
                    <div class="fav-card bg-white p-4 border border-[#D4B996] rounded-2xl flex items-center gap-4">
                        <?php if ($img): ?>
                            <img src="<?= Html::encode($img) ?>" class="w-20 h-20 object-cover rounded-xl" alt="<?= Html::encode($f->title) ?>">
                        <?php else: ?>
                            <div class="w-20 h-20 rounded-xl bg-[#FFF9F4] flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-3xl text-[#dec0b7]">restaurant_menu</span></div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <span class="text-[10px] font-mono text-sage uppercase font-bold"><?= Html::encode($f->category ? $f->category->name : '') ?></span>
                            <h4 class="font-serif font-bold text-base text-warm-ink"><?= Html::encode($f->title) ?></h4>
                            <a href="<?= Url::to(['site/recipe', 'id' => $f->id]) ?>" class="text-xs text-terracotta font-bold hover:underline mt-1 inline-block">Открыть рецепт →</a>
                        </div>
                        <button type="button" class="fav-remove p-2 text-terracotta hover:bg-[#FFF8F0] rounded-xl transition-colors self-start" title="Убрать из избранного" data-id="<?= (int)$f->id ?>"><span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">favorite</span></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- СЕКЦИЯ 3: СПИСОК ПОКУПОК -->
        <div id="section-shopping" class="hidden flex-col gap-6">
            <div class="bg-[#FAF4EB] p-6 border border-[#dec0b7] rounded-3xl flex flex-col gap-4">
                <div class="flex items-center justify-between border-b border-dashed border-[#dec0b7] pb-4">
                    <div>
                        <h3 class="font-serif text-2xl font-bold text-warm-ink">Ваш текущий список покупок</h3>
                        <p class="text-xs text-warm-muted mt-0.5">Ингредиенты, добавленные из рецептов для ближайшего похода на рынок</p>
                    </div>
                    <a href="<?= Url::to(['shopping-list']) ?>" class="px-4 py-2 bg-terracotta text-white text-xs font-bold rounded-xl hover:bg-terracotta-hover transition-colors shadow-xs">Открыть полную страницу →</a>
                </div>
                <?php if (!$shoppingItems): ?>
                    <div class="text-center text-sm text-warm-muted py-6">Список покупок пуст.</div>
                <?php endif; ?>
                <div class="flex flex-col gap-2.5" id="shopping-items-container">
                    <?php foreach ($shoppingItems as $it): ?>
                    <div class="flex items-center justify-between p-3.5 bg-white border border-[#D4B996]/60 rounded-xl transition-all">
                        <label class="flex items-center gap-3 cursor-pointer flex-1">
                            <input type="checkbox" data-shopping-id="<?= (int)$it->id ?>" onchange="toggleShoppingItem(this)" class="w-4 h-4 rounded text-terracotta focus:ring-0" <?= $it->is_bought ? 'checked' : '' ?>>
                            <span class="font-sans text-sm text-warm-ink <?= $it->is_bought ? 'line-through opacity-60' : 'font-medium' ?>">
                                <?= Html::encode($it->ingredient ? $it->ingredient->name : 'Ингредиент') ?>
                                <?php $q = $fmtQty($it->quantity, $it->unit); if ($q !== '' && $q !== '0'): ?>(<?= Html::encode($q) ?>)<?php endif; ?>
                            </span>
                        </label>
                        <span class="text-xs font-mono <?= $it->is_bought ? 'text-sage' : 'text-terracotta' ?> font-bold status-label"><?= $it->is_bought ? 'Куплено' : 'Нужно купить' ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</main>


<!-- MODAL: EDIT PROFILE (Скотч теперь висит на границе окна) -->
<div id="edit-profile-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#2c221e]/60 backdrop-blur-xs p-4 overflow-y-auto">
    <div class="relative w-full max-w-xl bg-[#FFF9F4] border border-[#D4B996] shadow-scrapbook rounded-3xl overflow-visible my-8 text-warm-ink">
        <!-- Скотч теперь выступает за верхнюю границу и висит -->
        <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-28 h-5 bg-[#c8a05a]/85 border border-dashed border-[#b48c46]/50 shadow-sm transform -rotate-1 pointer-events-none z-30"></div>
        
        <div class="flex items-center justify-between p-6 border-b border-[#D4B996] bg-[#FAF4EB] rounded-t-3xl">
            <div>
                <h3 class="font-serif text-2xl font-bold text-warm-ink">Редактировать профиль</h3>
                <p class="text-xs text-warm-muted mt-0.5">Обновите ваши данные авторизации и контакты</p>
            </div>
            <button onclick="closeEditProfileModal()" type="button" class="p-1.5 text-warm-muted hover:text-terracotta transition-colors rounded-lg">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <?= Html::beginForm(['site/profile'], 'post', ['id' => 'edit-profile-form', 'enctype' => 'multipart/form-data', 'class' => 'p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto']) ?>
            <?php if ($hasErrors): ?>
                <div class="text-xs text-red-700 bg-red-50 border border-red-200 rounded-xl p-3 font-sans">
                    <?php foreach ($profileForm->getFirstErrors() as $e): ?><div><?= Html::encode($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">Имя и Фамилия *</label>
                    <input id="edit-name-input" name="ProfileForm[fullname]" value="<?= Html::encode($profileForm->fullname) ?>" type="text" required class="w-full bg-[#FFF8F0] border border-[#D4B996] p-2.5 text-xs text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta font-sans">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">Эл. почта *</label>
                    <input id="edit-email-input" name="ProfileForm[mail]" value="<?= Html::encode($profileForm->mail) ?>" type="email" required class="w-full bg-[#FFF8F0] border border-[#D4B996] p-2.5 text-xs text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta font-sans">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">Телефон</label>
                    <input id="edit-phone-input" name="ProfileForm[phone]" value="<?= Html::encode($profileForm->phone) ?>" required type="tel" class="w-full bg-[#FFF8F0] border border-[#D4B996] p-2.5 text-xs text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta font-sans" placeholder="+7 (...) ...-..-..">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">Новый пароль</label>
                    <input id="edit-password-input" name="ProfileForm[newPassword]" autocomplete="new-password" minlength="8" type="password" placeholder="Оставьте пустым, если не меняете" class="w-full bg-[#FFF8F0] border border-[#D4B996] p-2.5 text-xs text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta font-sans">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">О себе</label>
                <textarea id="edit-bio-input" name="ProfileForm[bio]" maxlength="255" rows="2" class="w-full bg-[#FFF8F0] border border-[#D4B996] p-2.5 text-xs text-warm-ink rounded-xl focus:ring-0 focus:border-terracotta font-sans leading-relaxed"><?= Html::encode($profileForm->bio) ?></textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-mono font-bold uppercase tracking-wider text-warm-ink">Фотография профиля (аватар)</label>
                <div class="border-2 border-dashed border-[#D4B996] hover:border-terracotta bg-[#FFF8F0] p-4 text-center cursor-pointer transition-colors relative group rounded-2xl">
                    <input accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" id="profile-photo-input" name="avatar" type="file">
                    <div class="flex flex-col items-center justify-center gap-1 <?= $avatarUrl ? 'hidden' : '' ?>" id="profile-upload-placeholder">
                        <span class="material-symbols-outlined text-2xl text-terracotta">add_a_photo</span>
                        <span class="text-xs font-semibold text-warm-ink">Загрузить новый снимок</span>
                        <span class="text-[10px] text-warm-muted">JPG, PNG, WEBP до 10 МБ</span>
                    </div>
                    <div class="<?= $avatarUrl ? 'flex' : 'hidden' ?> flex-col items-center justify-center gap-2 relative z-20" id="profile-preview-container">
                        <img alt="Превью" class="w-20 h-20 object-cover rounded-xl border border-[#D4B996]" id="profile-preview-img" src="<?= Html::encode((string)$avatarUrl) ?>">
                        <span class="text-xs font-mono font-bold text-terracotta" id="profile-preview-label"><?= $avatarUrl ? 'Текущее фото (нажмите, чтобы заменить)' : 'Фото выбрано' ?></span>
                        <button type="button" id="profile-remove-photo" class="px-3 py-1 bg-white border border-[#D4B996] text-terracotta hover:bg-terracotta hover:text-white transition-colors text-xs font-mono font-bold rounded-lg flex items-center gap-1 z-30">
                            <span class="material-symbols-outlined text-sm">delete</span> Удалить фото
                        </button>
                    </div>
                </div>
                <input type="hidden" name="ProfileForm[remove_avatar]" id="profile-remove-avatar-flag" value="0">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D4B996] mt-2">
                <button onclick="closeEditProfileModal()" type="button" class="px-5 py-2.5 bg-white hover:bg-paper text-warm-ink border border-[#D4B996] text-xs font-bold rounded-xl shadow-xs transition-colors">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-2.5 bg-terracotta hover:bg-terracotta-hover text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                    Сохранить изменения
                </button>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>

<!-- MODAL: РЕЦЕПТ (создание и редактирование) -->
<div aria-labelledby="rf-modal-title" aria-modal="true" class="fixed inset-0 z-50 hidden items-start justify-center bg-[#2c221e]/60 backdrop-blur-xs p-4 overflow-y-auto py-10" id="recipe-modal" role="dialog">
    <div class="relative w-full max-w-[860px] bg-[#FFFDF9] border-2 border-[#D4B996] shadow-[0_20px_50px_rgba(74,53,39,0.25)] p-6 sm:p-9 text-warm-ink my-auto rounded-3xl">
        <div class="absolute -top-4 left-1/2 -translate-x-1/2 w-36 h-7 bg-[#c8a05a]/45 border border-dashed border-[#b48c46]/50 shadow-sm transform -rotate-1 pointer-events-none"></div>
        <div class="absolute -top-5 right-14 z-20 pointer-events-none flex items-center justify-center">
            <span class="material-symbols-outlined text-warm-muted text-4xl transform rotate-45 drop-shadow-sm">attach_file</span>
        </div>

        <button aria-label="Закрыть" class="absolute top-5 right-5 w-8 h-8 bg-[#FAF4EB] border border-[#D4B996] text-warm-ink hover:bg-terracotta hover:text-white transition-colors flex items-center justify-center z-20 rounded-lg" type="button" onclick="closeRecipeForm()">
            <span class="text-base font-bold leading-none font-mono">✕</span>
        </button>

        <div class="border-b border-dashed border-[#D4B996] pb-6 mb-7 relative">
            <div class="inline-flex items-center gap-2 mb-2 bg-[#F6ECE0] border border-[#D4B996] px-2.5 py-1 rounded-md">
                <span class="material-symbols-outlined text-sm text-terracotta leading-none">menu_book</span>
                <span class="font-mono text-[11px] font-bold tracking-wider text-warm-ink uppercase" id="rf-badge">ФОРМУЛЯР ПОПОЛНЕНИЯ КУЛИНАРНОЙ КНИГИ</span>
            </div>
            <h3 class="font-serif text-2xl sm:text-3xl lg:text-[32px] font-bold text-warm-ink leading-tight" id="rf-modal-title">Передать рецепт в архив</h3>
            <p class="text-warm-muted text-sm sm:text-base mt-1.5 max-w-xl font-sans" id="rf-modal-subtitle">Поделитесь секретом домашней кухни, точными граммовками и историей создания блюда для сохранения</p>
        </div>

        <form class="flex flex-col gap-7" id="recipe-form" enctype="multipart/form-data" autocomplete="off">
            <div id="rf-errors" class="hidden text-xs text-red-700 bg-red-50 border border-red-200 rounded-xl p-3 font-sans flex-col gap-1"></div>

            <div class="flex flex-col gap-1.5">
                <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink flex items-center gap-1.5" for="rf-title">
                    <span class="text-terracotta font-mono">§ 1.</span> Название блюда <span class="text-terracotta">*</span>
                </label>
                <input class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-warm-ink text-lg placeholder-warm-muted/50 focus:border-terracotta focus:bg-white focus:outline-none transition-colors font-serif rounded-xl" id="rf-title" name="title" maxlength="200" placeholder="Например: Бабушкин яблочный пирог" type="text">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink flex items-center gap-1.5" for="rf-category">
                        <span class="text-terracotta font-mono">§ 2.</span> Категория блюда <span class="text-terracotta">*</span>
                    </label>
                    <div class="relative">
                        <select class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink font-sans focus:border-terracotta focus:bg-white focus:outline-none transition-colors cursor-pointer appearance-none pr-9 rounded-xl" id="rf-category" name="category_id">
                            <option value="">— выберите категорию —</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int)$c->id ?>"><?= Html::encode($c->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-warm-muted text-lg">expand_more</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="rf-equipment">Оборудование</label>
                    <input class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink font-sans focus:border-terracotta focus:bg-white focus:outline-none transition-colors rounded-xl" id="rf-equipment" name="equipment" maxlength="255" placeholder="Например: духовка, форма 24 см" type="text">
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink" for="rf-description">Краткое описание</label>
                <textarea class="w-full bg-[#FFF9F4] border border-[#D4B996] px-3.5 py-2.5 text-sm text-warm-ink font-sans focus:border-terracotta focus:bg-white focus:outline-none transition-colors rounded-xl" id="rf-description" name="description" rows="2" placeholder="Пара предложений о блюде — они попадут в карточку рецепта"></textarea>
            </div>

            <div class="bg-[#FAF4EB] border border-[#D4B996] p-4 rounded-2xl">
                <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-warm-muted mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-terracotta">tune</span> Технические параметры приготовления
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-prep">Подготовка, мин</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-prep" name="prep_time_min" type="number" min="0" step="1" placeholder="20">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-cook">Готовка, мин</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-cook" name="cook_time_min" type="number" min="0" step="1" placeholder="55">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-servings">Порций</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-servings" name="servings" type="number" min="1" max="100" step="1" value="4">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-difficulty">Сложность</label>
                        <select class="w-full bg-white border border-[#D4B996] px-3.5 py-1.5 text-sm font-sans text-warm-ink focus:outline-none rounded-lg" id="rf-difficulty" name="difficulty">
                            <option value="Простая">Простая</option>
                            <option value="Средняя" selected>Средняя</option>
                            <option value="Для терпеливых">Для терпеливых</option>
                        </select>
                    </div>
                </div>
                <div class="font-mono text-[11px] font-bold uppercase tracking-wider text-warm-muted mt-4 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-terracotta">nutrition</span> Пищевая ценность на порцию (необязательно)
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-calories">Ккал</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-calories" name="calories" type="number" min="0" step="1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-protein">Белки, г</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-protein" name="protein" type="number" min="0" step="0.1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-fat">Жиры, г</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-fat" name="fat" type="number" min="0" step="0.1">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-warm-ink" for="rf-carbs">Углеводы, г</label>
                        <input class="w-full bg-white border border-[#D4B996] px-3 py-1.5 text-sm font-mono font-bold text-warm-ink focus:outline-none rounded-lg" id="rf-carbs" name="carbs" type="number" min="0" step="0.1">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink">
                    <span class="text-terracotta font-mono">§ 3.</span> Раскладка ингредиентов <span class="text-terracotta">*</span>
                </label>
                <div class="bg-[#FAF4EB] border border-[#D4B996] p-3 sm:p-4 flex flex-col gap-2.5 rounded-2xl" id="rf-ingredients"></div>
                <div>
                    <button class="bg-[#FAF4EB] hover:bg-[#F2E5D4] text-warm-ink border border-[#D4B996] px-3.5 py-1.5 text-xs flex items-center gap-1.5 font-sans font-medium rounded-xl transition-colors" id="rf-add-ingredient" type="button">
                        <span class="material-symbols-outlined text-sm text-terracotta">add</span>Добавить ингредиент
                    </button>
                </div>
                <datalist id="rf-ingredient-names">
                    <?php foreach ($ingredientNames as $n): ?><option value="<?= Html::encode($n) ?>"><?php endforeach; ?>
                </datalist>
                <datalist id="rf-units">
                    <?php foreach (['г', 'кг', 'мл', 'л', 'шт.', 'ч. л.', 'ст. л.', 'стакан', 'щепотка', 'по вкусу'] as $u): ?><option value="<?= Html::encode($u) ?>"><?php endforeach; ?>
                </datalist>
            </div>

            <div class="flex flex-col gap-3">
                <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink">
                    <span class="text-terracotta font-mono">§ 4.</span> Пошаговое приготовление <span class="text-terracotta">*</span>
                </label>
                <div class="flex flex-col gap-3" id="rf-steps"></div>
                <div>
                    <button class="bg-[#FAF4EB] hover:bg-[#F2E5D4] text-warm-ink border border-[#D4B996] px-3.5 py-1.5 text-xs flex items-center gap-1.5 font-sans font-medium rounded-xl transition-colors" id="rf-add-step" type="button">
                        <span class="material-symbols-outlined text-sm text-terracotta">add</span>Добавить шаг
                    </button>
                </div>
            </div>

            <div class="bg-[#FAF4EB] border-l-4 border-l-terracotta border border-[#D4B996] p-4 flex flex-col gap-2 rounded-2xl">
                <div class="flex items-center gap-2 text-terracotta">
                    <span class="material-symbols-outlined text-lg">lightbulb</span>
                    <label class="font-mono text-xs font-bold uppercase tracking-wider" for="rf-secret">Особый совет или секрет хозяйки</label>
                </div>
                <textarea class="w-full bg-white border border-[#E5D2BE] p-2.5 text-sm text-warm-ink focus:outline-none rounded-xl font-serif italic" id="rf-secret" name="secret" rows="2"></textarea>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-mono text-xs font-bold uppercase tracking-wider text-warm-ink flex items-center gap-1.5">
                    <span class="text-terracotta font-mono">§ 5.</span> Фотография блюда или рукописная заметка
                </label>
                <div class="border-2 border-dashed border-[#D4B996] hover:border-terracotta bg-[#FFF8F0] p-6 text-center cursor-pointer transition-colors relative group rounded-2xl">
                    <input accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10" id="rf-photo" name="photo" type="file">
                    <div class="flex flex-col items-center justify-center gap-2 pointer-events-none" id="rf-upload-placeholder">
                        <div class="w-12 h-12 bg-white border border-[#D4B996] text-terracotta flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform rounded-xl">
                            <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        </div>
                        <div class="font-bold text-base text-warm-ink font-sans">Перетащите снимок готового блюда или страницу фамильной тетради</div>
                        <div class="font-mono text-[11px] text-warm-muted uppercase tracking-wider">ПОДДЕРЖИВАЮТСЯ ФОРМАТЫ JPG, PNG, WEBP ДО 15 МБ</div>
                    </div>
                    <div class="hidden flex-col items-center justify-center gap-3 relative z-20" id="rf-preview">
                        <div class="relative max-w-xs max-h-48 rounded-xl overflow-hidden border-2 border-[#D4B996] shadow-sm">
                            <img alt="Превью фото" class="w-full h-full object-cover" id="rf-preview-img" src="">
                        </div>
                        <button class="px-3 py-1 bg-white border border-[#D4B996] text-terracotta hover:bg-terracotta hover:text-white transition-colors text-xs font-mono font-bold rounded-lg flex items-center gap-1 z-30" id="rf-remove-photo" type="button">
                            <span class="material-symbols-outlined text-sm">delete</span> Удалить фото
                        </button>
                    </div>
                </div>
                <input type="hidden" name="remove_photo" id="rf-remove-photo-flag" value="0">
            </div>

            <div class="border-t border-dashed border-[#D4B996] pt-6 flex flex-wrap items-center justify-end gap-3">
                <button class="px-5 py-2.5 bg-white hover:bg-paper text-warm-ink border border-[#D4B996] text-xs font-bold transition-colors rounded-xl font-sans" type="button" onclick="closeRecipeForm()">Отмена</button>
                <button class="px-5 py-2.5 bg-[#FAF4EB] hover:bg-[#F2E5D4] text-warm-ink border border-[#D4B996] text-sm font-semibold flex items-center gap-2 transition-all rounded-xl font-sans" type="submit" data-status="draft">
                    <span class="material-symbols-outlined text-base">draft</span>Сохранить черновик
                </button>
                <button class="bg-[#C85A32] hover:bg-[#B04924] text-white px-6 py-2.5 text-sm font-semibold flex items-center gap-2 transition-all rounded-xl shadow-sm font-sans" type="submit" data-status="publish">
                    <span class="material-symbols-outlined text-base">ink_pen</span>Опубликовать
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="site-toast" class="fixed bottom-6 right-6 z-50 hidden bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm text-warm-ink transition-all transform translate-y-2 rounded-2xl">
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-2xl text-terracotta shrink-0">verified</span>
        <div>
            <div id="toast-title" class="font-serif font-bold text-sm text-warm-ink">Уведомление</div>
            <div id="toast-message" class="text-xs text-warm-muted mt-0.5">Действие выполнено успешно.</div>
        </div>
    </div>
</div>

<?php
$fmtNum = function ($v) { return $v === null ? '' : rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'); };
$recipesJson = [];
foreach ($recipes as $r) {
    $recipesJson[$r->id] = [
        'id' => (int)$r->id,
        'title' => $r->title,
        'category_id' => (int)$r->category_id,
        'description' => (string)$r->description,
        'difficulty' => $r->difficulty,
        'servings' => $r->servings ?: 4,
        'prep_time_min' => $r->prep_time_min,
        'cook_time_min' => $r->cook_time_min,
        'equipment' => (string)$r->equipment,
        'calories' => $r->calories,
        'protein' => $fmtNum($r->protein),
        'fat' => $fmtNum($r->fat),
        'carbs' => $fmtNum($r->carbs),
        'secret' => (string)$r->secret,
        'photo' => $photoUrl($r->photo),
        'status' => $r->status,
        'ingredients' => array_values(array_map(function ($ri) use ($fmtNum) {
            return ['name' => $ri->ingredient ? $ri->ingredient->name : '', 'quantity' => $fmtNum($ri->quantity), 'unit' => (string)$ri->unit];
        }, $r->recipeIngredients)),
        'steps' => array_values(array_map(function ($st) use ($photoUrl) {
            return ['title' => (string)$st->title, 'description' => (string)$st->description, 'duration_min' => $st->duration_min, 'photo' => (string)$st->photo, 'photo_url' => $photoUrl($st->photo)];
        }, $r->recipeSteps)),
    ];
}
?>
<script>
window.RECIPE_FORM_CONFIG = {
    recipes: <?= json_encode((object)$recipesJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,
    saveUrl: <?= json_encode(Url::to(['site/save-recipe'])) ?>,
    csrf: <?= json_encode(Yii::$app->request->csrfToken) ?>
};
</script>
<script src="<?= Yii::getAlias('@web') ?>/js/recipe-form.js"></script>
<script>
(function () {
    var csrf = <?= json_encode(Yii::$app->request->csrfToken) ?>;
    var toggleUrl = <?= json_encode(Url::to(['site/toggle-shopping-item'])) ?>;

    // Сохранение отметки «куплено» в БД
    document.querySelectorAll('[data-shopping-id]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var body = new URLSearchParams({ bought: cb.checked ? 1 : 0, _csrf: csrf });
            fetch(toggleUrl + '?id=' + cb.dataset.shoppingId, { method: 'POST', body: body, headers: { 'X-CSRF-Token': csrf } })
                .then(function (r) { if (!r.ok) throw new Error(); })
                .catch(function () { cb.checked = !cb.checked; if (window.toggleShoppingItem) toggleShoppingItem(cb); alert('Не удалось сохранить изменение'); });
        });
    });

        // Аватар в окне редактирования профиля: превью и удаление
    var pInput = document.getElementById('profile-photo-input');
    if (pInput) {
        var pPlace = document.getElementById('profile-upload-placeholder');
        var pPrev = document.getElementById('profile-preview-container');
        var pImg = document.getElementById('profile-preview-img');
        var pLabel = document.getElementById('profile-preview-label');
        var pFlag = document.getElementById('profile-remove-avatar-flag');
        function pShow(src, label) {
            pImg.src = src; pLabel.textContent = label;
            pPlace.classList.add('hidden'); pPrev.classList.remove('hidden'); pPrev.classList.add('flex');
        }
        pInput.addEventListener('change', function () {
            var f = pInput.files[0];
            if (!f) return;
            if (f.size > 10 * 1024 * 1024) { alert('Фото больше 10 МБ'); pInput.value = ''; return; }
            pFlag.value = '0';
            var rd = new FileReader();
            rd.onload = function (ev) { pShow(ev.target.result, 'Фото выбрано'); };
            rd.readAsDataURL(f);
        });
        document.getElementById('profile-remove-photo').addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            pInput.value = ''; pFlag.value = '1'; pImg.src = '';
            pPrev.classList.add('hidden'); pPrev.classList.remove('flex'); pPlace.classList.remove('hidden');
        });
    }

    // Избранное: убрать рецепт из закладок
    document.querySelectorAll('.fav-remove').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var body = new URLSearchParams({ _csrf: csrf });
            fetch(<?= json_encode(Url::to(['site/toggle-favorite'])) ?> + '?id=' + btn.dataset.id, { method: 'POST', body: body, headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' } })
                .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                .then(function (r) {
                    if (r.favorite) { return; } // на случай рассинхронизации
                    btn.closest('.fav-card').remove();
                    var n = document.querySelectorAll('.fav-card').length;
                    document.getElementById('tab-fav-count').textContent = n;
                    document.getElementById('stat-favorites').textContent = n;
                    if (!n) document.getElementById('fav-empty').classList.remove('hidden');
                })
                .catch(function () { alert('Не удалось изменить избранное'); });
        });
    });

    // Поиск и фильтр по категории среди «Моих рецептов»
    var search = document.getElementById('recipe-search-input');
    var cat = document.getElementById('recipe-category-filter');
    function applyFilter() {
        var q = (search.value || '').trim().toLowerCase();
        document.querySelectorAll('.my-recipe-card').forEach(function (c) {
            var ok = (!q || c.dataset.title.indexOf(q) !== -1) && (!cat.value || c.dataset.category === cat.value);
            c.style.display = ok ? '' : 'none';
        });
    }
    if (search && cat) { search.addEventListener('input', applyFilter); cat.addEventListener('change', applyFilter); }

    <?php if ($hasErrors): ?>
    // если при сохранении профиля были ошибки — снова открываем окно
    window.addEventListener('load', function () { if (window.openEditProfileModal) openEditProfileModal(); });
    <?php endif; ?>
})();
</script>
