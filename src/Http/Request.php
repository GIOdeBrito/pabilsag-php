<?php 

namespace Pabilsag\Http;

class Request
{
	private string $method;
	private string $uri;
	private array $query = [];
	private array $postdata = [];
	private array $headers = [];
	private string $body;
	private object $parsedBody;
	
	public function __construct ($server, $get, $postdata, $cookies, $body)
	{
		$this->method = mb_strtoupper($_SERVER["REQUEST_METHOD"]);
		$this->uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
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
	
	public function getHeaders (): array
	{
		return $this->headers;
	}
	
	public function getBody (): string
	{
		return $this->body ?? '';
	}
	
	public function getParsedBody (): object
	{
		return $this->parsedBody;
	}
	
	public function getQuery (): object
	{
		return (object) $this->query;
	}
	
	public function getForm (): object
	{
		return (object) $this->postdata;
	}
	
	public function setParsedBody (object $body): void
	{
		$this->parsedBody = $body;
	}
}

?>