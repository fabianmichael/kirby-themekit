<?php

namespace FabianMichael\ThemeKit\Color;

use FabianMichael\ThemeKit\Color\Readability;
use Stringable;

// based on: https://github.com/hananils/kirby-colors/tree/main
class Color implements Stringable
{
	use Converter;

	private $original = null;
	private $space = null;
	private $r = null;
	private $g = null;
	private $b = null;
	private $h = null;
	private $s = null;
	private $l = null;
	private $a = 100;

	public function __construct($color)
	{
		if (is_a($color, static::class)) {
			$values = $color->toValues();
			$this->setValues($values);
		} elseif (static::isHex($color)) {
			$this->original = $color;
			$this->space = 'hex';
			$this->parseHex($color);
		} elseif (static::isRgb($color)) {
			$this->original = $color;
			$this->space = 'rgb';
			$this->parseRgb($color);
		} elseif (static::isHsl($color)) {
			$this->original = $color;
			$this->space = 'hsl';
			$this->parseHsl($color);
		} else {
			$this->setDefault();
		}
	}

	/**
	 * Checks
	 */

	public static function isHex(?string $color = null): bool
	{
		return !empty($color) && strpos($color, '#') === 0;
	}

	public static function isRgb(?string $color = null): bool
	{
		return !empty($color) && strpos($color, 'rgb') === 0;
	}

	public static function isHsl(?string $color = null): bool
	{
		return !empty($color) && strpos($color, 'hsl') === 0;
	}

	public function hasAlpha(): bool
	{
		return $this->a !== 100;
	}

	/**
	 * Parsers
	 */

	public function parseHex(string $color): void
	{
		$color = trim($color, '#; ');

		if (strlen($color) < 6) {
			$values = str_split($color, 1);
			foreach ($values as $key => $value) {
				$values[$key] = str_repeat($value, 2);
			}
		} else {
			$values = str_split($color, 2);
		}

		$this->setHex($values);
	}

	public function parseRgb(string $color): void
	{
		preg_match('/\((.*)\)/', $color, $matches);
		$values = preg_split('/[\s,\/]+/', $matches[1]);

		$this->setRgb($values);
	}

	public function parseHsl(string $color): void
	{
		preg_match('/\((.*)\)/', $color, $matches);
		$values = preg_split('/[\s,\/]+/', $matches[1]);

		$this->setHsl($values);
	}

	/**
	 * Setters
	 */

	public function setDefault(): void
	{
		$this->setValues([
			'original' => null,
			'space' => 'hex',
			'r' => 255,
			'g' => 255,
			'b' => 255,
			'h' => 0,
			's' => 0,
			'l' => 100,
			'a' => 100
		]);
	}

	public function setValues(array $values): static
	{
		$this->original = $values['original'];
		$this->space = $values['space'];
		$this->r = $values['r'];
		$this->g = $values['g'];
		$this->b = $values['b'];
		$this->h = $values['h'];
		$this->s = $values['s'];
		$this->l = $values['l'];
		$this->a = $values['a'];
		return $this;
	}

	/* Set by value */

	public function setSpace(string $format): static
	{
		$this->space = $format;
		return $this;
	}

	public function setRed($value): static
	{
		$this->r = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setGreen($value): static
	{
		$this->g = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setBlue($value): static
	{
		$this->b = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setHue($value): static
	{
		$this->h = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setSaturation($value): static
	{
		$this->s = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setLightness($value): static
	{
		$this->l = $this->convertValueToDecimal($value);
		return $this;
	}

	public function setAlpha($value): static
	{
		$this->a = $this->convertValueToDecimal($value);
		return $this;
	}

	/* Set by space */

	public function setHex($values): static
	{
		if (!$values) {
			return $this->setDefault();
		}

		$this->setRed(hexdec($values[0]));
		$this->setGreen(hexdec($values[1]));
		$this->setBlue(hexdec($values[2]));

		if (count($values) === 4) {
			$this->setAlpha($this->rebaseHexForDecimal($values[3]));
		}

		$this->convertRgbToHsl();
		return $this;
	}

	public function setRgb($values): static
	{
		if (!$values) {
			return $this->setDefault();
		}

		$this->setRed($values[0]);
		$this->setGreen($values[1]);
		$this->setBlue($values[2]);

		if (count($values) === 4) {
			$this->setAlpha($values[3]);
		}

		$this->convertRgbToHsl();
		return $this;
	}

	public function setHsl($values): static
	{
		if (!$values) {
			return $this->setDefault();
		}

		$this->setHue($values[0]);
		$this->setSaturation($values[1]);
		$this->setLightness($values[2]);

		if (count($values) === 4) {
			$this->setAlpha($values[3]);
		}

		$this->convertHslToRgb();
		return $this;
	}

	/**
	 * Getters
	 */

	public function getAlpha()
	{
		return $this->a;
	}

	/**
	 * Readability
	 */

	public function toReadabilityReport($combinations = ['#fff', '#000'])
	{
		$readability = new Readability($this, $combinations);
		return $readability->toReport();
	}

	public function toMostReadable($combinations = ['#fff', '#000'])
	{
		$readability = new Readability($this, $combinations);
		return $readability->toMostReadable();
	}

	/**
	 * Results
	 */

	public function toOriginal()
	{
		return $this->original;
	}

	public function toSpace()
	{
		return $this->space;
	}

	public function __toString(): string
	{
		return $this->toString();
	}

	public function toValues($precision = 0)
	{
		return [
			'original' => $this->original,
			'space' => $this->space,
			'r' => round($this->r, $precision),
			'g' => round($this->g, $precision),
			'b' => round($this->b, $precision),
			'h' => round($this->h, $precision),
			's' => round($this->s, $precision),
			'l' => round($this->l, $precision),
			'a' => round($this->a, $precision)
		];
	}

	public function toHex()
	{
		return [
			'r' => $this->convertDecimalToHex($this->r),
			'g' => $this->convertDecimalToHex($this->g),
			'b' => $this->convertDecimalToHex($this->b),
			'a' => $this->convertDecimalToHex(
				$this->rebaseDecimalForHex($this->a)
			)
		];
	}

	public function toRgb($precision = 0)
	{
		return [
			'r' => round($this->r, $precision),
			'g' => round($this->g, $precision),
			'b' => round($this->b, $precision),
			'a' => $this->convertToFloat($this->a, $precision)
		];
	}

	public function toHsl($precision = 0)
	{
		return [
			'h' => round($this->h, $precision),
			's' => round($this->s, $precision),
			'l' => round($this->l, $precision),
			'a' => $this->convertToFloat($this->a, $precision)
		];
	}

	public function toString($format = null)
	{
		if (!$format) {
			$format = $this->toSpace();
		}

		if (strpos($format, 'hsl') === 0) {
			$hsl = $this->toHsl();

			if ($this->a < 100) {
				return "hsla({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%, {$hsl['a']})";
			} else {
				return "hsla({$hsl['h']}, {$hsl['s']}%, {$hsl['l']}%)";
			}
		} elseif (strpos($format, 'rgb') === 0) {
			$rgb = $this->toRgb();

			if ($this->a < 100) {
				return "rgba({$rgb['r']}, {$rgb['g']}, {$rgb['b']}, {$rgb['a']})";
			} else {
				return "rgb({$rgb['r']}, {$rgb['g']}, {$rgb['b']})";
			}
		} else {
			$hex = $this->toHex();

			if ($this->a < 100) {
				return "#{$hex['r']} {$hex['g']}{$hex['b']}{$hex['a']}";
			} else {
				return "#{$hex['r']}{$hex['g']}{$hex['b']}";
			}
		}
	}
}
