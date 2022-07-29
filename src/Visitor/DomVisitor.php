<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMDocumentFragment;
use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

final class DomVisitor
{

	/** @var NodeVisitor[] */
	private array $nodeVisitors = [];

	/** @var DocumentVisitor[] */
	private array $documentVisitors = [];

	public function addVisitor(NodeVisitor|DocumentVisitor $visitor): self
	{
		if ($visitor instanceof DocumentVisitor) {
			$this->documentVisitors[] = $visitor;
		} else {
			$this->nodeVisitors[] = $visitor;
		}

		return $this;
	}

	public function visit(DOMNode $node): void
	{
		if ($document = $node->ownerDocument) {
			foreach ($this->documentVisitors as $visitor) {
				$visitor->enterDocument($document);
			}
		}

		$this->visitChildren($node, $this->nodeVisitors);
	}

	/**
	 * @param NodeVisitor[] $visitors
	 */
	private function visitChildren(DOMNode $node, array $visitors): void
	{
		if (!$visitors) {
			return;
		}

		$node = $node->firstChild;
		while ($node) {
			$current = $node;
			$node = $node->nextSibling;

			$this->visitNode($current, $visitors);
		}
	}

	/**
	 * @param NodeVisitor[] $visitors
	 */
	private function visitNode(DOMNode $node, array $visitors): void
	{
		$processor = new NodeProcessor($node);

		foreach ($visitors as $key => $visitor) {
			$return = $visitor->enterNode($node, $processor);

			if ($return instanceof TraverserAction) {
				if ($return === TraverserAction::DONT_TRAVERSE_CHILDREN) {
					unset($visitors[$key]);
				} else if ($return === TraverserAction::STOP_TRAVERSAL) {
					break;
				}
			} else if ($return instanceof DOMNode) {
				$node->parentNode?->replaceChild($return, $node);

				return;
			}
		}

		$this->visitChildren($node, $visitors);
	}

}
