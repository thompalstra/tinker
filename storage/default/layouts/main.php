<?php
use Hub\Http\View;
?>
<!DOCTYPE html>
<html>
    <head>
        <?= View::make('default/layouts/main/head') ?>
        <?= View::make('default/layouts/main/assets') ?>
    </head>
    <body>
        <?= View::make('default/layouts/main/nav') ?>
        <?= View::make('default/layouts/main/sidebar') ?>
        <?= $content?>
        <?= View::make('default/layouts/main/footer') ?>
    </body>
</html>
