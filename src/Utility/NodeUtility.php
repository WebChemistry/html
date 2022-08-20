<?php declare(strict_types = 1);

namespace WebChemistry\Html\Utility;

use DomainException;
use DOMDocument;
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
	 * @return DOMNodeList<DOMNode>
	 */
	public static function getElementsByTagName(DOMNode $node, string $tagName): DOMNodeList
	{
		if ($node instanceof DOMDocument) {
			return $node->getElementsByTagName($tagName);
		}

		return self::filter($node, $tagName);
	}

}
