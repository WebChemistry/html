<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;
use LogicException;
use Nette\Utils\Strings;
use WebChemistry\Html\Node\NodeProcessor;
use WebChemistry\Html\Renderer\NodeRenderer;
use WebChemistry\Html\Utility\HtmlString;
use WebChemistry\Html\Utility\NodeUtility;
use WebChemistry\Html\Visitor\Mode\AfterTraverseMode;
use WebChemistry\Html\Visitor\Mode\NodeEnterMode;
use WebChemistry\Html\Visitor\Rule\TruncateRule;

final class TruncateVisitor extends RootVisitor
{

	private bool $truncatedNode = false;

	/** @var TruncateRule[] */
	private array $rules = [];

	private int $_length;

	public function __construct(
		private int $length,
		private string|HtmlString $append = "\u{2026}",
	)
	{
	}

	public function addRule(TruncateRule $rule): self
	{
		$this->rules[] = $rule;

		return $this;
	}

	public function enterRoot(DOMNode $node, NodeProcessor $processor): void
	{
		$this->_length = $this->length;

		$visitor = new DomVisitor($processor->getParser(), [
			new CallbackVisitor(
				$this->enter(...),
				afterTraverse: $this->after(...),
			),
		]);

		$visitor->visit($node);
	}

	private function enter(DOMNode $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		if ($this->isTruncated()) {
			$mode->removeNode = true;

			return null;
		}

		if ($node instanceof DOMText) {
			return $this->enterText($node, $processor, $mode);
		}

		foreach ($this->rules as $rule) {
			if ($rule->matchNode($node)) {
				$this->modifyLength($rule->getLengthToTruncate());
			}
		}

		return null;
	}

	private function enterText(DOMText $node, NodeProcessor $processor, NodeEnterMode $mode): ?DOMNode
	{
		$previous = $this->_length;
		$this->modifyLength(mb_strlen((string) $node->nodeValue));
		$return = null;

		if ($this->isTruncated()) {
			$return = $this->createTruncatedNode($processor, [
				$processor->createText(Strings::truncate((string) $node->nodeValue, $previous, '')),
			]);

			$this->truncatedNode = true;
		}

		return $return;
	}

	private function after(DOMNode $node, NodeProcessor $processor, AfterTraverseMode $mode): void
	{
		if ($this->truncatedNode) {
			return;
		}

		$node->appendChild(
			$this->createTruncatedNode($processor),
		);
	}

	/**
	 * @param NodeProcessor $processor
	 * @param DOMNode[] $prependNodes
	 * @return DOMDocumentFragment
	 */
	private function createTruncatedNode(NodeProcessor $processor, array $prependNodes = []): DOMDocumentFragment
	{
		$append = $this->append;

		if ($append instanceof HtmlString) {
			$appendNode = $processor->createFromHtml($append->getHtml()) ?? throw new LogicException('Cannot create html from string.');
		} else {
			$appendNode = $processor->createText($append);
		}

		$fragment = $processor->createFragment();

		foreach ($prependNodes as $prependNode) {
			$fragment->appendChild($prependNode);
		}

		if ($doc = $fragment->ownerDocument) {
			$appendNode = $doc->importNode($appendNode, true);
		}

		$fragment->appendChild($appendNode);

		return $fragment;
	}

	public function isTruncated(): bool
	{
		return $this->_length < 0;
	}

	private function modifyLength(int $length): void
	{
		$this->_length -= $length;
	}

}
