<?php 

namespace GioPHP\Interfaces;

interface ComponentInterface 
{
	public function __construct (string $tag, string $template, array $params = []);
	
	public function render (array $attrs = []): void;
	public function getTagName (): string;
	public function getTemplatePath (): string;
}

?>