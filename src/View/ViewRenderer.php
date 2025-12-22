<?php

/* View renderer */

namespace GioPHP\View;

use GioPHP\DOM\DOMParser;
use GioPHP\Services\ComponentService;

class ViewRenderer
{
	private string $htmlContent = '';
	private object $dom;

	private ComponentService $components;

	public function __construct (ComponentService $components)
	{
		$this->components = $components;
	}

	public function beginCapture (): void
	{
		ob_start();
		echo '<div></div>';
	}

	public function endCapture (): void
	{
		$this->htmlContent = ob_get_clean();
	}

	public function setComponentsForElements (): void
	{
		if(empty($this->htmlContent))
		{
			return;
		}

		$this->parseHTML();
	}

	private function parseHTML (): void
	{
		$parser = new DOMParser($this->htmlContent);

		$components = $this->components->getComponents();

		$customTags = array_keys($components);
		$nodes = $parser->getNodeTuple($customTags);

		// Iterate over the found custom nodes
		foreach($nodes as $node)
		{
			$tagName = trim($node->localName);

			if(!isset($components[$tagName]))
			{
				continue;
			}

			// Get the component class
			$component = $components[$tagName];

			// Store the component's content into the buffer
			$element = $this->createElement($node, $component);

			$parser->replaceNode($node, $element);
		}

		$this->htmlContent = $parser->domToHTML();
	}

	public function createElement ($node, $componentClass): string
	{
		$attr = DOMParser::getNodeAttributes($node, 'g:');

		$attributes = [];

		array_walk($attr->attribute, function ($value, $key) use (&$attributes)
		{
			$attributes[$key] = $value;
		});

		$value = DOMParser::getNodeInnerText($node);
		$custom = $attr->custom ?? [];

		// The arguments for the component's method
		$args = [ 'value' => $value, ...$custom, ...$attributes ];

		ob_start();
		call_user_func([$componentClass, 'render'], $args);
		return ob_get_clean();
	}

	public function getHtml (): string
	{
		return $this->htmlContent;
	}
}

?>