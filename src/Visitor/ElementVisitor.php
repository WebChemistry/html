<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMElement;
use DOMNode;
use WebChemistry\Html\Node\Action\TraverserAction;
use WebChemistry\Html\Node\NodeProcessor;

final class ElementVisitor extends CallbackVisitor
{

	/**
	 * @param callable(DOMNode, NodeProcessor): (DOMNode|TraverserAction|null) $callback
	 * @param string[]|null $tags
	 */
	public function __construct(
		callable $callback,
		private ?array $tags = null,
	)
	{
		parent::__construct($callback);
	}

	final public function enterNode(DOMNode $node, NodeProcessor $processor): DOMNode|TraverserAction|null
	{
		if (!$node instanceof DOMElement) {
			return null;
		}

		if ($this->tags === null || in_array($node->nodeName, $this->tags, true)) {
			return parent::enterNode($node, $processor);
		}

		return null;
	}

}
