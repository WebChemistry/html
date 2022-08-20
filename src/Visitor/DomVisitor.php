<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

final class DomVisitor
{

	/**
	 * @param NodeVisitor[] $nodeVisitors
	 */
	public function __construct(
		private array $nodeVisitors = [],
	)
	{
	}

	public function addVisitor(NodeVisitor $visitor, ?int $insert = null): self
	{
		if ($insert === null) {
			$this->nodeVisitors[] = $visitor;
		} else {
			array_splice($this->nodeVisitors, $insert, 0, [$visitor]);
		}

		return $this;
	}

	/**
	 * @param NodeVisitor[] $visitors
	 */
	public function addVisitors(array $visitors): self
	{
		foreach ($visitors as $visitor) {
			$this->nodeVisitors[] = $visitor;
		}

		return $this;
	}

	public function visit(DOMNode $node): void
	{
		if (!$this->nodeVisitors) {
			return;
		}

		$visitors = $this->nodeVisitors;

		foreach ($this->nodeVisitors as $i => $visitor) {
			$visitor->beforeTraverse($node, $mode = new BeforeTraverseMode());

			if ($mode->dontTraverseChildren) {
				unset($visitors[$i]);
			}
		}

		$this->visitChildren($node, $visitors);
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

		$enterVisitors = $visitors;

		foreach ($enterVisitors as $key => $visitor) {
			$return = $visitor->enterNode($node, $processor, $mode = new NodeEnterMode());

			if ($return !== null) {
				$node->parentNode?->replaceChild($return, $node);

				return;
			}

			if ($mode->dontTraverseChildren) {
				unset($enterVisitors[$key]);
			}

			if ($mode->stopTraversal) {
				break;
			}
		}

		$this->visitChildren($node, $enterVisitors);

		foreach ($visitors as $visitor) {
			$return = $visitor->leaveNode($node, $processor, $mode = new NodeLeaveMode());

			if ($return !== null) {
				$node->parentNode?->replaceChild($return, $node);

				return;
			}

			if ($mode->stopTraversal) {
				break;
			}
		}
	}

}
