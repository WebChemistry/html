<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

class CallbackVisitor extends AbstractNodeVisitor
{

	/** @var callable(DOMNode, NodeProcessor, NodeEnterMode): (DOMNode|null)|null */
	private $enter;

	/** @var callable(DOMNode, NodeProcessor, NodeLeaveMode): (DOMNode|null)|null */
	private $leave;

	/**
	 * @param callable(DOMNode, NodeProcessor, NodeEnterMode): (DOMNode|null)|null $enter
	 * @param callable(DOMNode, NodeProcessor, NodeLeaveMode): (DOMNode|null)|null $leave
	 */
	public function __construct(?callable $enter = null, ?callable $leave = null)
	{
		$this->enter = $enter;
		$this->leave = $leave;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		if (!$this->enter) {
			return null;
		}

		return ($this->enter)($node, $processor, $mode);
	}

	public function leaveNode(DOMNode $node, NodeProcessor $processor, NodeLeaveMode $mode): ?DOMNode
	{
		if (!$this->leave) {
			return null;
		}

		return ($this->leave)($node, $processor, $mode);
	}

}
