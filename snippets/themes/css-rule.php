<?php

use Kirby\Toolkit\Str;

/** @var \FabianMichael\ThemeKit\Theme $theme */

echo '  ' . Str::template(
    $kirby->option('fabianmichael.themekit.css.selector'),
    ['slug' => $theme->slug()]
);
echo ' { ';
echo PHP_EOL;

snippet('themes/css-properties', [
    'theme' => $theme,
]);

echo '  }';
echo PHP_EOL;
