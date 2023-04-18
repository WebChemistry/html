<?php declare(strict_types = 1);

namespace WebChemistry\Html\Utility;

use Stringable;

final class HtmlString implements Stringable
{

	public function __construct(
		private string $html,
	)
	{
	}

	public function getHtml(): string
	{
		return $this->html;
	}

	public function __toString(): string
	{
		return $this->html;
	}

}
