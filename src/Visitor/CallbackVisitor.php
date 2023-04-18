<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\AfterTraverseMode;
use WebChemistry\Html\Visitor\Mode\BeforeTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Mode\NodeLeaveMode;

class CallbackVisitor extends AbstractNodeVisitor
{

	/** @var callable(DOMNode, NodeProcessor, NodeEnterMode): (DOMNode|null)|null */
	private $enter;

	/** @var callable(DOMNode, NodeProcessor, NodeLeaveMode): (DOMNode|null)|null */
	private $leave;

	/** @var callable(DOMNode, NodeProcessor, BeforeTraverseMode): (void)|null */
	private $beforeTraverse;

	/** @var callable(DOMNode, NodeProcessor, AfterTraverseMode): (void)|null */
	private $afterTraverse;

	/**
	 * @param callable(DOMNode, NodeProcessor, NodeEnterMode): (DOMNode|null)|null $enter
	 * @param callable(DOMNode, NodeProcessor, NodeLeaveMode): (DOMNode|null)|null $leave
	 * @param callable(DOMNode, NodeProcessor, BeforeTraverseMode): (void)|null $beforeTraverse
	 * @param callable(DOMNode, NodeProcessor, AfterTraverseMode): (void)|null $afterTraverse
	 */
	public function __construct(?callable $enter = null, ?callable $leave = null, ?callable $beforeTraverse = null, ?callable $afterTraverse = null)
	{
		$this->enter = $enter;
		$this->leave = $leave;
		$this->beforeTraverse = $beforeTraverse;
		$this->afterTraverse = $afterTraverse;
	}

	public function beforeTraverse(DOMNode $node, NodeProcessor $processor, BeforeTraverseMode $mode): void
	{
		if ($this->beforeTraverse) {
			($this->beforeTraverse)($node, $processor, $mode);
		}
	}

	public function afterTraverse(DOMNode $node, NodeProcessor $processor, AfterTraverseMode $mode): void
	{
		if ($this->afterTraverse) {
			($this->afterTraverse)($node, $processor, $mode);
		}
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
