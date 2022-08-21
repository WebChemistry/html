<?php declare(strict_types = 1);

namespace WebChemistry\Html\Utility;

use DomainException;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelectorConverter;
use WebChemistry\Html\Renderer\NodeRenderer;

final class NodeUtility
{

	/**
	 * @return DOMNodeList<DOMNode>
	 */
	public static function filter(DOMNode $node, string $selector): DOMNodeList
	{
		$result = (new DOMXPath(NodeRenderer::getDocument($node)))->query(
			(new CssSelectorConverter())->toXPath($selector), $node,
		);

		if ($result === false) {
			throw new DomainException(sprintf('Selector "%s" is invalid or DOM node is invalid.', $selector));
		}

		return $result;
	}

	/**
	 * @return iterable<DOMElement>
	 */
	public static function filterElements(DOMNode $node, string $selector): iterable
	{
		$result = (new DOMXPath(NodeRenderer::getDocument($node)))->query(
			(new CssSelectorConverter())->toXPath($selector), $node,
		);

		if ($result === false) {
			throw new DomainException(sprintf('Selector "%s" is invalid or DOM node is invalid.', $selector));
		}

		foreach ($result as $item) {
			if ($item instanceof DOMElement) {
				yield $item;
			}
		}
	}

	/**
	 * @return DOMNodeList<DOMElement>
	 */
	public static function getElementsByTagName(DOMNode $node, string $tagName): DOMNodeList
	{
		if ($node instanceof DOMDocument) {
			return $node->getElementsByTagName($tagName);
		}

		/** @var DOMNodeList<DOMElement> */
		return self::filter($node, $tagName);
	}

	public static function appendAttribute(DOMElement $element, string $attribute, string $value): void
	{
		$element->setAttribute(
			$attribute,
			($val = $element->getAttribute($attribute)) ? $val . ' ' . $value : $value,
		);
	}

	public static function removeNodeKeepChildren(DOMNode $node): void
	{
		$parent = $node->parentNode;
		if (!$parent) {
			throw new DomainException('DOMNode does not have parent node.');
		}

		$length = $node->childNodes->length;
		if ($length === 0) {
			$parent->removeChild($node);

			return;
		}

		if ($length === 1 && ($first = $node->firstChild)) {
			$parent->replaceChild($first, $node);

			return;
		}

		/** @var DOMNode $child */
		foreach ($node->childNodes as $child) {
			$parent->insertBefore($child->cloneNode(true), $node);
		}

		$parent->removeChild($node);
	}

}
