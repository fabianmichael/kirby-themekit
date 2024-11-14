<?php

use FabianMichael\ThemeKit\Styles;

$rules = [];

$page->theme()->use();

foreach (Styles::stack() as $theme) {
    $rules[] = snippet('themes/css-rule', [
        'theme' => $theme,
    ], return: true);
}

if (count($rules) === 0) {
    return;
}

?>

<style>
<?= implode(PHP_EOL, $rules) ?>
</style>
