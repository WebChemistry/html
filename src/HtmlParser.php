<?php declare(strict_types = 1);

namespace WebChemistry\Html;

use DomainException;
use DOMNode;
use Symfony\Component\HtmlSanitizer\Parser\MastermindsParser;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;
use WebChemistry\Html\Exception\ParseException;
use WebChemistry\Html\Renderer\NodeRenderer;
use WebChemistry\Html\Visitor\DomVisitor;
use WebChemistry\Html\Visitor\NodeVisitor;

final class HtmlParser
{

	private DomVisitor $visitor;

	private ParserInterface $parser;

	/**
	 * @param NodeVisitor[] $visitors
	 */
	public function __construct(array $visitors = [], ?ParserInterface $parser = null)
	{
		$this->parser = $parser ?? new MastermindsParser([
			'disable_html_ns' => true,
		]);
		$this->visitor = new DomVisitor($this->parser, $visitors);
	}

	public function withVisitor(NodeVisitor $visitor, ?int $insertBefore = null): self
	{
		$self = new self([], $this->parser);
		$self->visitor = clone $this->visitor;
		$self->visitor->addVisitor($visitor, $insertBefore);

		return $self;
	}

	public function withVisitors(NodeVisitor ... $visitors): self
	{
		$self = new self([], $this->parser);
		$self->visitor = clone $this->visitor;
		$self->visitor->addVisitors($visitors);

		return $self;
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
