<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Trim;

use DOMNode;

interface TrimTextRule
{

	public function isTrimable(DOMNode $node): bool;

}
