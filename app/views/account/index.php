<?php if(!!$account->folders): ?>
<h3>Folders</h3>
<ul>
<?php foreach($account->folders as $_folder): ?>
<li><a href="<?= router()->generate('folder', ['urlname' => $_folder->urlname]); ?>"><?=$_folder->name ?></a></li>
<?php endforeach ?>
</ul>
<?php endif ?>

<form action="<?= $folders_url ?>" method="POST">
	<label>
		<input type="text" name="name" value="" placeholder="Folder name">
	</label>
	<input type="submit" value="Create folder">
</form>