<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Rule;

use DOMNode;

interface TruncateRule
{

	public function getLengthToTruncate(): int;

	public function matchNode(DOMNode $node): bool;

}
