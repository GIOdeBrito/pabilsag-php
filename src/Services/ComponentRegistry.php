<?php

namespace GioPHP\Services;

use GioPHP\Services\Logger;

class ComponentRegistry
{
	private bool $useComponents = false;
	private array $registeredComponents = [];
	private ?Logger $logger = NULL;

	public function __construct (Logger $logger)
	{
		$this->logger = $logger;
	}

	public function useComponents (bool $value): void
	{
		$this->useComponents = $value;
	}

	public function isUsingComponents (): bool
	{
		return $this->useComponents;
	}

	/*public function register (string $tagName, string|array|object $callback): void
	{
		// Checks if the tag already exists or if the function is callable
		if(array_key_exists($tagName, $this->registeredComponents) || !is_callable($callback))
        {
			return;
        }

		$this->registeredComponents[$tagName] = $callback;
	}*/

	public function import (object $component): void
	{
		$tagName = $component?->getTagName() ?? NULL;

		$this->registeredComponents[$tagName] = $component;
	}

	public function getComponents (): array
	{
		return $this->registeredComponents;
	}
}

?>