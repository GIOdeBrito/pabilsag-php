<?php

namespace GioPHP\Services;

/* Dependency injection container */

final class DIContainer
{
	private array $singleton = [];
	private array $binds = [];

	public function singleton (string $abstract, callable $factory): void
	{
		$this->singleton[$abstract] = $factory;
	}

	public function bind (string $abstract, callable $factory): void
	{
		$this->binds[$abstract] = $factory;
	}

	public function make (string $class): mixed
	{
		// Singleton dependency
		if(isset($this->singleton[$class]))
		{
			// Returns instance
			if(!is_callable($this->singleton[$class]))
			{
				return $this->singleton[$class];
			}

			// Creates instance and returns
			$this->singleton[$class] = ($this->singleton[$class])($this);
			return $this->singleton[$class];
		}

		//var_dump($this->binds);

		// Transient dependency
		if(isset($this->binds[$class]))
		{
			return ($this->binds[$class])($this);
		}

		if(!class_exists($class))
		{
		    throw new Exception("DIContainer: class '{$class}' does not exist.");
		}

		$reflection = new \ReflectionClass($class);
		$constructor = $reflection->getConstructor();

		// Returns new instance if class has no dependencies
		if(is_null($constructor))
		{
			return new $reflection->newInstance();
		}

		$args = [];

		foreach($constructor->getParameters() as $parameter):

		    $paramInfo = $this->parameterDissect($parameter);

		    // Allows values for optinal params
		    if(!is_null($paramInfo->defaultValue))
		    {
		        $args[] = $paramInfo->defaultValue;
		    }

		    if($paramInfo->isPrimitive)
		    {
		        continue;
		    }

		    $name = $paramInfo->typeName;

		    if(isset($this->binds[$name]) || isset($this->singleton[$name]))
		    {
		        $args[] = $this->make($name);
		    }

		endforeach;

        return $reflection->newInstanceArgs($args);
	}

	private function parameterDissect (\ReflectionParameter $param): object
	{
	    $obj = [
	        'varName' => $param->getName(),
	        'typeName' => $param->getType()->getName(),
	        'type' => $param->getType(),
	        'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
	        'isPrimitive' => $param->getType()->isBuiltIn()
	    ];

	    return (object) $obj;
	}
}

?>