<?php declare(strict_types = 1);

namespace WebChemistry\Html\Bridge\Nette\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Symfony\Component\HtmlSanitizer\Parser\MastermindsParser;
use Symfony\Component\HtmlSanitizer\Parser\ParserInterface;
use WebChemistry\Html\HtmlParserFactory;

final class HtmlExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'parser' => Expect::structure([
				'options' => Expect::arrayOf(Expect::mixed())->default([
					'disable_html_ns' => true,
				]),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $config */
		$config = $this->getConfig();

		$builder->addDefinition($this->prefix('parser'))
			->setType(ParserInterface::class)
			->setFactory(MastermindsParser::class, [$config->parser->options]);

		$builder->addFactoryDefinition($this->prefix('htmlParser.factory'))
			->setImplement(HtmlParserFactory::class);
	}

}
