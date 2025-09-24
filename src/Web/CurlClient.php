<?php

namespace GioPHP\Web;

use GioPHP\Enums\HttpMethod;

final class CurlClient
{
	private object $curl;
	private string $method = '';

	public function __construct (string $url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$this->curl = $curl;
	}

	public function get (): CurlClient
	{
		return $this;
	}

	public function post (): CurlClient
	{
		curl_setopt($this->curl, CURLOPT_POST, true);
		$this->method = HttpMethod::POST;
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
		$response = curl_exec($this->curl);

		if(curl_errno($this->curl))
		{
			throw new \Exception(implode(' ', [ "GioPHP CurlClient error:", curl_error($curl) ]));
        }

		curl_close($this->curl);

		return $response;
	}
}

?>