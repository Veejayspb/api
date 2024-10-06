<?php
use veejay\api\Route;
use veejay\api\View;

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
