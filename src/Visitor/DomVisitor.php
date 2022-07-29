<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMDocumentFragment;
use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

final class DomVisitor
{

	/**
	 * @param NodeVisitor[] $visitors
	 */
	public function __construct(
		private array $visitors = [],
	)
	{
	}

	public function addVisitor(NodeVisitor $visitor): self
	{
		$this->visitors[] = $visitor;

		return $this;
	}

	public function visit(DOMDocumentFragment $node): void
	{
		$this->visitChildren($node, $this->visitors);
	}

	/**
	 * @param NodeVisitor[] $visitors
	 */
	private function visitChildren(DOMNode $node, array $visitors): void
	{
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
