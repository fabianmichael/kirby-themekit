<?php

use Kirby\Toolkit\Str;

/** @var \FabianMichael\ThemeKit\Theme $theme */

$propertyName = $kirby->option('fabianmichael.themekit.css.propertyName');
$colorValue = $kirby->option('fabianmichael.themekit.css.colorValue');

foreach ($theme->colorsExport() as $name) {
    if (!$color = $theme->$name()) {
        continue;
    }

    echo '  ' . Str::template($propertyName, [
        'name' => str_replace('_', '-', $name),
    ]);
    echo ': ';
    echo Str::template($colorValue, [
        ...($v = $color->toValues()),
        '/alpha' => r($v['a'] < 100, ' / ' . $v['a'], ''),
    ]);
    echo ';';
    echo PHP_EOL;
}

echo '  ' . Str::template($propertyName, [
    'name' => 'color-scheme',
]) . ': ' . $theme->scheme() . ';' .PHP_EOL;
