<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Recipe $model */
/** @var int $favCount */
$this->title = $model->title;
$ph = \app\models\Recipe::photoUrl($model->photo);
$num = function ($v) { return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'); };
$rows = [
    ['ID', (int)$model->id],
    ['Категория', $model->category ? Html::encode($model->category->name) : ''],
    ['Автор', $model->user ? '<a class="text-terracotta font-semibold hover:underline" href="' . Html::encode(Url::to(['user/view', 'id' => $model->user_id])) . '">' . Html::encode($model->user->username) . '</a>' : ''],
    ['Статус', '<span class="adm-pill ' . ($model->status === 'Опубликовано' ? 'is-ok' : '') . '">' . Html::encode($model->status) . '</span>'],
    ['Сложность', Html::encode($model->difficulty)],
    ['Порций', (int)($model->servings ?: 0) ?: ''],
    ['Подготовка / готовка', ($model->prep_time_min !== null ? (int)$model->prep_time_min : '—') . ' / ' . ($model->cook_time_min !== null ? (int)$model->cook_time_min : '—') . ' мин'],
    ['КБЖУ', $model->calories !== null ? (int)$model->calories . ' ккал · Б ' . $num($model->protein) . ' · Ж ' . $num($model->fat) . ' · У ' . $num($model->carbs) : ''],
    ['Оборудование', Html::encode($model->equipment)],
    ['Описание', nl2br(Html::encode($model->description))],
    ['Секрет хозяйки', nl2br(Html::encode($model->secret))],
    ['Создан', $model->created_at ? date('d.m.Y H:i', strtotime($model->created_at)) : ''],
    ['В избранном', '<a class="text-terracotta font-semibold hover:underline" href="' . Html::encode(Url::to(['favorite/index', 'q' => $model->title])) . '">' . $favCount . '</a>'],
];
$actions = '<a class="adm-btn" href="' . Html::encode(Url::to(['site/recipe', 'id' => $model->id])) . '" target="_blank">На сайте</a>'
    . '<a class="adm-btn is-primary" href="' . Html::encode(Url::to(['recipe/update', 'id' => $model->id])) . '">Изменить</a>'
    . Html::beginForm(['recipe/delete', 'id' => $model->id], 'post', ['class' => 'inline'])
    . '<button class="adm-btn is-danger" type="submit" onclick="return confirm(\'Удалить рецепт вместе с ингредиентами, шагами и избранным?\');">Удалить</button>'
    . Html::endForm();
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Рецепты', ['recipe/index']], [$model->title, null]],
        'eyebrow' => 'Рецепт № ' . (int)$model->id,
        'title' => $model->title,
        'actions' => $actions,
    ]) ?>

    <div class="adm-card adm-split">
        <div class="adm-side">
            <div class="w-full h-56 rounded-2xl overflow-hidden bg-paper border border-craft-border flex items-center justify-center">
                <?php if ($ph): ?><img alt="" class="w-full h-full object-cover" src="<?= Html::encode($ph) ?>">
                <?php else: ?><span class="material-symbols-outlined text-6xl text-[#dec0b7]">restaurant_menu</span><?php endif; ?>
            </div>
        </div>
        <dl class="adm-dl">
            <?php foreach ($rows as $r): ?>
            <div><dt><?= $r[0] ?></dt><dd><?= $r[1] !== '' && $r[1] !== null ? $r[1] : '—' ?></dd></div>
            <?php endforeach; ?>
        </dl>
    </div>

    <div class="adm-two">
        <div>
            <h2 class="font-serif text-2xl font-bold text-warm-ink mb-4">Ингредиенты (<?= count($model->recipeIngredients) ?>)</h2>
            <div class="adm-table-wrap">
                <table class="adm-table" style="min-width:0">
                    <thead><tr><th>Ингредиент</th><th>Количество</th><th>Примечание</th></tr></thead>
                    <tbody>
                    <?php foreach ($model->recipeIngredients as $ri): ?>
                        <tr>
                            <td class="font-semibold"><?= $ri->ingredient ? Html::encode($ri->ingredient->name) : '—' ?></td>
                            <td class="whitespace-nowrap"><?= Html::encode(trim(($ri->quantity !== null ? $num($ri->quantity) : '') . ' ' . $ri->unit)) ?: '—' ?></td>
                            <td><?= Html::encode($ri->note) ?: '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$model->recipeIngredients): ?><tr><td colspan="3" class="text-center text-warm-muted py-6">Нет ингредиентов.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <h2 class="font-serif text-2xl font-bold text-warm-ink mb-4">Шаги (<?= count($model->recipeSteps) ?>)</h2>
            <div class="flex flex-col gap-3">
                <?php foreach ($model->recipeSteps as $st): $sp = \app\models\Recipe::photoUrl($st->photo); ?>
                <div class="adm-card" style="padding:1rem">
                    <div class="flex items-center gap-2 text-xs font-bold text-terracotta uppercase tracking-wider">
                        Шаг <?= (int)$st->step_number ?>
                        <?php if ($st->duration_min): ?><span class="adm-pill">⏱ <?= (int)$st->duration_min ?> мин</span><?php endif; ?>
                    </div>
                    <?php if (trim((string)$st->title) !== ''): ?><div class="font-serif text-lg font-bold text-warm-ink mt-1"><?= Html::encode($st->title) ?></div><?php endif; ?>
                    <p class="text-sm text-warm-ink mt-1 leading-relaxed"><?= nl2br(Html::encode($st->description)) ?></p>
                    <?php if ($sp): ?><img alt="" class="mt-3 rounded-xl border border-craft-border" style="max-height:12rem" src="<?= Html::encode($sp) ?>"><?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if (!$model->recipeSteps): ?><p class="text-sm text-warm-muted">Шагов нет.</p><?php endif; ?>
            </div>
        </div>
    </div>
</section>
</main>
