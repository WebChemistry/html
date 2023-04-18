<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\AfterTraverseMode;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

abstract class RootVisitor implements NodeVisitor
{

	abstract public function enterRoot(DOMNode $node, NodeProcessor $processor): void;

	public function leaveRoot(DOMNode $node, NodeProcessor $processor): void
	{
	}

	final public function beforeTraverse(DOMNode $node, NodeProcessor $processor, BeforeTraverseMode $mode): void
	{
		$this->enterRoot($node, $processor);

		$mode->dontTraverseChildren = true;
	}

	public function afterTraverse(DOMNode $node, NodeProcessor $processor, AfterTraverseMode $mode): void
	{
		$this->leaveRoot($node, $processor);
	}

	final public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		$mode->dontTraverseChildren = true;

		return null;
	}

	final public function leaveNode(DOMNode $node, NodeProcessor $processor, NodeLeaveMode $mode): ?DOMNode
	{
		return null;
	}

}
