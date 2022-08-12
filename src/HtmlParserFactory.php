<?php declare(strict_types = 1);

namespace WebChemistry\Html;

interface HtmlParserFactory
{

	public function create(): HtmlParser;

}
