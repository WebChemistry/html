<?php declare(strict_types = 1);

namespace WebChemistry\Html\Sanitizer;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;

interface HtmlSanitizerFactory
{

	public function create(): HtmlSanitizer;

}
