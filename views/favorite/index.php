<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Favorite[] $items */
/** @var yii\data\Pagination $pages */
/** @var string $q */
$this->title = 'Избранное';
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Избранное', null]],
        'eyebrow' => 'Всего записей: ' . (int)$pages->totalCount,
        'title' => 'Избранное',
    ]) ?>

    <form method="get" action="<?= Html::encode(Url::to(['favorite/index'])) ?>" class="adm-filters">
        <div style="flex-grow:3">
            <label class="adm-label" for="f-q">Поиск</label>
            <input class="adm-input" id="f-q" name="q" value="<?= Html::encode($q) ?>" placeholder="Логин, ФИО или название рецепта">
        </div>
        <button class="adm-btn is-primary" type="submit"><span class="material-symbols-outlined text-base leading-none">search</span>Найти</button>
        <?php if ($q !== ''): ?><a class="adm-btn" href="<?= Html::encode(Url::to(['favorite/index'])) ?>">Сбросить</a><?php endif; ?>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>Пользователь</th><th>Рецепт</th><th>Добавлено</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $f): ?>
                <tr>
                    <td><?php if ($f->user): ?><a class="text-terracotta font-semibold hover:underline" href="<?= Html::encode(Url::to(['user/view', 'id' => $f->user_id])) ?>"><?= Html::encode($f->user->username) ?></a><?php else: ?>—<?php endif; ?></td>
                    <td><?php if ($f->recipe): ?><a class="hover:text-terracotta font-semibold" href="<?= Html::encode(Url::to(['recipe/view', 'id' => $f->recipe_id])) ?>"><?= Html::encode($f->recipe->title) ?></a><?php else: ?>—<?php endif; ?></td>
                    <td class="whitespace-nowrap"><?= $f->created_at ? date('d.m.Y H:i', strtotime($f->created_at)) : '—' ?></td>
                    <td>
                        <div class="adm-row-actions">
                            <a class="adm-btn" href="<?= Html::encode(Url::to(['favorite/view', 'user_id' => $f->user_id, 'recipe_id' => $f->recipe_id])) ?>">Открыть</a>
                            <?= Html::beginForm(['favorite/delete', 'user_id' => $f->user_id, 'recipe_id' => $f->recipe_id], 'post', ['class' => 'inline']) ?>
                                <button class="adm-btn is-danger" type="submit" onclick="return confirm('Убрать рецепт из избранного пользователя?');">Удалить</button>
                            <?= Html::endForm() ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?><tr><td colspan="4" class="text-center text-warm-muted py-8">Записей не найдено.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
        <span class="text-xs text-warm-muted font-mono">Показано <?= count($items) ?> из <?= (int)$pages->totalCount ?></span>
        <?= $this->render('//admin/_pager', ['pages' => $pages]) ?>
    </div>
</section>
</main>
