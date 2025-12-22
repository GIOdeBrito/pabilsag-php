<?php

use GioPHP\DOM\Component;

// Example use of heredoc
return new Component(
	tag: 'main-gphp-component-header',
	template: <<<HTML
		<div>
			<header>
				<h1>Welcome!</h1>
				<p>This is a GioPHP component example, consult the code for more information.</p>
			</header>
		</div>
	HTML
);

?>