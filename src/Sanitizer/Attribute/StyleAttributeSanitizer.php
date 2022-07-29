<?php declare(strict_types = 1);

namespace WebChemistry\Html\Sanitizer\Attribute;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

final class StyleAttributeSanitizer implements AttributeSanitizerInterface
{

	/**
	 * @param string[] $allowedStyles
	 */
	public function __construct(
		private array $allowedStyles,
	)
	{
	}

	public function getSupportedElements(): ?array
	{
		return null;
	}

	public function getSupportedAttributes(): ?array
	{
		return ['style'];
	}

	public function sanitizeAttribute(
		string $element,
		string $attribute,
		string $value,
		HtmlSanitizerConfig $config
	): ?string
	{
		$output = null;

		if (preg_match_all('#\s*(.+?)\s*:\s*(.+?)\s*(?:;+|$)\s*#m', $value, $matches, PREG_SET_ORDER)) {
			$output = [];

			foreach ($matches as [, $styleName, $styleValue]) {
				if (in_array($styleName, $this->allowedStyles)) {
					$output[] = sprintf('%s:%s', $styleName, $styleValue);
				}
			}

			$output = $output ? implode(';', $output) : null;
		}

		return $output;
	}

}
