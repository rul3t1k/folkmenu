<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Recipe $model */
/** @var app\models\Category[] $categories */
$this->title = 'Редактирование: ' . $model->title;
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Рецепты', ['recipe/index']], [$model->title, ['recipe/view', 'id' => $model->id]], ['Редактирование', null]],
        'eyebrow' => 'Рецепт № ' . (int)$model->id,
        'title' => 'Модерация рецепта',
    ]) ?>
    <p class="text-sm text-warm-muted -mt-4 mb-6">Здесь меняются основные поля и статус публикации. Ингредиенты и шаги редактирует автор в своём профиле.</p>

    <form method="post" class="adm-card flex flex-col gap-5">
        <input type="hidden" name="_csrf" value="<?= Html::encode(Yii::$app->request->csrfToken) ?>">
        <div>
            <label class="adm-label" for="r-title">Название</label>
            <input class="adm-input" id="r-title" name="Recipe[title]" value="<?= Html::encode($model->title) ?>" maxlength="255">
            <?php if ($model->getFirstError('title')): ?><div class="adm-err"><?= Html::encode($model->getFirstError('title')) ?></div><?php endif; ?>
        </div>
        <div>
            <label class="adm-label" for="r-desc">Описание</label>
            <textarea class="adm-input" id="r-desc" name="Recipe[description]" rows="4"><?= Html::encode($model->description) ?></textarea>
        </div>
        <div class="adm-form-3">
            <div>
                <label class="adm-label" for="r-cat">Категория</label>
                <select class="adm-input" id="r-cat" name="Recipe[category_id]">
                    <?php foreach ($categories as $c): ?><option value="<?= (int)$c->id ?>" <?= (int)$model->category_id === (int)$c->id ? 'selected' : '' ?>><?= Html::encode($c->name) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="adm-label" for="r-diff">Сложность</label>
                <select class="adm-input" id="r-diff" name="Recipe[difficulty]">
                    <?php foreach (['Простая', 'Средняя', 'Для терпеливых'] as $d): ?><option <?= $model->difficulty === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="adm-label" for="r-st">Статус</label>
                <select class="adm-input" id="r-st" name="Recipe[status]">
                    <?php foreach (['Опубликовано', 'Черновик'] as $d): ?><option <?= $model->status === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php if ($model->hasErrors()): ?><div class="adm-err"><?= Html::encode(implode(' ', $model->getFirstErrors())) ?></div><?php endif; ?>
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-dashed border-craft-border">
            <a class="adm-btn" href="<?= Html::encode(Url::to(['recipe/view', 'id' => $model->id])) ?>">Отмена</a>
            <button class="adm-btn is-primary" type="submit">Сохранить</button>
        </div>
    </form>
</section>
</main>
