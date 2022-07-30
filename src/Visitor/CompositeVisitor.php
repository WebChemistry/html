<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

final class CompositeVisitor extends AbstractNodeVisitor
{

	/** @var NodeVisitor[] */
	private array $visitors;

	public function __construct(NodeVisitor ... $visitors)
	{
		$this->visitors = $visitors;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		foreach ($this->visitors as $visitor) {
			$return = $visitor->enterNode($node, $processor, $mode);

			if ($return !== null) {
				return $return;
			}
		}

		return null;
	}

	public function leaveNode(DOMNode $node, NodeProcessor $processor, NodeLeaveMode $mode): ?DOMNode
	{
		foreach ($this->visitors as $visitor) {
			$return = $visitor->leaveNode($node, $processor, $mode);

			if ($return !== null) {
				return $return;
			}
		}

		return null;
	}

}
