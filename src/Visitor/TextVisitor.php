<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;

final class TextVisitor extends AbstractNodeVisitor
{

	/** @var callable(string, NodeProcessor=): (DOMNode|null) */
	private $callback;

	/**
	 * @param callable(string, NodeProcessor=): (DOMNode|null) $callback
	 */
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		if ($node->nodeName === '#text') {
			return ($this->callback)($node->textContent, $processor);
		}

		return null;
	}

}
