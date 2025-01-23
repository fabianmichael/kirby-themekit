<?php

use Kirby\Cms\App;
use FabianMichael\ThemeKit\Theme;
use FabianMichael\ThemeKit\Themes;
use Kirby\Toolkit\A;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('fabianmichael/themekit', [
	'options' => [
		'customThemes' => true,
		'contrastLight' => '#ffffff',
		'contrastDark' => '#000000',
		'css' => [
			'selector' => '[data-theme="{slug}"]',
			'propertyName' => '--theme--{name}',
			'colorValue' => 'hsl({h} {s}% {l}%{/alpha})',
		],
		'previewColors' => function (): array {
			$previews = [];
			foreach (Theme::fields() as $name => $field) {
				if (in_array($name, ['background', 'foreground'])) {
					continue;
				}

				if (A::get($field, 'type') === 'color') {
					$previews[] = $name;
				}
			}
			return array_slice($previews, 0, 2);
		},
		'themes' => [
			'light' => [
				'title' => 'Light',
				'background' => '#fff',
				'default' => true,
			],
			'dark' => [
				'title' => 'Dark',
				'background' => '#000',
			],
		],
		'fields' => [],
	],

	'areas' => require __DIR__ . '/config/areas.php',

	'blueprints' => [
		'fields/theme-group' => require __DIR__ . '/blueprints/fields/theme-group.php',
		'themekit/fields/theme-group' => require __DIR__ . '/blueprints/fields/theme-group.php',
	],

	'fields' => require __DIR__ . '/config/fields.php',

	'blockMethods' => [
		'theme' => function () {
			return Theme::from($this);
		}
	],

	'layoutMethods' => [
		'theme' => function () {
			return Theme::from($this);
		}
	],

	'pageMethods' => [
		'theme' => function () {
			return Theme::from($this) ?? themes()->default();
		},
	],

	'siteMethods' => [
		'themes' => function () {
			return Themes::instance();
		},
	],

	'snippets' => [
		'themes' => __DIR__ . '/snippets/themes.php',
		'themekit/themes' => __DIR__ . '/snippets/themes.php',
		'themes/css-rule' => __DIR__ . '/snippets/themes/css-rule.php',
		'themekit/themes/css-rule' => __DIR__ . '/snippets/themes/css-rule.php',
		'themes/css-properties' => __DIR__ . '/snippets/themes/css-properties.php',
		'themekit/themes/css-properties' => __DIR__ . '/snippets/themes/css-properties.php',
	],
]);
