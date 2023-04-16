<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Rule;

use DOMElement;
use DOMNode;

final class ElementTruncateRule implements TruncateRule
{

	/**
	 * @param array<string, string> $attributes
	 */
	public function __construct(
		private string $tag,
		private int $length,
		private array $attributes = [],
	)
	{
	}

	public function getLengthToTruncate(): int
	{
		return $this->length;
	}

	public function matchNode(DOMNode $node): bool
	{
		if (!$node instanceof DOMElement) {
			return false;
		}

		if ($node->nodeName !== $this->tag) {
			return false;
		}

		foreach ($this->attributes as $name => $value) {
			$attributeValues = explode(' ', $node->getAttribute($name));

			if (!in_array($value, $attributeValues, true)) {
				return false;
			}
		}

		return true;
	}

}
