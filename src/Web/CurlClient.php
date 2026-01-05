<?php

namespace Pabilsag\Web;

use Pabilsag\Enums\HttpMethod;
use Pabilsag\Web\CurlResponse;

final class CurlClient
{
	private object $curl;
	private string $method = '';
	private string $baseUrl = '';

	private array $queryParams = [];

	public function __construct (?string $url = NULL)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$this->curl = $curl;
		$this->baseUrl = $url ?? '';
	}

	public function get (): CurlClient
	{
		$this->method = HttpMethod::GET;
		return $this;
	}

	public function post (): CurlClient
	{
		$this->method = HttpMethod::POST;
		curl_setopt($this->curl, CURLOPT_POST, true);
		return $this;
	}

	public function url (string $url): CurlClient
	{
		$this->baseUrl = $url;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		return $this;
	}

	public function setQuery (array $params): CurlClient
	{
		$this->queryParams = $params;
		return $this;
	}

	public function json (array|object $data): CurlClient
	{
		$jsonData = json_encode($data);

		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
			implode(' ', [ "Content-Length:", strlen($jsonData) ])
        ]);

		return $this;
	}

	public function basicAuth (string $user, string $pwd): CurlClient
	{
		curl_setopt($this->curl, CURLOPT_USERPWD, implode(':', [ $user, $pwd ]));
		return $this;
	}

	public function send (): mixed
	{
		if($this->method === HttpMethod::GET && !empty($this->queryParams))
		{
			$fullurl = $this->baseUrl.'?'.http_build_query($this->queryParams);
			$this->url($fullurl);
		}

		$response = new CurlResponse(curl_exec($this->curl));

		if(curl_errno($this->curl))
		{
			throw new \Exception(implode(' ', [ "Pabilsag/CurlClient error:", curl_error($curl) ]));
        }

		curl_close($this->curl);

		if($response->isJson())
		{
			return $response->getAsJson();
		}

		return $response->getData();
	}
}

?>