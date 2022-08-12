<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

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
		}

		if ($visitor instanceof NodeVisitor) {
			$this->nodeVisitors[] = $visitor;
		}

		return $this;
	}

	public function addVisitors(NodeVisitor|DocumentVisitor ... $visitors): self
	{
		foreach ($visitors as $visitor) {
			$this->addVisitor($visitor);
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

		if (!$this->nodeVisitors) {
			return;
		}

		foreach ($this->nodeVisitors as $visitor) {
			$visitor->beforeTraverse($node);
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
