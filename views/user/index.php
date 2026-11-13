<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User[] $users */
/** @var yii\data\Pagination $pages */
/** @var string $q */
/** @var string $role */
/** @var int[] $recipeCounts */
$this->title = 'Пользователи';
?>
<main class="w-full pt-20 flex-1">
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <?= $this->render('//admin/_head', [
        'crumbs' => [['Админ-панель', ['/admin']], ['Пользователи', null]],
        'eyebrow' => 'Всего записей: ' . (int)$pages->totalCount,
        'title' => 'Пользователи',
    ]) ?>

    <form method="get" action="<?= Html::encode(Url::to(['user/index'])) ?>" class="adm-filters">
        <div style="flex-grow:3">
            <label class="adm-label" for="f-q">Поиск</label>
            <input class="adm-input" id="f-q" name="q" value="<?= Html::encode($q) ?>" placeholder="Логин, почта, ФИО или телефон">
        </div>
        <div>
            <label class="adm-label" for="f-role">Роль</label>
            <select class="adm-input" id="f-role" name="role">
                <option value="">Все</option>
                <option value="0" <?= $role === '0' ? 'selected' : '' ?>>Пользователь</option>
                <option value="1" <?= $role === '1' ? 'selected' : '' ?>>Админ</option>
            </select>
        </div>
        <button class="adm-btn is-primary" type="submit"><span class="material-symbols-outlined text-base leading-none">search</span>Найти</button>
        <?php if ($q !== '' || $role !== ''): ?><a class="adm-btn" href="<?= Html::encode(Url::to(['user/index'])) ?>">Сбросить</a><?php endif; ?>
    </form>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead><tr><th>ID</th><th>Логин</th><th>Email</th><th>ФИО</th><th>Телефон</th><th>Рецептов</th><th>Роль</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td class="font-mono text-xs"><?= (int)$u->id ?></td>
                    <td class="font-semibold"><?= Html::encode($u->username) ?></td>
                    <td><?= Html::encode($u->mail) ?></td>
                    <td><?= Html::encode($u->fullname) ?></td>
                    <td class="whitespace-nowrap"><?= $u->phone !== null && $u->phone !== '' ? Html::encode($u->phone) : '—' ?></td>
                    <td><?= (int)($recipeCounts[$u->id] ?? 0) ?></td>
                    <td><span class="adm-pill <?= $u->isAdmin ? 'is-warn' : '' ?>"><?= $u->isAdmin ? 'Админ' : 'Пользователь' ?></span></td>
                    <td>
                        <div class="adm-row-actions">
                            <a class="adm-btn" href="<?= Html::encode(Url::to(['user/view', 'id' => $u->id])) ?>">Открыть</a>
                            <a class="adm-btn" href="<?= Html::encode(Url::to(['user/update', 'id' => $u->id])) ?>">Изменить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$users): ?><tr><td colspan="8" class="text-center text-warm-muted py-8">Пользователей не найдено.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
        <span class="text-xs text-warm-muted font-mono">Показано <?= count($users) ?> из <?= (int)$pages->totalCount ?></span>
        <?= $this->render('//admin/_pager', ['pages' => $pages]) ?>
    </div>
</section>
</main>
