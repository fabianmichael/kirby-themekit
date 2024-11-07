<?php

namespace FabianMichael\ThemeKit\Panel;

class View
{
	public static function fiber(): array
	{
		return [
			'component'  => 'k-themes-themes-view',
			'title'      => 'Themes',
			'breadcrumb' => [],
			'props' => [
				'data' => site()->themes()->toData(function ($item) {
					if ($item['default']) {
						$item['title'] .= ' (default)';
					}
					return $item;
				}),
			],
		];
	}
}
