<?php

/* View renderer */

namespace Pabilsag\View;

use Pabilsag\DOM\DOMParser;

class ViewRenderer
{
	private string $htmlContent = '';
	private object $dom;

	public function beginCapture (): void
	{
		ob_start();
	}

	public function endCapture (): void
	{
		$this->htmlContent = ob_get_clean();
	}

	private function parseHTML (): void
	{
		$parser = new DOMParser($this->htmlContent);

		$this->htmlContent = $parser->domToHTML();
	}

	public function getHtml (): string
	{
		return $this->htmlContent;
	}
}

?>