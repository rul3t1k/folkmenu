<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Recipe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recipe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'difficulty')->dropDownList([ 'Простая' => 'Простая', 'Средняя' => 'Средняя', 'Для терпеливых' => 'Для терпеливых', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'prep_time_min')->textInput() ?>

    <?= $form->field($model, 'cook_time_min')->textInput() ?>

    <?= $form->field($model, 'equipment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'secret')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'photo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'calories')->textInput() ?>

    <?= $form->field($model, 'protein')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'carbs')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'Черновик' => 'Черновик', 'Опубликовано' => 'Опубликовано', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
