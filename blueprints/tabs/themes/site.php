<?php

use Kirby\Cms\App;
use FabianMichael\Themes\Theme;

return function (App $kirby): array|false
{
	return [
		'label' => 'Themes',
		'icon' => 'palette',
		'fields' => [
			'themes' => [
				'type' => 'blocks',
				'label' => 'Color themes',
				'fieldsets' => [
					'theme' => [
						'name' => 'Theme',
						'label' => '{{ title }}',
						'fields' => [
							'title' => [
								'type' => 'text',
								'label' => 'Title',
								'required' => true,
							],
							...Theme::fields(),
						],
					],
				],
			],
		],
	];
};
