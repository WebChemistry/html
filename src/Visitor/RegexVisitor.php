<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMNode;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;

final class RegexVisitor extends AbstractNodeVisitor
{

	/** @var callable(string, NodeProcessor=, list<string>=): (DOMNode|null) */
	private $callback;

	/**
	 * @param callable(string, NodeProcessor=, list<string>=): (DOMNode|null) $callback
	 */
	public function __construct(
		private string $pattern,
		callable $callback,
		private int $group = 0,
	)
	{
		$this->callback = $callback;
	}

	public function enterNode(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		if ($node->nodeName === '#text') {
			$text = $node->textContent;

			if (!preg_match_all($this->pattern, $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
				return null;
			}

			$nodes = [];
			$last = 0;

			foreach ($matches as $match) {
				$return = ($this->callback)($match[$this->group][0], $processor, array_column($match, 0));

				if (!$return instanceof DOMNode) {
					continue;
				}


				[$groupText, $groupOffset] = $match[$this->group];

				$nodes[] = $processor->createText(substr($text, $last, $groupOffset - $last));
				$nodes[] = $return;

				$last = $match[$this->group][1] + strlen($groupText);
			}

			if (!$nodes) {
				return null;
			}

			if ($last !== 0) {
				$nodes[] = $processor->createText(substr($text, $last));
			}

			return $processor->createCollection(...$nodes);
		}

		return null;
 	}

}
