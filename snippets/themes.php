<?php

use FabianMichael\Themes\Styles;

Styles::push($page->theme());

$styles = Styles::render();

if (!empty($styles)) {
    echo '<style>' . PHP_EOL . $styles . PHP_EOL . '</style>';
}
