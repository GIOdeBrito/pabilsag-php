<?php

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
		// Singleton dependency
		if(isset($this->singleton[$class]))
		{
			// Returns instance
			if(is_object($this->singleton[$class]))
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

		$reflection = new \ReflectionClass($class);
		$constructor = $reflection->getConstructor();

		// Returns new instance class has no dependencies
		if(is_null($constructor))
		{
			return new $reflection->newInstance();
		}


	}
}








<?php

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
			if(is_object($this->singleton[$class]))
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

		$reflection = new \ReflectionClass($class);
		$constructor = $reflection->getConstructor();

		// Returns new instance class has no dependencies
		if(is_null($constructor))
		{
			return new $reflection->newInstance();
		}

	    $args = [];

	    return false;
	}
}

class MockService
{
    public function __construct ()
    {
        echo "Mock service instantiated";
    }
}

class Router
{
    public function __construct (MockService $serv)
    {

    }
}

$container = new DIContainer();
$container->singleton(MockService::class, fn() => new MockService());

$container->make(Router::class);












?>