# ThemeKit for Kirby CMS

ThemeKit lets you define **color themes** (background and foreground color, plus any extra colors you need), pick them in the Panel and apply them to pages, blocks, layouts or arbitrary parts of your templates. Each theme is turned into a set of CSS custom properties, so your stylesheet only has to deal with variables like `var(--theme--background)`.

- Define themes in `config.php`, and optionally let editors create their own in the Panel
- `theme-selector` field with swatch previews, and a ready-made `theme-group` field for blueprints
- Custom, one-off themes per page, block or layout (background and foreground color picker)
- Automatic fallback for the foreground color, chosen by WCAG contrast (`#fff` or `#000` by default)
- Light/dark detection (`isDark()`, `scheme()`), so your CSS can adapt components to the theme
- Readability report (WCAG rating) for each theme in the Panel
- Only the themes actually used on a page end up in the page’s CSS

**Requirements:** Kirby 5, PHP 8.4+

## Installation

### Composer

```bash
composer require fabianmichael/kirby-themekit
```

## Quick start

### 1. Define your themes

By default, ThemeKit ships with a `light` (`#fff`) and a `dark` (`#000`) theme. Replace or extend them in `site/config/config.php`:

```php
<?php

return [
    // Disable the built-in themes by setting them to `false`
    'fabianmichael.themekit.themes' => [
        'light' => false, // disable default theme "light"
        'dark' => false, // disable default theme "dark"

        'white' => [
            'title'      => 'White',
            'background' => '#ffffff',
            'foreground' => '#192432',
            'default'    => true, // used when nothing is selected
        ],
        'ferrocyan' => [
            'title'      => 'Ferrocyan',
            'background' => '#273A53',
            'foreground' => '#ffffff',
        ],
        'victoriagelb' => [
            'title'      => 'Victoriagelb',
            'background' => '#fbbb21',
            // no foreground: picked automatically by contrast
        ],
    ],
];
```

The array key is the theme’s **slug**. Each theme accepts:

| Key          | Description                                                                                       |
| ------------ | ------------------------------------------------------------------------------------------------- |
| `title`      | Label shown in the Panel                                                                          |
| `background` | Background color (hex, `rgb()` or `hsl()`), required                                              |
| `foreground` | Foreground color. Optional; if omitted, the most readable of the `contrastColors` is used         |
| `default`    | Set to `true` for the default theme. If none is marked, the first theme is the default            |
| *any field*  | Values for additional color fields you registered via the `fields` option (see below)             |

### 2. Add the field to your blueprints

Use the `fields/theme-group` field group anywhere in a blueprint (pages, site, block and layout settings, …):

```yaml
# site/blueprints/pages/event.yml
sections:
  settings:
    type: fields
    fields:
      theme: fields/theme-group
```

The group renders a theme selector and, if `customThemes` is enabled (default), a *“Custom theme”* toggle that reveals color pickers for a one-off theme. The field is also available as `themekit/fields/theme-group`.

If you need a different field name, or more than one theme on the same object (e.g. a header theme and a footer theme), use the `theme_fields()` helper in a PHP blueprint:

```php
<?php // site/blueprints/sections/site-settings.php

return [
    'type' => 'fields',
    'fields' => [
        ...theme_fields('site_header_theme'),
        ...theme_fields('site_footer_theme'),
    ],
];
```

You can also use the bare `theme-selector` field type, if you don’t want custom themes:

```yaml
fields:
  theme:
    type: theme-selector
    label: Color theme
    none: false  # hide the “no theme” option (default: true)
```

### 3. Output the theme CSS

Place the `themes` snippet in your `<head>`. It renders a `<style>` element with the rules for every theme that has been *used* on the current page:

```php
<head>
  <!-- … -->
  <?php snippet('themes') ?>
</head>
```

A theme is marked as used by calling `->use()` on it (see next step). The snippet also registers `$page->theme()` itself, so the page’s own theme is always included.

