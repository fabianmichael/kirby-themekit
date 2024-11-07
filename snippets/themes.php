<?php

use FabianMichael\ThemeKit\Styles;

$rules = [];

foreach (themes() as $theme) {
    Styles::push($theme);
}

Styles::push($page->theme());

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
