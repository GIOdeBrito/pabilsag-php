<?php

use GioPHP\DOM\Component;

return new Component(
	tag: 'button-icon',
	template: <<<HTML
		<button {{@ id }}>
			{{@attributes}}
		</button>
	HTML,
	params: ['id', 'icon', 'value']
);

?>