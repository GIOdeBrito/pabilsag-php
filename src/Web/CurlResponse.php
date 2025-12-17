<?php

namespace GioPHP\Web;

use function GioPHP\Helpers\Json\json_validator;

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
		return json_decode($this->rawResponse);
	}

	public function isJson (): bool
	{
		// If php 8.3's json_validate does not exist
		// then use the polyfill function instead
		if(!function_exists('json_validate'))
		{
			return json_validator($this->rawResponse);
		}

		return json_validate($this->rawResponse);
	}
}

?>