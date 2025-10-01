<?php

namespace GioPHP\Web;

use function GioPHP\Helpers\jsonValidate;

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
		if(!function_exists('json_validate'))
		{
			return jsonValidate($this->rawResponse);
		}

		return json_validate($this->rawResponse);
	}
}

?>