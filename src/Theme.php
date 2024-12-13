<?php

// phpcs:disable PSR1.Methods.CamelCapsMethodName

namespace FabianMichael\ThemeKit;

use FabianMichael\ThemeKit\Color\Color;
use Kirby\Cms\Block;
use Kirby\Cms\Layout;
use Kirby\Cms\Page;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Obj;

class Theme extends Obj
{
	protected const CUSTOM_THEME_PREFIX = 'theme_';

	public function __construct(array $data)
	{
		$data = array_merge([
			'source' => 'config',
		], $data);

		parent::__construct($data);
	}

	public function colorsExport(): array
	{
		return [
			...array_keys(static::fields()),
			'background_contrast',
			'foreground_contrast',
		];
	}

	protected static function constrastColors(): array
	{
		return [
			option('fabianmichael.themekit.contrastLight'),
			option('fabianmichael.themekit.contrastDark'),
		];
	}

	public function readability(): array
	{
		$report = $this->foreground()
			->toReadabilityReport([$this->background()->toString('hex')]);

		return A::get($report, 'combinations.0.accessibility');
	}

	public function foreground(): Color
	{
		if (! empty($this->foreground)) {
			return new Color($this->foreground);
		}

		$color = $this->background()->toMostReadable(static::constrastColors());
		$color = A::first($color);
		$foreground = A::get($color, 'color');

		return $foreground;
	}

	public function background(): Color
	{
		return new Color($this->background);
	}

	public function background_contrast(): Color
	{
		$color = $this->background()->toMostReadable(static::constrastColors());
		$color = A::first($color);
		return A::get($color, 'color');
	}

	public function foreground_contrast(): Color
	{
		$color = $this->foreground()->toMostReadable(static::constrastColors());
		$color = A::first($color);
		return A::get($color, 'color');
	}

	public function isDefault(): bool
	{
		return (bool) $this->default();
	}

	public function isCustom(): bool
	{
		return (bool) $this->custom;
	}

	public function isDark(): bool
	{
		$color = $this->background()->toMostReadable(static::constrastColors());
		$color = A::first($color);
		$color = A::get($color, 'color');

		return $color->toString('hex') === (new Color(option('fabianmichael.themekit.contrastLight')))->toString('hex');
	}

	public function isLight(): bool
	{
		return !$this->isDark();
	}

	public function scheme(): string
	{
		return r($this->isLight(), 'light', 'dark');
	}

	public function toArray(): array
	{
		$data = [
			'slug' => $this->slug(),
			'title' => $this->title(),
			'dark' => $this->isDark(),
			'default' => $this->isDefault(),
			'editable' => $this->isEditable(),
			'source' => $this->source(),
			'readability' => $this->readability(),
		];

		foreach (array_keys(static::fields()) as $field) {
			$data[$field] = $this->get($field);
		}

		return $data;
	}

	public function __call(string $property, array $arguments)
	{
		$field = A::get(static::fields(), $property);

		if ($field && A::get($field, 'type') === 'color') {
			if (!empty($this->$property)) {
				return new Color($this->$property);
			}

			if ($fallback = A::get($field, 'fallback')) {
				if (is_callable($fallback)) {
					return $fallback($this);
				} elseif (is_string($fallback)) {
					return $this->$fallback();
				}
			}

			return null;
		}

		return parent::__call($property, $arguments);
	}

	public function toData(): array
	{
		$data = $this->toArray();

		foreach ($this->colorsExport() as $color) {
			$data[$color] = $this->$color()?->toString('hex');
		}

		return $data;
	}

	public function isEditable(): bool
	{
		return $this->source() === 'site';
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

			$slug = 'custom-' . substr(sha1(serialize($props)), 0, 8);

			$props = [
				...$props,
				'slug' => $slug,
				'source' => null,
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

		return null;
	}

	public static function fields(?string $prefix = null): array
	{
		$fields = [
			"{$prefix}background" => [
				'type' => 'color',
				'label' => 'Background color',
				'required' => true,
				'translate' => false,
				'width' => '1/2',
			],
			"{$prefix}foreground" => [
				'type' => 'color',
				'label' => 'Foreground color',
				'translate' => false,
				'width' => '1/2',
			],
		];

		foreach (option('fabianmichael.themekit.fields') as $name => $field) {
			$fields["{$prefix}{$name}"] = $field;
		}

		return $fields;
	}

	public function use(): static
	{
		Styles::push($this);
		return $this;
	}
}
