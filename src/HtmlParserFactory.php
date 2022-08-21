<?php declare(strict_types = 1);

namespace WebChemistry\Html;

use WebChemistry\Html\Visitor\NodeVisitor;

interface HtmlParserFactory
{

	/**
	 * @param NodeVisitor[] $visitors
	 */
	public function create(array $visitors = []): HtmlParser;

}
