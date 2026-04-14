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

    public function __construct (?string $url = null)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        if($url !== null)
		{
            $this->baseUrl = $url;
            curl_setopt($this->curl, CURLOPT_URL, $url);
        }
    }

    public function get (): self
    {
        $this->method = HttpMethod::GET;
        return $this;
    }

    public function post (): self
    {
        $this->method = HttpMethod::POST;
        curl_setopt($this->curl, CURLOPT_POST, true);
        return $this;
    }

    public function url (string $url): self
    {
        $this->baseUrl = $url;
        curl_setopt($this->curl, CURLOPT_URL, $url);
        return $this;
    }

    public function setQuery (array $params): self
    {
        $this->queryParams = $params;
        return $this;
    }

    public function json (array|object $data): self
    {
        $jsonData = json_encode($data);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        return $this;
    }

    public function basicAuth (string $user, string $pwd): self
    {
        curl_setopt($this->curl, CURLOPT_USERPWD, "$user:$pwd");
        return $this;
    }

    public function send (): mixed
    {
        if($this->method === HttpMethod::GET && !empty($this->queryParams))
		{
            $fullUrl = $this->baseUrl . '?' . http_build_query($this->queryParams);
            $this->url($fullUrl);
        }

        $responseBody = curl_exec($this->curl);

        if(curl_errno($this->curl))
		{
            $error = curl_error($this->curl);
            curl_close($this->curl);
            throw new \Exception("CurlClient error: $error");
        }

        $response = new CurlResponse($responseBody);
        curl_close($this->curl);

        return $response->isJson()
            ? $response->getAsJson()
            : $response->getData();
    }
}

