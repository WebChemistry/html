<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

interface NodeVisitor
{

	public function enterNode(DOMNode $node, NodeProcessor $processor): DOMNode|TraverserAction|null;

}
