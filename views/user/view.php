<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var app\models\Recipe[] $recipes */
/** @var int $favCount */
$this->title = $model->username;
$av = \app\models\Recipe::photoUrl($model->avatar);
$rows = [
    ['ID', (int)$model->id],
    ['Логин', Html::encode($model->username)],
    ['Email', Html::encode($model->mail)],
    ['ФИО', Html::encode($model->fullname)],
    ['Телефон', Html::encode($model->phone)],
    ['О себе', nl2br(Html::encode($model->bio))],
    ['Роль', '<span class="adm-pill ' . ($model->isAdmin ? 'is-warn' : '') . '">' . ($model->isAdmin ? 'Админ' : 'Пользователь') . '</span>'],
    ['Избранное', $favCount . ' (<a class="text-terracotta font-semibold hover:underline" href="' . Html::encode(Url::to(['favorite/index', 'q' => $model->username])) . '">смотреть</a>)'],
];
$actions = '<a class="adm-btn is-primary" href="' . Html::encode(Url::to(['user/update', 'id' => $model->id])) . '">Изменить</a>'
    . Html::beginForm(['user/delete', 'id' => $model->id], 'post', ['class' => 'inline'])
    . '<button class="adm-btn is-danger" type="submit" onclick="return confirm(\'Удалить пользователя?\');">Удалить</button>'
    . Html::endForm();
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Пользователи', ['user/index']], [$model->username, null]],
        'eyebrow' => 'Пользователь № ' . (int)$model->id,
        'title' => $model->username,
        'actions' => $actions,
    ]) ?>

    <div class="adm-card adm-split is-user">
        <div class="adm-side">
            <div class="w-28 h-28 rounded-2xl overflow-hidden bg-paper border border-craft-border flex items-center justify-center">
                <?php if ($av): ?><img alt="" class="w-full h-full object-cover" src="<?= Html::encode($av) ?>">
                <?php else: ?><span class="material-symbols-outlined text-5xl text-[#dec0b7]">person</span><?php endif; ?>
            </div>
        </div>
        <dl class="adm-dl">
            <?php foreach ($rows as $r): ?>
            <div><dt><?= $r[0] ?></dt><dd><?= $r[1] !== '' && $r[1] !== null ? $r[1] : '—' ?></dd></div>
            <?php endforeach; ?>
        </dl>
    </div>

    <h2 class="font-serif text-2xl font-bold text-warm-ink mt-10 adm-h2">Рецепты пользователя (<?= count($recipes) ?>)</h2>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>ID</th><th>Название</th><th>Статус</th><th>Создан</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recipes as $r): ?>
                <tr>
                    <td class="font-mono text-xs"><?= (int)$r->id ?></td>
                    <td class="font-semibold"><?= Html::encode($r->title) ?></td>
                    <td><span class="adm-pill <?= $r->status === 'Опубликовано' ? 'is-ok' : '' ?>"><?= Html::encode($r->status) ?></span></td>
                    <td class="whitespace-nowrap"><?= $r->created_at ? date('d.m.Y', strtotime($r->created_at)) : '—' ?></td>
                    <td><div class="adm-row-actions"><a class="adm-btn" href="<?= Html::encode(Url::to(['recipe/view', 'id' => $r->id])) ?>">Открыть</a></div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$recipes): ?><tr><td colspan="5" class="text-center text-warm-muted py-6">Рецептов нет.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
</main>
