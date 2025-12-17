<?php 

// Auth view

?>

<div>
	<form action="/public/api/v1/auth" method="POST">
		<label for="">Username</label>
		<input type="text" name="usr" value="">
		
		<label for="">Secret</label>
		<input type="password" name="pwd" value="">
		
		<input type="submit" value="Send">
	</form>
</div>