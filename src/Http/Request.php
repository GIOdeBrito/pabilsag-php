<?php

namespace GioPHP\Http;

use GioPHP\Services\Logger;
use GioPHP\Http\FileData;
use function GioPHP\Helpers\{
	convertToType,
	getSchemaMethod,
	getSchemaTypes
};

class Request
{
	private string $method = "";
	private string $path = "";

	private array $form = [];
	private array $body = [];
	private array $query = [];
	private array $files = [];

	private Logger $logger;

	public function __construct (Logger $logger)
	{
		$this->method = mb_strtoupper($_SERVER["REQUEST_METHOD"]);
		$this->path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		$this->logger = $logger;
	}

	public function __get (string $name): mixed
	{
		if(!property_exists($this, $name))
		{
			$this->logger->error("Property '{$name}' does not have a getter function or does not exist.");
			return NULL;
		}

		return $this->{$name}();
	}

	private function method (): string
	{
		return strtoupper($this->method);
	}

	private function path (): string
	{
		return $this->path;
	}

	private function form (): object
	{
		return (object) $this->form;
	}

	private function body (): object
	{
		return (object) $this->body;
	}

	private function query (): object
	{
		return (object) $this->query;
	}

	private function files (): object
	{
		return (object) $this->files;
	}

	public function getSchema (array $schema = []): void
	{
		foreach($schema as $key => $value):

			$name = $key;
			$entry = getSchemaMethod($value);
			$type = mb_strtolower(explode(':', $value)[1], 'UTF-8');

			switch($entry)
			{
				case 'form':
					$this->getFormParam($key, $type);
					break;

				case 'json':
					$this->getJsonParam($key, $type);
					break;

				case 'query':
					$this->getQueryParam($key, $type);
					break;

				case 'file':
					$this->getFileParam($key, $type);
					break;

				default:
					break;
			}

		endforeach;
	}

	private function getFormParam (string $key, string $type): void
	{
		$value = NULL;

		if(isset($_POST[$key]))
		{
			$value = convertToType($_POST[$key], $type);
		}

		$this->form[$key] = $value;
	}

	private function getJsonParam (string $key, string $type): void
	{
		$json = json_decode(file_get_contents('php://input') ?? '', true);
		$value = NULL;

		if(isset($json[$key]))
		{
			$value = convertToType($json[$key], $type);
		}

		$this->body[$key] = $value;
	}

	private function getQueryParam (string $key, string $type): void
	{
		$value = NULL;

		if(isset($_GET[$key]))
		{
			$value = convertToType($_GET[$key], $type);
		}

		$this->query[$key] = $value;
	}

	private function getFileParam (string $key, string $type): void
	{
		$this->files[$key] = NULL;

		if(!isset($_FILES[$key]))
		{
			return;
		}

		$filedata = $_FILES[$key];

		if(!isset($filedata['name']))
		{
			return;
		}

		// Checks if route expects multiple files
		$isMultipleFileType = str_contains($type, '[]');

		// Checks if multiple files are being sent at once
		$isMultiForm = is_array($filedata['name']) ? true : false;

		if($isMultipleFileType !== $isMultiForm)
		{
			return;
		}

		// Get the file types that are allowed
		$allowedTypes = getSchemaTypes($type);

		if(!$isMultipleFileType)
		{
			$name = $filedata['name'][$i];
			$fullpath = $filedata['full_path'][$i];
			$type = $filedata['type'][$i];
			$tempname = $filedata['tmp_name'][$i];
			$error = $filedata['error'][$i];
			$size = $filedata['size'][$i];

			$fileItem = new FileData(
				$name,
				$fullpath,
				$type,
				$tempname,
				$error,
				$size
			);

			if(!in_array($fileItem->extension(), $allowedTypes))
			{
				return;
			}

			$this->files[$key] = $fileItem;
		}

		for($i = 0; $i < count($filedata['name']); $i++):

			$name = $filedata['name'][$i];
			$fullpath = $filedata['full_path'][$i];
			$type = $filedata['type'][$i];
			$tempname = $filedata['tmp_name'][$i];
			$error = $filedata['error'][$i];
			$size = $filedata['size'][$i];

			$fileItem = new FileData(
				$name,
				$fullpath,
				$type,
				$tempname,
				$error,
				$size
			);

			if(!in_array($fileItem->extension(), $allowedTypes))
			{
				continue;
			}

			$this->files[$key][$i] = $fileItem;

		endfor;
	}

	private function getSingleFileParam (): void
	{
		$name = $filedata['name'];
		$fullpath = $filedata['full_path'];
		$type = $filedata['type'];
		$tempname = $filedata['tmp_name'];
		$error = $filedata['error'];
		$size = $filedata['size'];

		$this->files[$key] = new FileData(
			$name,
			$fullpath,
			$type,
			$tempname,
			$error,
			$size
		);
	}
}

?>