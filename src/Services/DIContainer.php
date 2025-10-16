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

	public function make (string $class): object
	{
		$dependency = $this->getBindOrSingletonDependency($class);

		if(!is_null($dependency))
		{
			return $dependency;
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
				continue;
		    }

			// Ignore primitive types i.e. int, string...
		    if($paramInfo->isPrimitive)
		    {
		        continue;
		    }

			$paramTypeName = $paramInfo->typeName;

		    if(isset($this->binds[$paramTypeName]) || isset($this->singleton[$paramTypeName]))
		    {
		        $args[] = $this->make($paramTypeName);
		    }

		endforeach;

        return $reflection->newInstanceArgs($args);
	}

	private function getBindOrSingletonDependency (string $class): object|null
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

		// Transient dependency
		if(isset($this->binds[$class]))
		{
			return ($this->binds[$class])($this);
		}

		return NULL;
	}

	private function parameterDissect (\ReflectionParameter $param): object
	{
	    $obj = [
	        'varName' => $param->getName(),
	        'typeName' => $param->getType()->getName(),
	        'type' => $param->getType(),
			'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL,
	        'isPrimitive' => $param->getType()->isBuiltIn()
	    ];

	    return (object) $obj;
	}
}

?>