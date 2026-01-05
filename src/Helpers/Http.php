<?php 

namespace Pabilsag\Helpers\Http;

function get_request_headers (): array
{
	$headers = [];
	
	foreach(array_keys($_SERVER) as $head)
	{
		if(str_starts_with($head, 'HTTP_'))
		{
			$headers[str_replace('_', '-', $head)] = $_SERVER[$head];
		}
	}
	
	return $headers;
}

?>