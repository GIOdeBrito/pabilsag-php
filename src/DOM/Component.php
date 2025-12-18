<?php

namespace GioPHP\DOM;

use GioPHP\Interfaces\ComponentInterface;

class Component implements ComponentInterface
{
	private string $tag;
	private mixed $template;
	private array $params;

	public function __construct (string $tag, mixed $template, array $params = NULL)
	{
		$this->tag = $tag;
		$this->template = $template;
		$this->params = $params ?? [];
	}

	public function render (array $attrs = []): void
	{
		// Get the intersecting attributes to turn into single variables
		$difference = array_intersect($this->params, array_keys($attrs));
		// Get the attributes which were not assigned
		$differenceNonAssigned = array_diff($this->params, $difference);

		$templateVars = [];

		foreach($difference as $key)
		{
			$templateVars[$key] = $attrs[$key] ?? NULL;
			unset($attrs[$key]);
		}

		// Merge the template variables with the missing ones specified in the params
		$templateVars = array_merge(array_fill_keys($differenceNonAssigned, NULL), $templateVars);
		// Attach the generic attribute chain string to the array
		$templateVars['attributes'] = $this->getAttributesAsPropertyString($attrs);

		// Extract them as scope variables
		extract($templateVars);

		if(gettype($this->template) === 'string')
		{
			include $this->template;
		}
	}

	public function getTagName (): string
	{
		return $this->tag;
	}

	private function getAttributesAsPropertyString (array $kvp = []): string
	{
		$properties = [];

		array_walk($kvp, function ($value, $key) use (&$properties)
		{
			array_push($properties, "{$key}=\"{$value}\"");
		});

		return implode(' ', $properties);
	}
}

?>