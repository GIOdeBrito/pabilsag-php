<?php 

// Auth view

?>

<?php require ABSPATH.'/src/Partials/nav.php' ?>

<fieldset>
	<legend>Form POST</legend>
	
	<form action="/public/api/v1/auth" method="POST">
		<label for="">Username</label>
		<input type="text" name="usr" value="" required>
		
		<label for="">Secret</label>
		<input type="password" name="pwd" value="" required>
		
		<input type="submit" value="Send">
	</form>
</fieldset>

<br>
	
<fieldset>
	<legend>JSON POST</legend>
	
	<form method="POST" data-name="json-form">
		<label for="">Username</label>
		<input type="text" name="usr" value="" required>
		
		<label for="">Secret</label>
		<input type="password" name="pwd" value="" required>
		
		<input type="submit" value="Send">
	</form>
</fieldset>

<script>
	
	window.addEventListener('load', () =>
	{
		const submit = document.querySelector('form[data-name="json-form"] > input[type="submit"]');
		
		const username = document.querySelector('form[data-name="json-form"] > input[type="text"]');
		const password = document.querySelector('form[data-name="json-form"] > input[type="password"]');
		
		submit.addEventListener('click', async (ev) =>
		{
			ev.preventDefault();
			
			const obj = {
				user: username.value,
				pwd: password.value
			};
			
			const response = await fetch('/public/api/v1/auth', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(obj)
			});
			
			console.log(response);
		});
	});
	
</script>
