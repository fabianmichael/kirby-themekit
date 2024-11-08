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
			'colorValue' => '{h} {s} {l}%',
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
			// [
			//	'slug' => 'light',
			// 	'name' => 'Light',
			// 	'background' => '#fff',
			// 	'default' => true,
			// ],
			// [
			//	'slug' => 'dark',
			// 	'name' => 'Dark',
			// 	'color' => '#000',
			// ],
		],
		'fields' => [],
	],

	'areas' => require __DIR__ . '/config/areas.php',

	'blueprints' => [
		'fields/theme-group' => require __DIR__ . '/blueprints/fields/theme-group.php',
	],

	'fields' => require __DIR__ . '/config/fields.php',

	'layoutMethods' => [
		'theme' => function () {
			return Theme::from($this);
		}
	],

	'pageMethods' => [
		'theme' => function () {
			return Theme::from($this);
		},
	],

	'siteMethods' => [
		'themes' => function () {
			return Themes::instance();
		},
	],

	'snippets' => [
		'themes' => __DIR__ . '/snippets/themes.php',
		'themes/css-rule' => __DIR__ . '/snippets/themes/css-rule.php',
		'themes/css-properties' => __DIR__ . '/snippets/themes/css-properties.php',
	],
]);
