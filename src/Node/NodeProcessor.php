<?php declare(strict_types = 1);

namespace WebChemistry\Html\Node;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;
use LogicException;
use Masterminds\HTML5;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;

final class NodeProcessor
{

	public function __construct(
		private DOMNode $node,
		private ParserInterface $parser,
	)
	{
	}

	public function getParser(): ParserInterface
	{
		return $this->parser;
	}

	public function createText(string $value): DOMText
	{
		return $this->getDocument()->createTextNode($value);
	}

	/**
	 * @param array<string, string> $attributes
	 */
	public function createElement(string $name, ?string $value = null, array $attributes = []): DOMElement
	{
		$document = $this->getDocument();
		$element = $value === null ? $document->createElement($name) : $document->createElement($name, htmlspecialchars($value));

		foreach ($attributes as $name => $val) {
			$element->setAttribute($name, htmlspecialchars($val));
		}

		return $element;
	}

	public function createFromHtml(string $html): ?DOMNode
	{
		return $this->parser->parse($html);
	}

	public function createFragmentFromHtml(string $html): DOMDocumentFragment
	{
		return (new HTML5(['disable_html_ns' => true]))->loadHTMLFragment($html, [
			HTML5\Parser\DOMTreeBuilder::OPT_TARGET_DOC => $this->getDocument(),
		]);
	}

	public function createFragment(): DOMDocumentFragment
	{
		return $this->getDocument()->createDocumentFragment();
	}

	public function createCollection(DOMNode ... $nodes): DOMDocumentFragment
	{
		$fragment = $this->getDocument()->createDocumentFragment();

		foreach ($nodes as $node) {
			$fragment->appendChild($node);
		}

		return $fragment;
	}

	public function addChildrenTo(DOMNode $source, DOMNode $target): DOMNode
	{
		foreach ($source->childNodes as $node) {
			$target->appendChild(clone $node);
		}

		return $target;
	}

	public function getDocument(): DOMDocument
	{
		return $this->node->ownerDocument ?? throw new LogicException('DOM is not attached to document.');
	}

}
