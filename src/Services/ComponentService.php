<?php

namespace Pabilsag\Services;

use Pabilsag\Services\Logger;
use Pabilsag\Interfaces\ComponentInterface;

class ComponentService
{
	private bool $useComponents = false;
	private array $registeredComponents = [];
	private Logger $logger;

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
	
	#[\Deprecated(reason: "Use import() instead")]
	public function register (string $tagName, string|array|object $callback): void
	{
		// Checks if the tag already exists or if the function is callable
		if(array_key_exists($tagName, $this->registeredComponents) || !is_callable($callback))
        {
			return;
        }

		$this->registeredComponents[$tagName] = $callback;
	}

	public function import (ComponentInterface $component): void
	{
		$tagName = $component->getTagName();
		
		if(is_null($tagName))
		{
			$this->logger->error("Component is missing a proper tag defintion: {$component->getTemplatePath()}");
			throw new \Exception("Component's tag name cannot be empty!");
		}

		$this->registeredComponents[$tagName] = $component;
	}

	public function getComponents (): array
	{
		return $this->registeredComponents;
	}
}

?>