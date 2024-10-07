<?php
use veejay\api\component\Route;
use veejay\api\component\View;

/* @var View $this */
/* @var Route $route */
?>
<ul>
    <?php foreach ($route->params as $name => $description): ?>
        <li>
            <?= $name ?><?= $route->isRequired($name) ? '*' : '' ?>
            -
            <?= $description ?>
        </li>
    <?php endforeach ?>
</ul>
