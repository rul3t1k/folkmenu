<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var array $crumbs  [[подпись, url|null], ...]
 * @var string $eyebrow
 * @var string $title
 * @var string $actions  готовый html кнопок справа (необязательно)
 */
$actions = $actions ?? '';
?>
<nav class="text-xs text-warm-muted mb-4 flex flex-wrap items-center gap-1.5" aria-label="Навигационная цепочка">
    <?php foreach ($crumbs as $i => $c): ?>
        <?php if ($i): ?><span>/</span><?php endif; ?>
        <?php if ($c[1] !== null): ?>
            <a class="hover:text-terracotta transition-colors font-semibold" href="<?= Html::encode(Url::to($c[1])) ?>"><?= Html::encode($c[0]) ?></a>
        <?php else: ?>
            <span class="text-warm-ink font-semibold"><?= Html::encode($c[0]) ?></span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-terracotta uppercase tracking-wider">
            <span class="w-6 h-0.5 bg-terracotta"></span> <?= Html::encode($eyebrow) ?>
        </div>
        <h1 class="font-serif text-3xl md:text-4xl font-bold text-warm-ink mt-1"><?= Html::encode($title) ?></h1>
    </div>
    <?php if ($actions): ?><div class="flex flex-wrap items-center gap-2"><?= $actions ?></div><?php endif; ?>
</div>
