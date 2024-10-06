<?php
use veejay\api\Route;
use veejay\api\View;

/* @var View $this */
/* @var Route $route */
?>
<details>
    <summary class="row">
        <span class="label label-<?= strtolower($route->method) ?>">
            <?= $route->method ?>
        </span>
        <span>
            <?= $route->getUri() ?>
        </span>
    </summary>

    <?php if ($route->description): ?>
        <p>
            <b>Описание:</b>
        </p>
        <p>
            <?= $route->description ?>
        </p>
    <?php endif ?>

    <p>
        <b>Параметры:</b>
    </p>
    <?php if ($route->params): ?>
        <?= $this->render(__DIR__ . '/_params.php', [
            'route' => $route,
        ]) ?>
        <?php if ($route->getRequired()): ?>
            <small>
                * параметры, отмеченные звездочкой, являются обязательными
            </small>
        <?php endif ?>
    <?php else: ?>
        Отсутствуют
    <?php endif ?>

    <p>
        <b>Вернет:</b>
    </p>
    <?php if ($route->returns): ?>
        <?= $this->render(__DIR__ . '/_returns.php', [
            'route' => $route,
        ]) ?>
    <?php else: ?>
        Не указано
    <?php endif ?>
</details>
