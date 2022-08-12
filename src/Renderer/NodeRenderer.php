<?php declare(strict_types = 1);

namespace WebChemistry\Html\Renderer;

use DomainException;
use DOMDocument;
use DOMNode;

final class NodeRenderer
{

	/**
	 * @throws DomainException
	 */
	public static function getDocument(DOMNode $node): DOMDocument
	{
		return $node->ownerDocument ?? throw new DomainException('Document is not set.');
	}

	/**
	 * @throws DomainException
	 */
	public static function render(DOMNode $node): string
	{
		if (($content = self::getDocument($node)->saveHTML($node)) === false) {
			throw new DomainException('Cannot convert html node to string.');
		}

		return $content;
	}

}
