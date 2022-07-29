<?php declare(strict_types = 1);

namespace WebChemistry\Html\Visitor;

use DOMDocument;

interface DocumentVisitor
{

	public function enterDocument(DOMDocument $document): void;

}
