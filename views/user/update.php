<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $model */
$this->title = 'Редактирование: ' . $model->username;
$self = (int)$model->id === (int)Yii::$app->user->id;
$field = function ($attr, $label, $type = 'text') use ($model) {
    $err = $model->getFirstError($attr);
    return '<div><label class="adm-label" for="u-' . $attr . '">' . $label . '</label>'
        . '<input class="adm-input" id="u-' . $attr . '" name="User[' . $attr . ']" type="' . $type . '" value="' . Html::encode($model->$attr) . '">'
        . ($err ? '<div class="adm-err">' . Html::encode($err) . '</div>' : '') . '</div>';
};
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Пользователи', ['user/index']], [$model->username, ['user/view', 'id' => $model->id]], ['Редактирование', null]],
        'eyebrow' => 'Пользователь № ' . (int)$model->id,
        'title' => 'Данные пользователя',
    ]) ?>

    <form method="post" class="adm-card flex flex-col gap-5">
        <input type="hidden" name="_csrf" value="<?= Html::encode(Yii::$app->request->csrfToken) ?>">
        <?= $field('username', 'Логин') ?>
        <?= $field('fullname', 'ФИО') ?>
        <?= $field('mail', 'Электронная почта', 'email') ?>
        <?= $field('phone', 'Телефон') ?>
        <div>
            <label class="adm-label" for="u-bio">О себе</label>
            <textarea class="adm-input" id="u-bio" name="User[bio]" rows="3"><?= Html::encode($model->bio) ?></textarea>
        </div>
        <div>
            <label class="adm-label" for="u-role">Роль</label>
            <select class="adm-input" id="u-role" name="User[role]" <?= $self ? 'disabled' : '' ?>>
                <option value="0" <?= !$model->isAdmin ? 'selected' : '' ?>>Пользователь</option>
                <option value="1" <?= $model->isAdmin ? 'selected' : '' ?>>Админ</option>
            </select>
            <?php if ($self): ?><p class="text-xs text-warm-muted mt-1">Свою роль изменить нельзя.</p><?php endif; ?>
        </div>
        <div>
            <label class="adm-label" for="u-newPassword">Новый пароль</label>
            <input class="adm-input" id="u-newPassword" name="User[newPassword]" type="password" autocomplete="new-password" placeholder="Оставьте пустым, чтобы не менять">
            <?php if ($model->getFirstError('password')): ?><div class="adm-err"><?= Html::encode($model->getFirstError('password')) ?></div><?php endif; ?>
        </div>
        <?php if ($model->hasErrors() && !$model->getFirstError('password')): ?>
            <div class="adm-err"><?= Html::encode(implode(' ', $model->getFirstErrors())) ?></div>
        <?php endif; ?>
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-dashed border-craft-border">
            <a class="adm-btn" href="<?= Html::encode(Url::to(['user/view', 'id' => $model->id])) ?>">Отмена</a>
            <button class="adm-btn is-primary" type="submit">Сохранить</button>
        </div>
    </form>
</section>
</main>
