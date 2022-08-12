<?php declare(strict_types = 1);

namespace WebChemistry\Html;

use DomainException;
use DOMNode;
use Symfony\Component\HtmlSanitizer\Parser\MastermindsParser;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;
use WebChemistry\Html\Exception\ParseException;
use WebChemistry\Html\Renderer\NodeRenderer;
use WebChemistry\Html\Visitor\DomVisitor;

final class HtmlParser
{

	public readonly DomVisitor $visitor;

	private ParserInterface $parser;

	public function __construct(?ParserInterface $parser = null)
	{
		$this->parser = $parser ?? new MastermindsParser([
			'disable_html_ns' => true,
		]);
		$this->visitor = new DomVisitor();
	}

	public function createNode(string $content): ?DOMNode
	{
		return $this->parser->parse($content);
	}

	public function parse(string|DOMNode $content): string
	{
		$node = $this->parseOnly($content);

		try {
			return NodeRenderer::render($node);
		} catch (DomainException $exception) {
			throw new ParseException($exception->getMessage());
		}
	}

	public function parseOnly(string|DOMNode $content): DOMNode
	{
		$node = is_string($content) ? $this->createNode($content) : $content;

		if (!$node) {
			throw new ParseException('Cannot create node from content.');
		}

		$this->visitor->visit($node);

		return $node;
	}

}
