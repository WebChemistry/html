<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor\Mode;

final class NodeEnterMode
{

	public bool $dontTraverseChildren = false;

	public bool $stopTraversal = false;

}
