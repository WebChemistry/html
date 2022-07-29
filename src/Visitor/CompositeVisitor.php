<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

final class CompositeVisitor implements NodeVisitor
{

	/** @var NodeVisitor[] */
	private array $visitors;

	public function __construct(NodeVisitor ... $visitors)
	{
		$this->visitors = $visitors;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor): DOMNode|TraverserAction|null
	{
		foreach ($this->visitors as $visitor) {
			$return = $visitor->enterNode($node, $processor);

			if ($return !== null) {
				return $return;
			}
		}

		return null;
	}

}
