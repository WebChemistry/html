<?php declare(strict_types = 1);

use Tester\Assert;
use Tester\Environment;
use WebChemistry\Html\HtmlParser;
use WebChemistry\Html\Visitor\Trim\TrimElementRule;
use WebChemistry\Html\Visitor\TrimVisitor;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();
Environment::setupFunctions();

$customElementRule = new class implements TrimElementRule {

	public function isTrimable(DOMNode $node): bool
	{
		if ($node->nodeName !== 'p') {
			return false;
		}

		if (TrimVisitor::isEmptyNode($node)) {
			return true;
		}

		if ($node->childNodes->count() === 1 && ($firstChild = $node->firstChild) && $firstChild->nodeName === 'br') {
			return true;
		}

		return false;
	}

};

test('remove empty elements from start', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p></p><p>1</p>'));
	Assert::same('<p>1</p>', $parser->parse('<p></p><p></p><p>1</p>'));
});

test('remove empty elements from end', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p>1</p><p></p>'));
	Assert::same('<p>1</p>', $parser->parse('<p>1</p><p></p><p></p>'));
});

test('remove empty elements from middle', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p><p></p><p>2</p>', $parser->parse('<p>1</p><p></p><p></p><p></p><p></p><p></p><p>2</p>'));
});

test('remove empty elements from both sides', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p></p><p>1</p><p></p>'));
	Assert::same('<p>1</p>', $parser->parse('<p></p><p></p><p>1</p><p></p><p></p>'));
});

test('trim elements from from left', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p></p><p>  1</p><p></p>'));
});

test('trim elements from from right', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p></p><p>1  </p><p></p>'));
});

test('trim elements from from both sides', function (): void {
	$parser = new HtmlParser([
		new TrimVisitor()
	]);

	Assert::same('<p>1</p>', $parser->parse('<p></p><p>  1  </p><p></p>'));
});

test('custom element function', function () use ($customElementRule): void {
	$parser = new HtmlParser([
		new TrimVisitor(
			trimElementRule: $customElementRule,
		),
	]);

	Assert::same('<p>1</p>', $parser->parse('<p><br></p><p></p><p>  1  </p><p><br></p>'));
});
