<?php

use FabianMichael\ThemeKit\Panel\ThemeCreateDrawer;
use FabianMichael\ThemeKit\Panel\ThemeEditDrawer;
use FabianMichael\ThemeKit\Panel\View;
use FabianMichael\ThemeKit\Themes;
use Kirby\Cms\App;

return [
	'themes' => function (App $kirby) {
		if (! $kirby->option('fabianmichael.themekit.customThemes')) {
			// don’t show panel area when custom themes are disabled
			return [];
		}

		return [
			'label' => 'Themes',
			'icon'  => 'palette',
			'menu'  => true,
			'link'  => 'themes',
			'views' => [
				[
					'pattern' => 'themes',
					'action'  => fn () => View::fiber(),
				],
			],
			'dialogs' => [
				'themes.delete' => [
					'pattern' => 'themes/(:any)/delete',
					'load' => fn (string $slug) => [
						'component' => 'k-remove-dialog',
						'props'     => [
							'text' => t('field.structure.delete.confirm')
						]
					],
					'submit' => function (string $slug) {
						$themes = Themes::instance();
						$themes->remove($slug);
						$themes->save();
						return true;
					}
				],
			],
			'drawers' => [
				'themes.create' => [
					'pattern' => 'themes/create',
					'load'    => fn () => (new ThemeCreateDrawer())->load(),
					'submit'  => fn () => (new ThemeCreateDrawer())->submit(),
				],

				'themes.edit' => [
					'pattern' => 'themes/(:any)/edit',
					'load'    => fn (string $slug) => (new ThemeEditDrawer($slug))->load(),
					'submit'  => fn (string $slug) => (new ThemeEditDrawer($slug))->submit(),
				],
			],
		];
	}
];
