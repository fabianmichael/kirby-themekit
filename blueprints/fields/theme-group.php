<?php

use Kirby\Cms\App;
use FabianMichael\ThemeKit\Theme;
use FabianMichael\ThemeKit\Themes;

return function (App $kirby): array {
	$themes = Themes::instance();
	// $options = array_reduce(
	// 	$themes->toArray(),
	// 	function ($carry, $theme) {
	// 		$carry[] = [
	// 			'value' => $theme['slug'],
	// 			'text' => $theme['title'],
	// 		];
	// 		return $carry;
	// 	},
	// 	[]
	// );
	$options = [];
	foreach ($themes as $theme) {
		$options[] = [
			'value' => $theme->slug(),
			'text' => $theme->title(),
		];
	}

	$themesField = [
		'type' => 'theme-selector',
		'label' => 'Farbschema',
		'default' => $themes->default()?->slug(),
		'options' => $options,
		'translate' => false,
	];

	if (option('fabianmichael.themekit.customThemes') === true) {
		return [
			'type' => 'group',
			'fields' => [
				'theme_custom' => [
					'type' => 'toggle',
					'label' => 'Eigenes Farbschema',
					'translate' => false,
				],
				'theme' => array_merge(
					$themesField,
					[
						'when' => [
							'theme_custom' => false,
						],
					],
				),
				...array_map(fn($theme) => [
					...$theme,
					'when' => [
						'theme_custom' => true,
					],
				], Theme::fields('theme_'))
			],
		];
	}

	return [
		'type' => 'group',
		'fields' => [
			'theme' => $themesField,
		],
	];
};
