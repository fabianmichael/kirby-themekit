<?php

use FabianMichael\ThemeKit\Theme;
use FabianMichael\ThemeKit\Themes;

function themes(): Themes
{
	return Themes::instance();
}

function theme(?string $slug = null): ?Theme
{
	return ($slug !== null)
		? themes()->find($slug)
		: themes()->default();
}

function theme_fields(?string $fieldName = 'theme'): array {
	$themes = Themes::instance();

	$themesField = [
		'type' => 'theme-selector',
		'label' => t('themekit.fields.theme.label'),
		'default' => $themes->default()?->slug(),
		'translate' => false,
	];

	if (option('fabianmichael.themekit.customThemes') === true) {
		return [
			"{$fieldName}_custom" => [
				'type' => 'toggle',
				'label' => t('themekit.fields.custom.label'),
				'translate' => false,
			],
			$fieldName => array_merge(
				$themesField,
				[
					'when' => [
						"{$fieldName}_custom" => false,
					],
				],
			),
			...array_map(fn($theme) => [
				...$theme,
				'when' => [
					"{$fieldName}_custom" => true,
				],
			], Theme::fields("{$fieldName}_"))
		];
	}

	return [
		$fieldName => $themesField,
	];
}
