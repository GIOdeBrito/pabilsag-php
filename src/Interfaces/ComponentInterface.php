<?php 

namespace GioPHP\Interfaces;

interface ComponentInterface 
{
	public function __construct (string $tag, mixed $template, array $params = NULL);
	
	public function render (array $attrs = []): void;
}

?>