<?php

namespace FabianMichael\Themes;

use Closure;
use Kirby\Toolkit\Collection;

class Themes extends Collection
{
	protected static Themes $instance;

	protected function __construct()
	{
		// themes from config
		foreach (option('fabianmichael.themes.themes', []) as $slug => $theme) {
			$this->set($slug, new Theme(array_merge($theme, [
				'slug' => $slug,
			])));
		}

		// load global themes from $site
		$code = kirby()->defaultLanguage()?->code();
		foreach (site()->content($code)->get('themes')->toBlocks() as $theme) {
			$slug = $theme->title()->slug() . '-' . explode('-', $theme->id())[0];
			$theme = new Theme([
				...$theme->content()->toArray(),
				'slug' => $slug
			]);
			$this->set($slug, $theme);
		}
	}

	public static function instance(): self
	{
		return static::$instance ?? (static::$instance = new static());
	}

	public function default(): ?Theme
	{
		return $this->findBy('default', true) ?? $this->first();
	}

	public function toArray(Closure $map = null): array
	{
		$data = array_map(fn ($item) => $item->toArray(), $this->data);

		if ($map !== null) {
			$data = array_map($map, $data);
		}

		return $data;
	}
}