> **Important:** The snippet only outputs themes that have been registered *by the time it runs*. If your `<head>` is rendered before the body markup (plain PHP templates), themes used further down the page are missed. Build the body first (e.g. with [snippet slots](https://getkirby.com/docs/reference/templates/helpers/slots) in your layout snippet, so the content is rendered before the layout), or register the themes you need up front with `->use()`.

> **Tip:** `snippet('themekit/themes')` is an alias, in case your project defines its own `themes` snippet.

The generated CSS looks like this (illustrative output):

```css
[data-theme="victoriagelb"] {
    --theme--background: #fbbb21;
    --theme--foreground: #000000;
    --theme--background-contrast: #000000;
    --theme--foreground-contrast: #ffffff;
    --theme--color-scheme: light;
}
```

### 4. Apply themes in your templates

Add the theme to an element via `data-theme`. `attrValue()` gives you the right attribute value, and `use()` registers the theme so its CSS is output:

```php
<?php /** @var \Kirby\Cms\Page $page */ ?>
<html data-theme="<?= $page->theme()->use()->attrValue() ?>">
```

```php
<!-- a block or layout with its own theme -->
<section data-theme="<?= $block->theme()?->use()->attrValue() ?>">
  …
</section>
```

Then use the properties in your CSS:

```css
body {
  background: var(--theme--background);
  color: var(--theme--foreground);
  color-scheme: var(--theme--color-scheme);
}

.button {
  background: var(--theme--foreground);
  color: var(--theme--background);
}
```

Because the properties are set on the element carrying `data-theme`, they are inherited by all children and can be overridden further down the tree by nesting a different theme.

## Reading themes in PHP

### Methods on content objects

| Method                               | Returns                                                                                               |
| ------------------------------------ | ----------------------------------------------------------------------------------------------------- |
| `$page->theme()`                     | The page’s theme, falling back to the default theme. Never `null`                                     |
| `$block->theme()`                    | The block’s theme, or `null` if none is set                                                           |
| `$layout->theme()`                   | The layout’s theme (read from its `attrs`), or `null`                                                 |
| `$field->toTheme()`                  | Theme from a field of that name, e.g. `$site->site_header_theme()->toTheme()`. `null` if none is set |
| `$site->themes()`                    | A `Themes` collection with all available themes                                                       |

`toTheme()` uses the field’s name as the prefix, so it works together with `theme_fields('my_field')`:

```php
$headerTheme = $page->site_header_theme()->toTheme()?->use()
    ?? $site->site_header_theme()->toTheme()?->use()
    ?? $page->theme();
```

### Helpers

```php
themes();             // Themes collection (same as $site->themes())
theme();              // default theme
theme('victoriagelb') // theme by slug, or null
```

```php
<aside data-theme="<?= theme('dark')->use()->attrValue() ?>">…</aside>
```

### The `Theme` object

```php
$theme->slug();               // 'victoriagelb'
$theme->title();              // 'Victoriagelb'
$theme->background();         // Color object, casts to hex string
$theme->foreground();         // Color object (explicit, or the most readable contrast color)
$theme->background_contrast();// most readable contrast color on the background
$theme->foreground_contrast();// most readable contrast color on the foreground

$theme->isDark();             // true if white text is more readable on the background
$theme->isLight();
$theme->scheme();             // 'light' or 'dark'
$theme->lightDark();          // 'light dark' or 'dark light' (for the `color-scheme` property)

$theme->isDefault();
$theme->isCustom();           // true for custom themes defined on a page/block
$theme->isEditable();         // whether it can be edited in the Panel
$theme->readability();        // WCAG rating of foreground vs. background

$theme->use();                // registers the theme for the `themes` snippet; returns $theme
$theme->attrValue();          // value for the `data-theme` attribute
```

Colors are `Color` objects, which cast to a hex string and can be used in templates directly (e.g. `<meta name="theme-color" content="<?= $theme->background() ?>">`).

Extra fields (see below) are available as methods with the same name, e.g. `$theme->heading()`.

## Custom themes in the Panel

With `customThemes` enabled (default), a **Themes** area appears in the Panel where editors can create, edit and delete their own global themes. Those are stored in the `themes` field of the site’s content file and sit beside the themes from your config.

The area is added to the Panel menu automatically. If you have customized `panel.menu`, add `themes` yourself:

```php
'panel' => [
    'menu' => [
        'site',
        'themes',
        '-',
        'users',
        'system',
    ],
],
```

Themes defined in `config.php` can’t be edited in the Panel by default. Set `editableDefaultThemes` to `true` to allow that.

If you don’t want editors to manage or create themes at all, disable the feature. This also hides the *“Custom theme”* toggle in `theme-group` and the Panel area:

```php
'fabianmichael.themekit.customThemes' => false,
```

## Options

All options live under the `fabianmichael.themekit` prefix.

| Option                  | Default                              | Description                                                                                   |
| ----------------------- | ------------------------------------ | --------------------------------------------------------------------------------------------- |
| `themes`                | `light` and `dark`                   | Theme definitions, see above. Set a built-in slug to `false` to remove it                    |
| `customThemes`          | `true`                               | Enable the Panel area for global themes and the *Custom theme* toggle in `theme-group`        |
| `editableDefaultThemes` | `false`                              | Allow editing themes defined in config in the Panel                                           |
| `contrastColors`        | `['#fff', '#000']`                   | Candidates for the automatic foreground color and for `*_contrast` colors                     |
| `fields`                | `[]`                                 | Additional color fields for every theme (array or callback returning an array)                |
| `previewColors`         | first two extra color fields         | Names of the extra color fields shown as swatches in the theme selector (array or callback)   |
| `css.selector`          | `[data-theme="{slug}"]`              | CSS selector for each theme rule. Placeholders: `{slug}`, `{scheme}`                          |
| `css.attrValue`         | `{slug}`                             | Value returned by `$theme->attrValue()`. Placeholders: `{slug}`, `{scheme}`                   |
| `css.propertyName`      | `--theme--{name}`                    | Name template for the generated custom properties. Placeholder: `{name}`                      |

### Extra theme colors

Backgrounds and foregrounds are often not enough. Register additional fields for all themes with the `fields` option. They show up in the custom theme editor, in the Panel area, and as CSS properties:

```php
'fabianmichael.themekit.fields' => [
    'heading' => [
        'type'     => 'color',
        'label'    => 'Heading color',
        'width'    => '1/2',
        // Optional fallback, used when a theme doesn’t define this color.
        // A method name on the theme, or a callable receiving the theme.
        'fallback' => 'foreground',
    ],
    'accent' => [
        'type'  => 'color',
        'label' => 'Accent color',
        'width' => '1/2',
    ],
],
```

```php
'fabianmichael.themekit.themes' => [
    'white' => [
        'title'      => 'White',
        'background' => '#ffffff',
        'foreground' => '#192432',
        'heading'    => '#273A53',
        'accent'     => '#fbbb21',
        'default'    => true,
    ],
],
```

This produces `--theme--heading` and `--theme--accent` alongside the standard properties. Extra fields are also used for the swatches in the theme selector (see `previewColors`).

### Targeting light and dark themes

The more themes you have, the more it makes sense to style by *scheme* (light or dark background) instead of by individual theme. Change the selector and attribute value, so each element carries both its slug and its scheme:

```php
'fabianmichael.themekit.css.selector'  => '[data-theme~="{slug}"]',
'fabianmichael.themekit.css.attrValue' => '{slug} scheme:{scheme}',
```

An element then renders as `data-theme="ferrocyan scheme:dark"`, and your CSS can target the scheme, while the theme colors still come from the slug rule:

```css
[data-theme~='scheme:light'] {
  --button--bg: #fff;
  --button--text: #273a53;
}

[data-theme~='scheme:dark'] {
  --button--bg: #273a53;
  --button--text: #fff;
}
```

This is particularly useful for nested themes, e.g. a dark box inside a light section.

### Customizing the CSS output

The plugin provides three snippets, which you can override in your own `site/snippets` folder:

| Snippet                  | Purpose                                              |
| ------------------------ | ---------------------------------------------------- |
| `themes`                 | `<style>` element containing the rules of used themes |
| `themes/css-rule`        | One rule (selector and declarations) per theme       |
| `themes/css-properties`  | The custom properties declared for a theme           |

To add your own properties, override `themes/css-properties` and include the original through its `themekit/` alias:

```php
<?php // site/snippets/themes/css-properties.php

/** @var \FabianMichael\ThemeKit\Theme $theme */

snippet('themekit/themes/css-properties', [
  'theme' => $theme,
]);

echo '    --theme--is-dark: ' . ($theme->isDark() ? 1 : 0) . ';' . PHP_EOL;
```

## A full example

A typical setup, modeled after the hannover-bowling.de website:

**`site/config/config.php`**

```php
'fabianmichael.themekit.customThemes'  => false,
'fabianmichael.themekit.css.selector'  => '[data-theme~="{slug}"]',
'fabianmichael.themekit.css.attrValue' => '{slug} scheme:{scheme}',
'fabianmichael.themekit.themes' => [
    'light' => false,
    'dark'  => false,
    'white' => [
        'title'      => 'White',
        'background' => '#ffffff',
        'foreground' => '#192432',
        'default'    => true,
    ],
    'victoriagelb' => [
        'title'      => 'Victoriagelb',
        'background' => '#fbbb21',
        'foreground' => '#192432',
    ],
    'ferrocyan' => [
        'title'      => 'Ferrocyan',
        'background' => '#273A53',
        'foreground' => '#ffffff',
    ],
],
```

**`site/blueprints/pages/event.yml`**

```yaml
sidebar:
  sections:
    settings:
      type: fields
      fields:
        theme: fields/theme-group
```

**Layout snippet**

```php
<!doctype html>
<html data-theme="<?= $page->theme()->use()->attrValue() ?>">
<head>
  <!-- … -->
  <?php snippet('themes') ?>
  <meta name="theme-color" content="<?= $page->theme()->background() ?>">
</head>
<body>
  <?= $slots->default() ?>
</body>
</html>
```

**A card that uses its own theme** (`site/snippets/components/flipcard.php`)

```php
<div class="flipcard__backface" data-theme="<?= $event->theme()->use()->attrValue() ?>">
  …
</div>
```

**A fixed theme, independent of editor choice**

```php
<section data-theme="<?= theme('victoriagelb')->use()->attrValue() ?>">
  …
</section>
```

**Using the theme color in a Panel blueprint** (e.g. as the background of a card image)

```yaml
image:
  cover: true
  query: page.card_character.toFile
  back: "{{ page.theme.background }}"
```

**`src/css/components/box.css`**

```css
.box {
  background: var(--theme--background);
  color: var(--theme--foreground);
}

[data-theme~='scheme:light'] .box[data-theme~='scheme:dark']::after {
  border-color: var(--theme--background);
}
```

## Development

The Panel components are built with [kirbyup](https://github.com/johannschopplich/kirbyup):

```bash
npm install
npm run dev    # watch
npm run build  # production build (outputs index.js and index.css)
```

Code style is checked with PHP_CodeSniffer: `npm run php:check` and `npm run php:fix`.

## License

[MIT](LICENSE) © Fabian Michael
