<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use Nette\Utils\Strings;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Trim\TrimElementRule;
use WebChemistry\Html\Visitor\Trim\TrimElementByName;
use WebChemistry\Html\Visitor\Trim\TrimTextByName;
use WebChemistry\Html\Visitor\Trim\TrimTextRule;

final class TrimVisitor extends AbstractNodeVisitor
{

	private TrimElementRule $trimElementRule;

	private TrimTextRule $trimTextRule;

	/**
	 * @param bool $startTrim Removes empty elements from start
	 * @param bool $endTrim Removes empty elements from end
	 * @param bool $leftTrim Trim spaces from left in element
	 * @param bool $rightTrim Trim spaces from right in element
	 * @param int<0,max>|null $maxTrim Max empty elements in the middle of document, null => infinity
	 */
	public function __construct(
		private bool $startTrim = true,
		private bool $endTrim = true,
		private bool $leftTrim = true,
		private bool $rightTrim = true,
		private ?int $maxTrim = 1,
		?TrimElementRule $trimElementRule = null,
		?TrimTextRule $trimTextRule = null,
	)
	{
		$this->trimElementRule = $trimElementRule ?? new TrimElementByName();
		$this->trimTextRule = $trimTextRule ?? new TrimTextByName();
	}

	public function beforeTraverse(DOMNode $node, NodeProcessor $processor, BeforeTraverseMode $mode): void
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
			if (!$this->trimElementRule->isTrimmable($child)) {
				break;
			}

			$node->removeChild($child);
		}
	}

	private function endTrim(DOMNode $node): void
	{
		while ($child = $node->lastChild) {
			if (!$this->trimElementRule->isTrimmable($child)) {
				break;
			}

			$node->removeChild($child);
		}
	}

	public static function isEmptyNode(DOMNode $node): bool
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
			if (!$this->trimTextRule->isTrimmable($child)) {
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
			if (!$this->trimTextRule->isTrimmable($child)) {
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
			if (!$this->trimElementRule->isTrimmable($child)) {
				$counter = 0;
				$child = $child->nextSibling;

				continue;
			}

			$remove = $child;
			$child = $child->nextSibling;

			if ($counter >= $maxTrim) {
				$node->removeChild($remove);
			}

			$counter++;
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
