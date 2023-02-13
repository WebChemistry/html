<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use DOMText;
use Nette\Utils\Strings;
use WebChemistry\Html\Utility\NodeUtility;

final class TruncateVisitor extends RootVisitor
{

	public bool $truncated = false;

	private int $_length;

	public function __construct(
		private int $length,
		private string $textAppend = "\u{2026}",
		private ?string $htmlAppend = null,
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

	private function tryToTruncate(DOMText $node): DOMNode
	{
		$return = $node;
		$length = mb_strlen((string) $node->nodeValue);

		if ($length > $this->_length) {
			$node->nodeValue = Strings::truncate((string) $node->nodeValue, $this->_length, $this->textAppend);

			if ($append = $this->htmlAppend) {
				$return = NodeUtility::insertAfter($node, NodeUtility::createHtml($node, $append));
			}

			$this->truncated = true;
		}

		$this->_length -= $length;

		return $return;
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
				$childNode = $this->tryToTruncate($childNode);

			} else {
				$this->visitChildren($childNode);

			}

			$childNode = $childNode->nextSibling;
		}
	}

}
