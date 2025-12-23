<?php

use GioPHP\DOM\Component;

// Example use of heredoc
return new Component(
	tag: 'button-icon',
	template: <<<HTML
	
		<button id="{{ @id }}" {{ @attributes }}>
			<img width="14px" src="{{ @icon }}" alt="Icon">
			{{ @value }}
		</button>
		
	HTML,
	params: ['id', 'icon', 'value']
);

?>