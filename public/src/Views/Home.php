
<?php require ABSPATH.'/src/Partials/nav.php' ?>

<h1>Welcome</h1>

<button id="b-main" name="button">Click Me</button>

<p>This comes from a View!</p>
<p>Current date: <?= date('d/m/Y') ?></p>

<style>

	#bicon-main {
		display: flex
	}

</style>

<script>

	window['b-main'].addEventListener('click', () => alert('This is a button'));

</script>
