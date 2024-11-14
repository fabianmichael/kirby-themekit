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
