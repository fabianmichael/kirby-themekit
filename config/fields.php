<?php

return [
	'theme-selector' => [
		'props' => [
			'default' => function (string $default = ''): string {
				return $default;
			},
			'none' => function (bool $none = true): bool {
				return $none;
			},
		],
		'computed' => [
			'themes' => function (): array {
				return themes()->toData();
			},
			'default' => function () {
				return $this->none
					? ''
					: $this->sanitizeOption($this->default);
			},
			'value' => function (): string {
				return $this->sanitizeOption($this->value) ?? '';
			},
			'previewColors' => function (): array {
				$colors = option('fabianmichael.themekit.previewColors');

				if (is_callable($colors)) {
					return $colors();
				}

				if (is_array($colors)) {
					return $colors;
				}

				return [];
			}
		],
		'methods' => [
			'sanitizeOption' => function (?string $option = null) {
				return in_array($option, themes()->pluck('slug')) ? $option : '';
			},
		],
	],
];
