<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use DOMText;
use Nette\Utils\Strings;

final class TruncateVisitor extends RootVisitor
{

	public bool $truncated = false;

	private int $_length;

	public function __construct(
		private int $length,
		private string $append = "\u{2026}",
	)
	{
	}

	public function enterRoot(DOMNode $node): void
	{
		$this->truncated = false;
		$this->_length = $this->length;

		if ($node instanceof DOMText) {
			$this->tryToTruncate($node);

			return;
		}

		$this->visitChildren($node);
	}

	private function tryToTruncate(DOMText $node): void
	{
		$length = mb_strlen((string) $node->nodeValue);

		if ($length > $this->_length) {
			$node->nodeValue = Strings::truncate((string) $node->nodeValue, $this->_length, $this->append);
			$this->truncated = true;
		}

		$this->_length -= $length;
	}

	private function visitChildren(DOMNode $node): void
	{
		$childNode = $node->firstChild;

		while ($childNode) {
			if ($this->truncated) {
				$tmpNode = $childNode;
				$childNode = $childNode->nextSibling;

				$node->removeChild($tmpNode);

				continue;
			}

			if ($childNode instanceof DOMText) {
				$this->tryToTruncate($childNode);

			} else {
				$this->visitChildren($childNode);

			}

			$childNode = $childNode->nextSibling;
		}
	}

}
