<p>
	<a href="<?= $folder->getShareableLink() ?>">View folder contents</a>
</p>
<form action="<?= $upload_url ?>" method="POST" enctype="multipart/form-data">
	<label>
		<input type="file" name="file" value="">
	</label>
	<input type="submit" value="Upload file">
</form>