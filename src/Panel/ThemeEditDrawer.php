<?php

namespace FabianMichael\ThemeKit\Panel;

use FabianMichael\ThemeKit\Theme;
use Kirby\Toolkit\I18n;

class ThemeEditDrawer extends ThemeCreateDrawer
{
	public function __construct(
		protected string $slug
	) {
	}

	public function load(): array
	{
		$fields = $this->fields();

		// set autofocus if specific column cell was passed
		if ($column = $this->kirby()->request()->get('column')) {
			foreach ($fields as $name => $field) {
				$fields[$name]['autofocus'] = $name === $column;
			}
		}

		return [
			'component' => 'k-form-drawer',
			'props' => [
				'fields'  => $fields,
				'icon'    => 'palette',
				'title'   => I18n::translate('edit'),
				'value'   => $this->value(),
				'options' => [
					[
						'icon'   => 'trash',
						'title'  => I18n::translate('remove'),
						'dialog' => "themes/{$this->slug}/delete",
					]
				]
			]
		];
	}

	protected function theme(): Theme
	{
		return $this->themes()->get($this->slug);
	}

	public function submit(): bool
	{
		$themes = $this->themes();
		$data = $this->data();
		$themes = $themes->update($this->slug, [
			...$data,
			'source' => 'site',
		]);
		$themes = $themes->save();
		return true;
	}

	protected function value(): array
	{
		return $this->theme()->toArray();
	}
}
