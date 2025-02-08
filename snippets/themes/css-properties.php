<?php

use Kirby\Toolkit\Str;

/** @var \FabianMichael\ThemeKit\Theme $theme */

$propertyName = $kirby->option('fabianmichael.themekit.css.propertyName');

foreach ($theme->colorsExport() as $name) {
    if (!$color = $theme->$name()) {
        continue;
    }

    echo '    ' . Str::template($propertyName, [
        'name' => str_replace('_', '-', $name),
    ]);
    echo ": {$color};";
    echo PHP_EOL;
}

echo '    ' . Str::template($propertyName, [
    'name' => 'color-scheme',
]) . ': ' . $theme->scheme() . ';' .PHP_EOL;
