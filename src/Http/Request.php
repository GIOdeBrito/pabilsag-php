<?php 

namespace GioPHP\Http;

class Request
{
	private string $method;
	private string $uri;
	private array $query = [];
	private array $postdata = [];
	private array $headers = [];
	private string $body;
	
	public function __construct ($server, $get, $postdata, $cookies, $body)
	{
		$this->method = mb_strtoupper($server["REQUEST_METHOD"]);
		$this->uri = parse_url($server["REQUEST_URI"], PHP_URL_PATH);
		$this->query = $get;
		$this->postdata = $postdata;
		$this->headers = $server;
		$this->body = $body;
	}
	
	public function getMethod (): string
	{
		return $this->method;
	}
	
	public function getUri (): string
	{
		return $this->uri;
	}
	
	public function getBody (): object
	{
		return json_decode($this->body);
	}
	
	public function getBodyAsString (): string
	{
		return $this->body;
	}
	
	public function getQuery (): object
	{
		return (object) $this->query;
	}
	
	public function getForm (): object
	{
		return (object) $this->postdata;
	}
}

?>