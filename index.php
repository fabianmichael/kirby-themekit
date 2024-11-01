<?php

use Kirby\Cms\App;
use FabianMichael\Themes\Theme;
use FabianMichael\Themes\Themes;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('fabianmichael/themes', [
	'options' => [
		'custom' => true,
		'themes' => [
			// 'light' => [
			// 	'name' => 'Light',
			// 	'background' => '#fff',
			// 	'default' => true,
			// ],
			// 'dark' => [
			// 	'name' => 'Dark',
			// 	'color' => '#000',
			// ],
		],
	],

	'blueprints' => [
		'fields/theme-group' => require __DIR__ . '/blueprints/fields/theme-group.php',
		'tabs/themes/site' => require __DIR__ . '/blueprints/tabs/themes/site.php',
	],

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
	],
]);
