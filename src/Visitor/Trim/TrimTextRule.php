<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Trim;

use DOMNode;

interface TrimTextRule
{

	public function isTrimmable(DOMNode $node): bool;

}
