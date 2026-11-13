<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Recipe[] $recipes */
/** @var yii\data\Pagination $pages */
/** @var string $q */
/** @var string $status */
/** @var int $categoryId */
/** @var app\models\Category[] $categories */
$this->title = 'Рецепты';
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Рецепты', null]],
        'eyebrow' => 'Всего записей: ' . (int)$pages->totalCount,
        'title' => 'Рецепты',
    ]) ?>

    <form method="get" action="<?= Html::encode(Url::to(['recipe/index'])) ?>" class="adm-filters">
        <div style="flex-grow:3">
            <label class="adm-label" for="f-q">Поиск по названию</label>
            <input class="adm-input" id="f-q" name="q" value="<?= Html::encode($q) ?>" placeholder="Например, борщ">
        </div>
        <div>
            <label class="adm-label" for="f-cat">Категория</label>
            <select class="adm-input" id="f-cat" name="category">
                <option value="">Все</option>
                <?php foreach ($categories as $c): ?><option value="<?= (int)$c->id ?>" <?= $categoryId === (int)$c->id ? 'selected' : '' ?>><?= Html::encode($c->name) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="adm-label" for="f-st">Статус</label>
            <select class="adm-input" id="f-st" name="status">
                <option value="">Все</option>
                <option value="Опубликовано" <?= $status === 'Опубликовано' ? 'selected' : '' ?>>Опубликовано</option>
                <option value="Черновик" <?= $status === 'Черновик' ? 'selected' : '' ?>>Черновик</option>
            </select>
        </div>
        <button class="adm-btn is-primary" type="submit"><span class="material-symbols-outlined text-base leading-none">search</span>Найти</button>
        <?php if ($q !== '' || $status !== '' || $categoryId): ?><a class="adm-btn" href="<?= Html::encode(Url::to(['recipe/index'])) ?>">Сбросить</a><?php endif; ?>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>ID</th><th></th><th>Название</th><th>Категория</th><th>Автор</th><th>Сложность</th><th>Статус</th><th>Создан</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recipes as $r): $ph = \app\models\Recipe::photoUrl($r->photo); ?>
                <tr>
                    <td class="font-mono text-xs"><?= (int)$r->id ?></td>
                    <td><div class="adm-thumb"><?php if ($ph): ?><img alt="" src="<?= Html::encode($ph) ?>" style="width:100%;height:100%;object-fit:cover"><?php else: ?><span class="material-symbols-outlined text-xl">restaurant_menu</span><?php endif; ?></div></td>
                    <td class="font-semibold" style="max-width:22rem"><?= Html::encode($r->title) ?></td>
                    <td><?= $r->category ? Html::encode($r->category->name) : '—' ?></td>
                    <td><?php if ($r->user): ?><a class="text-terracotta hover:underline" href="<?= Html::encode(Url::to(['user/view', 'id' => $r->user_id])) ?>"><?= Html::encode($r->user->username) ?></a><?php else: ?>—<?php endif; ?></td>
                    <td class="whitespace-nowrap"><?= Html::encode($r->difficulty) ?></td>
                    <td><span class="adm-pill <?= $r->status === 'Опубликовано' ? 'is-ok' : '' ?>"><?= Html::encode($r->status) ?></span></td>
                    <td class="whitespace-nowrap"><?= $r->created_at ? date('d.m.Y', strtotime($r->created_at)) : '—' ?></td>
                    <td>
                        <div class="adm-row-actions">
                            <a class="adm-btn" href="<?= Html::encode(Url::to(['recipe/view', 'id' => $r->id])) ?>">Открыть</a>
                            <a class="adm-btn" href="<?= Html::encode(Url::to(['recipe/update', 'id' => $r->id])) ?>">Изменить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recipes): ?><tr><td colspan="9" class="text-center text-warm-muted py-8">Рецептов не найдено.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
        <span class="text-xs text-warm-muted font-mono">Показано <?= count($recipes) ?> из <?= (int)$pages->totalCount ?></span>
        <?= $this->render('//admin/_pager', ['pages' => $pages]) ?>
    </div>
</section>
</main>
