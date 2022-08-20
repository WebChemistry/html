<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

abstract class RootVisitor implements NodeVisitor
{

	abstract public function enterRoot(DOMNode $node): void;

	final public function beforeTraverse(DOMNode $node, BeforeTraverseMode $mode): void
	{
		$this->enterRoot($node);

		$mode->dontTraverseChildren = true;
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
