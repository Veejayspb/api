<?php
use veejay\api\Route;
use veejay\api\View;

/* @var View $this */
/* @var Route $route */
?>
<table class="pure-table">
    <colgroup>
        <col width="200">
    </colgroup>
    <thead>
    <tr>
        <th>Код ответа</th>
        <th>Данные</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($route->returns as $code => $data): ?>
        <tr>
            <td>
                <?= $code ?>
            </td>
            <td>
                <pre>
                    <?= json_encode($data) ?>
                </pre>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
