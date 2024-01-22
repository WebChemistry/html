<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Trim;

use DOMNode;

final class TrimTextByName implements TrimTextRule
{

	/**
	 * @param string[] $names
	 */
	public function __construct(
		private array $names = ['p'],
	)
	{
	}

	public function isTrimmable(DOMNode $node): bool
	{
		return in_array($node->nodeName, $this->names, true);
	}

}
