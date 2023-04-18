<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\AfterTraverseMode;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

interface NodeVisitor
{

	public function beforeTraverse(DOMNode $node, NodeProcessor $processor, BeforeTraverseMode $mode): void;

	public function afterTraverse(DOMNode $node, NodeProcessor $processor, AfterTraverseMode $mode): void;

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode;

	public function leaveNode(DOMNode $node, NodeProcessor $processor, NodeLeaveMode $mode): ?DOMNode;

}
