<?php

use GioPHP\DOM\Component;

return new Component(
	tag: 'button-icon',
	template: <<<HTML
		<button {{@ id }} {{ @attributes }}>
			<img src="{{ @icon }}" alt="Icon">
			{{ @value }}
		</button>
	HTML,
	params: ['id', 'icon', 'value']
);

?>