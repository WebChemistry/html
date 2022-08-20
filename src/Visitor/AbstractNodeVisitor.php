<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

abstract class AbstractNodeVisitor implements NodeVisitor
{

	public function beforeTraverse(DOMNode $node, BeforeTraverseMode $mode): void
	{
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		return null;
	}

	public function leaveNode(DOMNode $node, NodeProcessor $processor, NodeLeaveMode $mode): ?DOMNode
	{
		return null;
	}

}
