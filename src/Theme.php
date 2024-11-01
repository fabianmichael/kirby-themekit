<?php

namespace FabianMichael\Themes;

use FabianMichael\Themes\Color\Color;
use Kirby\Cms\Block;
use Kirby\Cms\Layout;
use Kirby\Cms\Page;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;

class Theme extends Obj
{
	protected const CONTRAST_COLORS = [
		'#fff',
		'#000',
	];

	protected const CUSTOM_THEME_PREFIX = 'theme_';

	protected const COLORS_EXPORT = [
		'background',
		'backgroundContrast',
		'foreground',
		'foregroundContrast',
	];

	public function __construct(array $data)
	{
		parent::__construct($data);
	}

	public function foreground(): Color
	{
		if (! empty($this->foreground)) {
			return new Color($this->foreground);
		}

		$color = $this->background()->toMostReadable(static::CONTRAST_COLORS);
		$color = A::first($color);
		$foreground = A::get($color, 'color');

		return $foreground;
	}

	public function background(): Color
	{
		return new Color($this->background);
	}

	public function backgroundContrast(): Color
	{
		$color = $this->background()->toMostReadable(static::CONTRAST_COLORS);
		$color = A::first($color);
		$color = A::get($color, 'color');
		return $color;
	}

	public function foregroundContrast(): Color
	{
		$color = $this->foreground()->toMostReadable(static::CONTRAST_COLORS);
		$color = A::first($color);
		$color = A::get($color, 'color');
		return $color;
	}

	public function isDefault(): bool
	{
		return (bool) $this->default();
	}

	public function isCustom(): bool
	{
		return (bool) $this->custom();
	}

	public function isDark(): bool
	{
		$color = $this->background()->toMostReadable(static::CONTRAST_COLORS);
		$color = A::first($color);
		$color = A::get($color, 'color');

		return $color->toString('hex') === '#ffffff';
	}

	public function isLight(): bool
	{
		return !$this->isDark();
	}

	public function toArray(): array
	{
		$data = [
			'slug' => $this->slug(),
			'title' => $this->title(),
			'dark' => $this->isDark(),
			'default' => $this->isDefault(),
		];

		foreach (static::COLORS_EXPORT as $color) {
			$kebap = Str::kebab($color);
			$data[$kebap] = $this->$color()->toString('hex');
		}

		return $data;
	}

	public function toCSSRule()
	{
		$css = ["[data-theme=\"{$this->slug()}\"]", '{'];
		if (option('debug') && $this->isDefault()) {
			$css[] = '/* default theme */';
		}
		foreach (static::COLORS_EXPORT as $color) {
			$kebab = Str::kebab($color);
			extract($this->$color()->toHSL());
			$css[] = "--theme--{$kebab}: {$h} {$s}% {$l}%;";
		}
		$css[] = '}';

		return implode(' ', $css);
	}

	public function __toString(): string
	{
		return $this->slug();
	}

	public static function from(Page|Layout|Block $item): ?Theme
	{
		$themes = Themes::instance();
		$content = $item instanceof Layout
			? $item->attrs()
			: $item->content(kirby()->defaultLanguage()?->code());
		$themeField = $content->get('theme');
		$isCustom = $content->get(static::CUSTOM_THEME_PREFIX .'custom')->isTrue();

		if ($isCustom) {
			$props = [];

			foreach (['slug', 'title', ...array_keys(static::fields())] as $field) {
				$props[$field] = $content->get(static::CUSTOM_THEME_PREFIX . $field)->value();
			}

			if ($item instanceof Page) {
				$slug = 'page-' . Str::slug($item->uuid()->toString());
			} elseif ($item instanceof Layout) {
				$slug = 'layout-' . Str::slug(explode('-', $item->id())[0]);
			} else {
				$slug = 'custom-';
			}

			$props = [
				...$props,
				'slug' => $slug,
			];

			$theme = new static($props);

			return $theme;
		}

		if ($themeField->isNotEmpty()) {
			foreach ($themes as $theme) {
				if ($theme->slug() === $themeField->toString()) {
					return $theme;
				}
			}
		}

		return $themes->default();
	}
	public static function fields(?string $prefix = null): array
	{
		return [
			"{$prefix}background" => [
				'type' => 'color',
				'label' => 'Background color',
				'required' => true,
				'translate' => false,
			],
			"{$prefix}foreground" => [
				'type' => 'color',
				'label' => 'Foreground color',
				'translate' => false,
			],
		];
	}
}
