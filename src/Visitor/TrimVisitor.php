<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use Nette\Utils\Strings;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;

final class TrimVisitor extends AbstractNodeVisitor
{

	/**
	 * @param string[] $names
	 * @param bool $startTrim Removes empty elements from start
	 * @param bool $endTrim Removes empty elements from end
	 * @param bool $leftTrim Trim spaces from left in element
	 * @param bool $rightTrim Trim spaces from right in element
	 * @param int<0,max>|null $maxTrim Max empty elements, null => infinity
	 */
	public function __construct(
		private array $names = ['p'],
		private bool $startTrim = true,
		private bool $endTrim = true,
		private bool $leftTrim = true,
		private bool $rightTrim = true,
		private ?int $maxTrim = 1,
	)
	{
	}

	public function beforeTraverse(DOMNode $node, BeforeTraverseMode $mode): void
	{
		if ($this->startTrim) {
			$this->startTrim($node);
		}

		if ($this->endTrim) {
			$this->endTrim($node);
		}

		if ($this->leftTrim) {
			$this->leftTrim($node);
		}

		if ($this->rightTrim) {
			$this->rightTrim($node);
		}

		if ($this->maxTrim !== null) {
			$this->maxTrim($node, $this->maxTrim);
		}
	}

	private function startTrim(DOMNode $node): void
	{
		while ($child = $node->firstChild) {
			if (!in_array($child->nodeName, $this->names, true)) {
				break;
			}

			$parent = $child->parentNode;

			if (!$parent) {
				break;
			}

			if ($this->isEmptyNode($child)) {
				$parent->removeChild($child);

				continue;
			}

			break;
		}
	}

	private function endTrim(DOMNode $node): void
	{
		while ($child = $node->lastChild) {
			if (!in_array($child->nodeName, $this->names, true)) {
				break;
			}

			$parent = $child->parentNode;

			if (!$parent) {
				break;
			}

			if ($this->isEmptyNode($child)) {
				$parent->removeChild($child);

				continue;
			}

			break;
		}
	}

	private function isEmptyNode(DOMNode $node): bool
	{
		if (!$node->hasChildNodes()) {
			return true;
		}

		if (($child = $node->firstChild) && $child->nodeName === '#text' && $child === $node->lastChild) {
			if (self::trim($child->textContent) === '') {
				return true;
			}
		}

		return false;
	}

	private function leftTrim(DOMNode $node): void
	{
		/** @var DOMNode $child */
		foreach ($node->childNodes as $child) {
			if (!in_array($child->nodeName, $this->names, true)) {
				continue;
			}

			$text = $child->firstChild;

			if ($text === null || $text->nodeName !== '#text') {
				continue;
			}

			$trimmed = self::ltrim($text->textContent);

			if ($trimmed !== $text->textContent) {
				$text->textContent = $trimmed;
			}
		}
	}

	private function rightTrim(DOMNode $node): void
	{
		/** @var DOMNode $child */
		foreach ($node->childNodes as $child) {
			if (!in_array($child->nodeName, $this->names, true)) {
				continue;
			}

			$text = $child->lastChild;

			if ($text === null || $text->nodeName !== '#text') {
				continue;
			}

			$trimmed = self::rtrim($text->textContent);

			if ($trimmed !== $text->textContent) {
				$text->textContent = $trimmed;
			}
		}
	}

	/**
	 * @param DOMNode $node
	 * @param int<0, max> $maxTrim
	 */
	private function maxTrim(DOMNode $node, int $maxTrim): void
	{
		$counter = 0;

		$child = $node->firstChild;

		while ($child) {
			if (!in_array($child->nodeName, $this->names, true)) {
				$counter = 0;
				$child = $child->nextSibling;

				continue;
			}

			$parent = $child->parentNode;

			if (!$parent) {
				$counter = 0;
				$child = $child->nextSibling;

				continue;
			}

			if ($this->isEmptyNode($child)) {
				$remove = $child;
				$child = $child->nextSibling;

				if ($counter >= $maxTrim) {
					$parent->removeChild($remove);
				}

				$counter++;

				continue;
			}

			$counter = 0;
			$child = $child->nextSibling;
		}
	}

	public static function trim(string $content): string
	{
		return Strings::replace($content, '#^\s*(.*?)\s*$#u', '$1');
	}

	public static function ltrim(string $content): string
	{
		return Strings::replace($content, '#^\s+#u');
	}

	public static function rtrim(string $content): string
	{
		return Strings::replace($content, '#\s+$#u');
	}

}
