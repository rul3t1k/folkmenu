<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\LoginForm $model */
?>
<!-- ================= MAIN CONTENT: AUTH PAGE ================= -->
<main class="w-full pt-24 pb-12 flex-1">
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        
        <!-- Auth Card Container -->
        <div class="relative max-w-md mx-auto my-6 bg-surface border-2 border-[#dec0b7] shadow-scrapbook p-8 sm:p-10 rounded-3xl">
            
            <!-- Vintage Accents: Tape & Paperclip -->
            <div class="washi-tape washi-honey -top-4 left-1/2 -translate-x-1/2 w-32 h-6 -rotate-1 pointer-events-none rounded-xs"></div>
            <div class="absolute -top-5 right-10 z-20 pointer-events-none flex items-center justify-center">
                <span class="material-symbols-outlined text-warm-muted text-3xl transform rotate-45 drop-shadow-xs">attach_file</span>
            </div>

            <!-- Tab Switches -->
            <div class="flex border-b border-[#dec0b7] mb-8">
                <a class="flex-1 pb-3 text-center font-serif text-xl font-bold text-terracotta border-b-2 border-terracotta transition-all duration-200 cursor-pointer" href="<?= Url::to(['site/login']) ?>">Вход</a>
                <a class="flex-1 pb-3 text-center font-serif text-xl font-medium text-warm-muted hover:text-warm-ink border-b-2 border-transparent transition-all duration-200 cursor-pointer" href="<?= Url::to(['site/registration']) ?>">Регистрация</a>
            </div>

            <!-- Login Section -->
            <div id="auth-login-section">
                <div class="mb-6 text-center">
                    <h1 class="font-serif text-2xl sm:text-3xl font-bold text-warm-ink mb-1.5">Вход в личный кабинет</h1>
                    <p class="font-sans text-xs text-warm-muted">Войдите, чтобы сохранять рецепты и список покупок</p>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'enableClientValidation' => false,
                    'options' => ['class' => 'flex flex-col gap-4'],
                    'fieldConfig' => [
                        'options' => ['class' => 'flex flex-col gap-1.5'],
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'font-mono text-xs font-bold uppercase tracking-wider text-warm-ink'],
                        'errorOptions' => ['class' => 'text-xs text-red-600 font-sans'],
                    ],
                ]); ?>
                    <?= $form->field($model, 'username')->textInput([
                        'class' => 'w-full bg-[#FFF9F4] border border-[#dec0b7] p-3 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors',
                        'placeholder' => 'Логин или почта',
                        'autocomplete' => 'username',
                        'autofocus' => true,
                    ]) ?>

                    <?= $form->field($model, 'password', [
                        'template' => "<div class=\"flex justify-between items-center\">{label}</div>\n<div class=\"relative flex items-center\">{input}<button class=\"absolute right-3 text-warm-muted hover:text-warm-ink p-1 cursor-pointer flex items-center toggle-pass-btn\" data-target=\"loginform-password\" type=\"button\"><span class=\"material-symbols-outlined text-lg leading-none\">visibility</span></button></div>\n{error}",
                    ])->passwordInput([
                        'class' => 'w-full bg-[#FFF9F4] border border-[#dec0b7] p-3 text-sm text-warm-ink placeholder-[#8a726a] outline-none focus:border-terracotta font-sans rounded-xl transition-colors pr-10',
                        'placeholder' => '••••••••••••',
                        'autocomplete' => 'current-password',
                    ]) ?>

                    <?= Html::submitButton('<span>Войти</span>', ['class' => "w-full mt-2 py-3.5 px-6 bg-terracotta hover:bg-terracotta-hover text-white font-sans text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow-md transition-all rounded-xl cursor-pointer flex items-center justify-center gap-2"]) ?>
                <?php ActiveForm::end(); ?>
                <div class="mt-6 text-center text-xs font-sans text-warm-muted">
                    Еще нет аккаунта? <a class="text-terracotta font-bold hover:underline cursor-pointer" href="<?= Url::to(['site/registration']) ?>">Зарегистрироваться</a>
                </div>
            </div>
            
        </div>
    </div>
</main>

<!-- Toast Notification Box -->
<div class="fixed bottom-6 right-6 z-50 bg-[#FAF4EB] border-2 border-terracotta shadow-scrapbook p-4 max-w-sm hidden rounded-2xl" id="auth-toast">
    <div class="flex items-start gap-3">
        <span class="material-symbols-outlined text-terracotta text-2xl shrink-0">verified</span>
        <div class="flex flex-col">
            <span class="font-serif font-bold text-base text-warm-ink" id="toast-title">Folk Menu</span>
            <span class="font-sans text-xs text-warm-muted mt-0.5" id="toast-message">Уведомление</span>
        </div>
    </div>
</div>