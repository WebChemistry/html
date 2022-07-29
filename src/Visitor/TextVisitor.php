<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

final class TextVisitor implements NodeVisitor
{

	/** @var callable(string, NodeProcessor): (DOMNode|null) */
	private $callback;

	/**
	 * @param callable(string, NodeProcessor): (DOMNode|null) $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor): DOMNode|TraverserAction|null
	{
		if ($node->nodeName === '#text') {
			return ($this->callback)($node->textContent, $processor);
		}

		return null;
	}

}
