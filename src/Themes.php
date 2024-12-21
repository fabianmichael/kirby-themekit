<?php

namespace FabianMichael\ThemeKit;

use Closure;
use Kirby\Exception\DuplicateException;
use Kirby\Toolkit\Collection;

class Themes extends Collection
{
	protected static Themes $instance;

	protected function __construct()
	{
		// themes from config
		foreach (option('fabianmichael.themekit.themes', []) as $slug => $theme) {
			$theme = new Theme(array_merge($theme, [
				'slug' => $slug,
				'source' => 'config',
			]));

			$this->set($theme->slug(), $theme);
		}

		// load global themes from $site
		$code = kirby()->defaultLanguage()?->code();
		foreach (site()->content($code)->get('themes')->yaml() as $theme) {
			$theme = new Theme([
				...$theme,
				'source' => 'site',
			]);
			$this->set($theme->slug(), $theme);
		}
	}

	public function create(array $data): self
	{
		$theme = new Theme([
			...$data,
			'source' => 'site',
		]);

		if ($this->has($theme->slug()) === true) {
			throw new DuplicateException('Theme with slug already exists' . print_r($data, true));
		}

		$this->prepend($theme->slug(), $theme);
		return $this;
	}

	public static function instance(): self
	{
		return static::$instance ?? (static::$instance = new static());
	}

	public function default(): ?Theme
	{
		return $this->findBy('default', true) ?? $this->first();
	}

	public function toArray(?Closure $map = null): array
	{
		$data = array_map(fn ($item) => $item->toArray(), $this->data);

		if ($map !== null) {
			$data = array_map($map, $data);
		}

		return $data;
	}

	public function save(): void
	{
		$data = $this
			->filterBy('source', 'site')
			->toArray(fn ($theme) => array_filter(
				$theme,
				fn($key) => in_array($key, [
					'slug',
					'title',
					...array_keys(Theme::fields()),
				]),
				ARRAY_FILTER_USE_KEY
			));

		site()->update(
			['themes' => $data],
			kirby()->defaultLanguage()?->code()
		);
	}

	/**
	 * Converts the object into an array
	 *
	 * @param \Closure|null $map
	 * @return array
	 */
	public function toData(?Closure $map = null): array
	{
		$data = array_values(array_map(fn ($item) => $item->toData(), $this->data));

		if ($map !== null) {
			return array_map($map, $data);
		}

		return $data;
	}

	/**
	 * Updates existing theme
	 *
	 * @param string $slug Slug of exisiting theme
	 */
	public function update(string $slug, array $data): self
	{
		$theme = new Theme($data);
		$this->set($slug, $theme);
		return $this;
	}
}
