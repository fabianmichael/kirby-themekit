<?php

namespace FabianMichael\Themes;

class Styles
{
	protected static array $content = [];

	public static function push(Theme $theme): void
	{
		static::$content[$theme->slug()] = $theme;
	}

	public static function render(): string
	{
		return implode(PHP_EOL, array_map(fn($theme) => $theme->toCSSRule(), static::$content));
	}
}
