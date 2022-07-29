<?php declare(strict_types = 1);

namespace WebChemistry\Html;

use LogicException;
use Symfony\Component\HtmlSanitizer\Parser\MastermindsParser;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;
use WebChemistry\Html\Exception\ParseException;
use WebChemistry\Html\Visitor\DomVisitor;

final class HtmlParser
{

	public readonly DomVisitor $visitor;

	public function __construct(
		private ParserInterface $parser = new MastermindsParser([
			'disable_html_ns' => true,
		]),
	)
	{
		$this->visitor = new DomVisitor();
	}

	public function parse(string $content): string
	{
		$node = $this->parser->parse($content);

		if (!$node) {
			return $content;
		}

		if (!($document = $node->ownerDocument)) {
			throw new ParseException('Document is not set.');
		}

		$this->visitor->visit($node);

		if (($content = $document->saveHTML($node)) === false) {
			throw new ParseException('Cannot convert html node to string.');
		}

		return $content;
	}

}
