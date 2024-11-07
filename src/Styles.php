<?php

namespace FabianMichael\ThemeKit;

class Styles
{
	protected static array $stack = [];

	public static function push(Theme $theme): void
	{
		static::$stack[$theme->slug()] = $theme;
	}

	public static function stack(): array
	{
		return static::$stack;
	}
}
