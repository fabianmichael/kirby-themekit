<?php

use FabianMichael\ThemeKit\Theme;
use FabianMichael\ThemeKit\Themes;

return function (): array {
	$themes = Themes::instance();
	$options = [];

	foreach ($themes as $theme) {
		$options[] = [
			'value' => $theme->slug(),
			'text' => $theme->title(),
		];
	}

	$themesField = [
		'type' => 'theme-selector',
		'label' => 'Color theme',
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
					'label' => 'Custom theme',
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
