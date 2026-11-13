<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Favorite */

$this->title = 'Update Favorite: ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Favorites', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['view', 'user_id' => $model->user_id, 'recipe_id' => $model->recipe_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="favorite-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
