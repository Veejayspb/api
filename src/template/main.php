<?php
use veejay\api\Application;
use veejay\api\component\View;

/* @var View $this */
/* @var Application $application */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $application->name ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css" integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">

    <?= $this->renderCss(__DIR__ . '/style.css') ?>

</head>
<body>
<h1><?= $application->name ?></h1>

<?php foreach ($application->routes as $route): ?>
    <?= $this->render(__DIR__ . '/_row.php', [
        'route' => $route,
    ]) ?>
<?php endforeach ?>

<?= $this->renderJs(__DIR__ . '/script.js') ?>

</body>
</html>
