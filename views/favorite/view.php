<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Favorite $model */
$this->title = 'Избранное';
$u = $model->user; $r = $model->recipe;
$rows = [
    ['Пользователь', $u ? '<a class="text-terracotta font-semibold hover:underline" href="' . Html::encode(Url::to(['user/view', 'id' => $u->id])) . '">' . Html::encode($u->username) . '</a> (' . Html::encode($u->fullname) . ')' : ''],
    ['Рецепт', $r ? '<a class="text-terracotta font-semibold hover:underline" href="' . Html::encode(Url::to(['recipe/view', 'id' => $r->id])) . '">' . Html::encode($r->title) . '</a>' : ''],
    ['Добавлено', $model->created_at ? date('d.m.Y H:i', strtotime($model->created_at)) : ''],
];
$actions = Html::beginForm(['favorite/delete', 'user_id' => $model->user_id, 'recipe_id' => $model->recipe_id], 'post', ['class' => 'inline'])
    . '<button class="adm-btn is-danger" type="submit" onclick="return confirm(\'Убрать рецепт из избранного пользователя?\');">Удалить</button>'
    . Html::endForm();
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Избранное', ['favorite/index']], ['Запись', null]],
        'eyebrow' => 'Избранное пользователя',
        'title' => $r ? $r->title : 'Запись',
        'actions' => $actions,
    ]) ?>
    <div class="adm-card">
        <dl class="adm-dl">
            <?php foreach ($rows as $row): ?>
            <div><dt><?= $row[0] ?></dt><dd><?= $row[1] !== '' ? $row[1] : '—' ?></dd></div>
            <?php endforeach; ?>
        </dl>
    </div>
</section>
</main>
