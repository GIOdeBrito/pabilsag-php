<?php

use GioPHP\DOM\Component;

// Example use of heredoc
return new Component(
	tag: 'button-icon',
	template: <<<HTML
		<button id="{{ @id }}" {{ @attributes }}>
			<img src="{{ @icon }}" alt="Icon">
			{{ @value }}
		</button>
	HTML,
	params: ['id', 'icon', 'value']
);

?>