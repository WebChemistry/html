<?php declare(strict_types = 1);

namespace WebChemistry\Html\Node;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;
use LogicException;

final class NodeProcessor
{

	public function __construct(
		private DOMNode $node,
	)
	{
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
		$element = $value === null ? $document->createElement($name) : $document->createElement($name, $value);

		foreach ($attributes as $name => $val) {
			$element->setAttribute($name, $val);
		}

		return $element;
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
