<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

class CallbackVisitor implements NodeVisitor
{

	/** @var callable(DOMNode, NodeProcessor): (DOMNode|TraverserAction|null) */
	private $callback;

	/**
	 * @param callable(DOMNode, NodeProcessor): (DOMNode|TraverserAction|null) $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor): DOMNode|TraverserAction|null
	{
		return ($this->callback)($node, $processor);
	}

}
