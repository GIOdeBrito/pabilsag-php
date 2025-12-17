<?php 

namespace GioPHP\Helpers\Json;

function json_http_response (int $status, array|object $data = []): void
{
	http_response_code($status);
	echo json_encode($data, JSON);
}

// Sort of polyfill for PHP's 8.3 'json_validate' function
function json_validator (string $data): bool
{
	json_decode($data);

	if(json_last_error() === 0)
	{
		return true;
	}

	return false;
}

?>