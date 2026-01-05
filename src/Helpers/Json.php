<?php 

namespace Pabilsag\Helpers\Json;

function json_http_response (int $status, array|object $data = []): void
{
	http_response_code($status);
	echo json_encode($data, JSON);
}

?>