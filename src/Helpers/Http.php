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

function get_ip_addr (): string
{
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
	
	return $_SERVER['REMOTE_ADDR'];
}

function is_ip_valid (string $ip_addr): bool
{
	if(!filter_var($ip_addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6))
	{
		return false;
    }
	
	return true;
}

?>