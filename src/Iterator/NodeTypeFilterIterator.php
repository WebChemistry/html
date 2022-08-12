<?php declare(strict_types = 1);

namespace WebChemistry\Html\Iterator;

use DOMNode;
use Generator;
use IteratorIterator;

/**
 * @template T of DOMNode
 * @extends IteratorIterator<int, T, Generator<T>>
 */
final class NodeTypeFilterIterator extends IteratorIterator
{

	/**
	 * @param class-string<T> $className
	 */
	public function __construct(DOMNode $node, string $className, ?int $depth = null, bool $floating = false)
	{
		parent::__construct(
			$floating ? self::createFloatingGenerator($node, $className, $depth) :
				self::createGenerator($node, $className, $depth)
		);
	}

	public static function createFloatingGenerator(DOMNode $node, string $className, ?int $depth = null): Generator
	{
		if ($node instanceof $className) {
			yield $node;

			if ($depth === null || $depth >= 1) {
				foreach ($node->childNodes as $child) {
					yield from self::createGenerator($child, $className, $depth === null ? null : $depth - 1);
				}
			}
		} else {
			foreach ($node->childNodes as $child) {
				yield from self::createFloatingGenerator($child, $className, $depth);
			}
		}
	}

	/**
	 * @template T of DOMNode
	 * @param class-string<T> $className
	 * @return Generator<T>
	 */
	public static function createGenerator(DOMNode $node, string $className, ?int $depth = null): Generator
	{
		if ($node instanceof $className) {
			yield $node;
		}

		if ($depth === null || $depth >= 1) {
			foreach ($node->childNodes as $child) {
				yield from self::createGenerator($child, $className, $depth === null ? null : $depth - 1);
			}
		}
	}

}
