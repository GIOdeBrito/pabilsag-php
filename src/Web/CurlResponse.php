<?php

namespace GioPHP\Web;

final class CurlResponse
{
	private mixed $rawResponse = NULL;

	public function __construct (mixed $response)
	{
		$this->rawResponse = $response;
	}

	public function getData (): mixed
	{
		return $this->rawResponse;
	}

	public function getAsJson (): object
	{
		return json_decode($this->$rawResponse);
	}

	public function isJson (): bool
	{
		return json_validate($this->rawResponse) ? true : false;
	}
}

?>