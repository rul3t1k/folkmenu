<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var int[] $counts */
$this->title = 'Админ-панель';

$sections = [
    ['title' => 'Пользователи', 'text' => 'Учётные записи, роли и редактирование', 'url' => ['user/index'], 'icon' => 'group', 'count' => $counts['users'], 'cls' => 'bg-sage-light text-sage', 'note' => 'всего'],
    ['title' => 'Рецепты', 'text' => 'Каталог: ингредиенты, шаги, публикация и модерация', 'url' => ['recipe/index'], 'icon' => 'menu_book', 'count' => $counts['recipes'], 'cls' => 'bg-terracotta-light text-terracotta', 'note' => 'в т. ч. черновиков: ' . $counts['drafts']],
    ['title' => 'Избранное', 'text' => 'Рецепты, которые сохранили пользователи', 'url' => ['favorite/index'], 'icon' => 'favorite', 'count' => $counts['favorites'], 'cls' => 'bg-rose-light text-rose', 'note' => 'записей'],
];
?>
<main class="w-full pt-20 flex-1">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <?= $this->render('_head', [
            'crumbs' => [['Админ-панель', null]],
            'eyebrow' => 'Управление сайтом',
            'title' => 'Админ-панель',
        ]) ?>
        <p class="text-sm text-warm-muted -mt-4 mb-8">Разделы для работы с данными сайта.</p>

        <div class="adm-admin-grid">
            <?php foreach ($sections as $s): ?>
            <a class="group relative bg-white border border-craft-border rounded-2xl p-6 shadow-xs hover:shadow-card-lift hover:border-terracotta transition-all duration-300 flex flex-col justify-between" href="<?= Html::encode(Url::to($s['url'])) ?>">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl <?= $s['cls'] ?> flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-2xl"><?= $s['icon'] ?></span>
                        </div>
                        <span class="font-serif text-4xl font-bold text-warm-ink"><?= (int)$s['count'] ?></span>
                    </div>
                    <h2 class="font-serif text-2xl font-bold text-warm-ink group-hover:text-terracotta transition-colors"><?= Html::encode($s['title']) ?></h2>
                    <p class="text-sm text-warm-muted mt-2 leading-relaxed"><?= Html::encode($s['text']) ?></p>
                    <p class="text-xs text-warm-muted mt-2 font-mono"><?= Html::encode($s['note']) ?></p>
                </div>
                <div class="pt-5 mt-3 border-t border-craft-subtle flex items-center text-xs font-bold text-terracotta group-hover:translate-x-1 transition-transform">
                    <span>Открыть раздел</span>
                    <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>
