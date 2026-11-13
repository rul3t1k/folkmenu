<?php
use yii\helpers\Html;

/** @var yii\data\Pagination $pages */
$count = $pages->getPageCount();
$cur = $pages->getPage();
if ($count <= 1) { return; }
$from = max(0, $cur - 2);
$to = min($count - 1, $cur + 2);
?>
<div class="adm-pager">
    <?php if ($cur > 0): ?><a href="<?= Html::encode($pages->createUrl($cur - 1)) ?>">←</a><?php else: ?><span class="is-disabled">←</span><?php endif; ?>
    <?php if ($from > 0): ?><a href="<?= Html::encode($pages->createUrl(0)) ?>">1</a><?php if ($from > 1): ?><span class="is-disabled">…</span><?php endif; ?><?php endif; ?>
    <?php for ($i = $from; $i <= $to; $i++): ?>
        <?php if ($i === $cur): ?><span class="is-active"><?= $i + 1 ?></span><?php else: ?><a href="<?= Html::encode($pages->createUrl($i)) ?>"><?= $i + 1 ?></a><?php endif; ?>
    <?php endfor; ?>
    <?php if ($to < $count - 1): ?><?php if ($to < $count - 2): ?><span class="is-disabled">…</span><?php endif; ?><a href="<?= Html::encode($pages->createUrl($count - 1)) ?>"><?= $count ?></a><?php endif; ?>
    <?php if ($cur < $count - 1): ?><a href="<?= Html::encode($pages->createUrl($cur + 1)) ?>">→</a><?php else: ?><span class="is-disabled">→</span><?php endif; ?>
</div>
