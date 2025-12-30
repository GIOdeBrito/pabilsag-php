<?php

namespace GioPHP\DOM;

use GioPHP\Interfaces\ComponentInterface;

use function GioPHP\Helpers\String\{ normalize_whitespace, remove_linebreaks };
use function GioPHP\Helpers\Polyfill\garray_find;

class Component implements ComponentInterface
{
	private string $tag;
	private string $template;
	private array $params;

	public function __construct (string $tag, string $template, array $params = [])
	{
		$this->tag = $tag;
		$this->template = $template;
		$this->params = $params;
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
		
		// Checks if the string is a path that points to a file on disk
		if(file_exists($this->template))
		{
			// Extract them as scope variables
			extract($templateVars);
			
			include $this->template;
			
			return;
		}
		
		$fHTML = $this->formatHTMLDocString($this->template, $templateVars);
			
		// If not then it must be a pure HTML string
		echo $fHTML;
	}

	public function getTagName (): string
	{
		return $this->tag;
	}
	
	public function getTemplatePath (): string
	{
		return $this->template;
	}

	private function getAttributesAsPropertyString (array $kvp = []): string
	{
		$properties = [];

		array_walk($kvp, function ($value, $key) use (&$properties)
		{
			$keyValuePair = sprintf("\"%s\"=\"%s\"", $key, $value);
			array_push($properties, $keyValuePair);
		});

		return implode(' ', $properties);
	}
	
	private function formatHTMLDocString (string $content, array $params = []): string
	{
		$parsedString = $content;
		
		$offset_search = -1;
		
		for(;;):
			
			$offset_search = strpos($parsedString, '{{', $offset_search + 1);
			
			// Halts the loop if no parameter slot is found 
			if($offset_search === false)
			{
				break;
			}
			
			$offset_end = strpos($parsedString, '}}', $offset_search);
			
			$matchedParam = substr($parsedString, $offset_search, $offset_end - $offset_search + 2);
			$normalizedMatchParam = normalize_whitespace($matchedParam);
			
			// NOTE: Using a polyfill now, as the current
			// environment does not support PHP 8.4's
			// array_find().
			$paramValue = garray_find($params, fn($value, $key) => "{{@{$key}}}" === $normalizedMatchParam);
			
			$paramReplacementValue = '';
			
			if(!is_null($paramValue))
			{
				$paramReplacementValue = $paramValue;
			}
			
			$parsedString = str_replace($matchedParam, $paramReplacementValue, $parsedString);
			
		endfor;
		
		// Removes possible carriage returns and line breaks
		$parsedString = remove_linebreaks($parsedString);
		
		return $parsedString;
	}
}

?>