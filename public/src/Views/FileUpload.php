<h1>File Uploader</h1>

<p>Upload and send multiple files here.</p>

<form method="POST" action="/public/api/v1/fileschema" enctype="multipart/form-data">
	<input type="file" multiple name="annex[]">
	<input type="submit" value="Send">
</form>

<br>

<p>Upload a single file here.</p>

<form method="POST" action="/public/api/v1/fileschema" enctype="multipart/form-data">
	<input type="file" ame="annex">
	<input type="submit" value="Send">
</form>