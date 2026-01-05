
<main-pabilsag-component-header/>

<p>This comes from a View!</p>
<p>Current date: <?= date('d/m/Y') ?></p>

<button-icon
	id="bicon-main"
	g:icon="https://cdn-icons-png.flaticon.com/512/726/726165.png"
	class="b b-primary">
		Click me!
</button-icon>

<style>
	
	#bicon-main {
		display: flex
	}
	
</style>

<script>
	
	window['bicon-main'].addEventListener('click', () => alert('This is a button'));
	
</script>
