<?php

namespace GioPHP\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
		public string $method = '',
		public string $path = '',
		public array $middlewares = [],
		public string $description = '',
		public bool $isFallbackRoute = false,
		public bool $isStatic = false,

		// DO NOT MANUALLY SET THESE ATTRIBUTES
		public string $functionName = '',
    ) {}
}

?>