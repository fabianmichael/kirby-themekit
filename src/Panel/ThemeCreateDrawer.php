<?php

namespace FabianMichael\ThemeKit\Panel;

use Exception;
use FabianMichael\ThemeKit\Theme;
use FabianMichael\ThemeKit\Themes;
use Kirby\Cms\App;
use Kirby\Toolkit\A;
use Kirby\Toolkit\I18n;

class ThemeCreateDrawer
{
	protected function data(): array
	{
		return $this->kirby()->request()->get([
			'title',
			'slug',
			...array_keys(Theme::fields()),
		], '');
	}

	protected function fields(): array
	{
		$fields = [
			'title' => [
				'type'     => 'text',
				'label'    => 'Title',
				'icon'     => 'brush',
				'counter'  => false,
				'required' => true,
			],
			'slug' => [
				'type' => 'slug',
				'label' => 'Slug',
				'font' => 'monospace',
				'sync' => 'title',
				'required' => 'true',
				'disabled' => $this instanceof ThemeEditDrawer,
			],
			...Theme::fields(),
		];

		return $fields;
	}

	protected function kirby(): App
	{
		return App::instance();
	}

	public function load(): array
	{
		return [
			'component' => 'k-form-drawer',
			'props' => [
				'title'  => $this->title(),
				'icon'   => 'add',
				'value'  => $this->value(),
				'fields' => $this->fields(),
			],
		];
	}

	protected function themes(): Themes
	{
		return Themes::instance();
	}

	public function submit(): bool|array
	{
		$data = $this->data();

		if (A::get($data, 'slug') === 'custom') {
			throw new Exception('Slug must not be "custom".');
		}

		$themes = $this->themes();
		$themes->create($data);
		$themes->save();
		return true;
	}

	protected function title(): string
	{
		return I18n::translate('add');
	}

	protected function value(): array
	{
		return [
			'background' => '#ffffff',
		];
	}
}
